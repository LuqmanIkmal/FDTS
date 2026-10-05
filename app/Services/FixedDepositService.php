<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\FdTransaction;
use App\Models\FixedDeposit;
use App\Models\FreeFd;
use App\Models\PledgeFd;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FD business logic (replaces the transactional parts of FixedDepositDAO).
 */
class FixedDepositService
{
    /** Find a bank by exact name, creating it when it does not exist. */
    public function getOrCreateBank(string $bankName): int
    {
        return Bank::firstOrCreate(['bank_name' => $bankName])->bank_id;
    }

    /**
     * Check account, referral and certificate numbers for duplicates.
     *
     * @return string|null error message, or null when all are unique
     */
    public function checkForDuplicates(?string $accNumber, ?string $referralNumber, ?string $certNo): ?string
    {
        $errors = [];

        if ($accNumber !== null && FixedDeposit::where('acc_number', $accNumber)->exists()) {
            $errors[] = "Account Number {$accNumber} already exists.";
        }

        if ($referralNumber !== null && trim($referralNumber) !== ''
            && FixedDeposit::where('referral_number', $referralNumber)->exists()) {
            $errors[] = "Referral Number {$referralNumber} already exists.";
        }

        if ($certNo !== null && trim($certNo) !== '' && FixedDeposit::where('cert_no', $certNo)->exists()) {
            $errors[] = "Certificate Number {$certNo} already exists.";
        }

        return $errors ? implode(' ', $errors) : null;
    }

    public function recordCreate(int $fdId, ?int $staffId, ?string $calcTotalProfit): FdTransaction
    {
        return FdTransaction::create([
            'fd_id' => $fdId,
            'staff_id' => $staffId,
            'transaction_type' => 'CREATE',
            'transaction_date' => now(),
            'calc_total_profit' => $calcTotalProfit,
        ]);
    }

    /** Record a withdrawal and reduce the FD's remaining balance. */
    public function withdraw(FixedDeposit $fd, int $staffId, Carbon $transactionDate, string $amount, ?string $calcTotalProfit): void
    {
        DB::transaction(function () use ($fd, $staffId, $transactionDate, $amount, $calcTotalProfit) {
            FdTransaction::create([
                'fd_id' => $fd->fd_id,
                'staff_id' => $staffId,
                'transaction_date' => $transactionDate,
                'transaction_type' => 'WITHDRAW',
                'withdraw_amount' => $amount,
                'calc_total_profit' => $calcTotalProfit,
            ]);

            $locked = FixedDeposit::whereKey($fd->fd_id)->lockForUpdate()->first();
            $locked->update([
                'remaining_balance' => bcsub((string) ($locked->remaining_balance ?? '0'), $amount, 2),
                'total_withdrawn' => bcadd((string) ($locked->total_withdrawn ?? '0'), $amount, 2),
            ]);
        });
    }

    /**
     * Reinvest an FD: the old FD becomes MATURED and a new ONGOING FD is created
     * with the old FD's maturity amount as its principal (linked via previous_fd_id).
     * Used by both manual reinvest and auto-renewal (staffId null = system).
     *
     * @return FixedDeposit the new FD
     */
    public function reinvest(FixedDeposit $oldFd, Carbon $newStartDate, int $newTenure, Carbon $newMaturityDate,
        Carbon $transactionDate, ?int $staffId): FixedDeposit
    {
        return DB::transaction(function () use ($oldFd, $newStartDate, $newTenure, $newMaturityDate, $transactionDate, $staffId) {
            $maturityAmount = $oldFd->maturity_amount;

            // Step 1: old FD -> MATURED
            $oldFd->update(['status' => 'MATURED']);

            // Step 2: REINVEST transaction for the old FD
            FdTransaction::create([
                'fd_id' => $oldFd->fd_id,
                'staff_id' => $staffId,
                'transaction_date' => $transactionDate,
                'transaction_type' => 'REINVEST',
                'calc_total_profit' => $maturityAmount,
            ]);

            // Step 3: new FD linked to the old one
            $newFd = FixedDeposit::create([
                'acc_number' => $oldFd->acc_number,
                'deposit_amount' => $maturityAmount,
                'interest_rate' => $oldFd->interest_rate,
                'start_date' => $newStartDate->toDateString(),
                'tenure' => $newTenure,
                'maturity_date' => $newMaturityDate->toDateString(),
                'fd_type' => $oldFd->fd_type,
                'status' => 'ONGOING',
                'bank_id' => $oldFd->bank_id,
                'previous_fd_id' => $oldFd->fd_id,
                'reminder_maturity' => 'N',
                'reminder_incomplete' => 'N',
                'remaining_balance' => $maturityAmount,
                'total_withdrawn' => 0,
            ]);

            // Step 4: CREATE transaction for the new FD with its expected value
            $this->recordCreate($newFd->fd_id, $staffId,
                FixedDeposit::maturityAmountFor($maturityAmount, $oldFd->interest_rate, $newTenure));

            // Step 5: copy type-specific settings
            if ($oldFd->fd_type === 'FREEFD' && ($free = $oldFd->freeFd)) {
                FreeFd::create([
                    'fd_id' => $newFd->fd_id,
                    'auto_renewal_status' => $free->auto_renewal_status,
                    'withdrawable_status' => $free->withdrawable_status,
                ]);
            } elseif ($oldFd->fd_type === 'PLEDGEFD' && ($pledge = $oldFd->pledgeFd)) {
                PledgeFd::create([
                    'fd_id' => $newFd->fd_id,
                    'collateral_status' => $pledge->collateral_status,
                    'pledge_value' => $pledge->pledge_value,
                ]);
            }

            Log::info("FD{$oldFd->fd_id} reinvested as FD{$newFd->fd_id} (principal RM {$maturityAmount})");

            return $newFd;
        });
    }

    /**
     * Auto-renew matured Free FDs that have Auto Renewal = Y.
     * Runs daily from the scheduler.
     *
     * @return int number of FDs renewed
     */
    public function autoRenewMaturedFds(): int
    {
        $fds = FixedDeposit::query()
            ->join('free_fds', 'free_fds.fd_id', '=', 'fixed_deposit_records.fd_id')
            ->where('fixed_deposit_records.fd_type', 'FREEFD')
            ->where('fixed_deposit_records.status', 'ONGOING')
            ->whereDate('fixed_deposit_records.maturity_date', '<=', today())
            ->where('free_fds.auto_renewal_status', 'Y')
            ->select('fixed_deposit_records.*')
            ->get();

        $renewed = 0;

        foreach ($fds as $fd) {
            try {
                $newStart = today()->addDay();
                $newMaturity = $newStart->copy()->addMonthsNoOverflow((int) $fd->tenure);
                $newFd = $this->reinvest($fd, $newStart, (int) $fd->tenure, $newMaturity, now(), null);
                $renewed++;
                Log::info("AUTO-RENEWED: FD{$fd->fd_id} -> FD{$newFd->fd_id}");
            } catch (\Throwable $e) {
                Log::error("AUTO-RENEWAL FAILED for FD{$fd->fd_id}: {$e->getMessage()}");
            }
        }

        return $renewed;
    }
}
