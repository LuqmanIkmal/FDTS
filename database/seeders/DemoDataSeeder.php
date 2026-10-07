<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\FdTransaction;
use App\Models\FixedDeposit;
use App\Models\FreeFd;
use App\Models\PledgeFd;
use App\Models\Staff;
use App\Services\FixedDepositService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sample banks and fixed deposits, so the dashboard, lists and reports have data to show.
 *
 * DatabaseSeeder does not call this. Run it on purpose:
 *     php artisan db:seed --class=DemoDataSeeder
 * Running it again replaces the sample FDs (their dates are relative to the day it runs).
 * Remove them with:
 *     php artisan db:seed --class=RemoveDemoDataSeeder
 *
 * Every sample FD has a referral number starting with DEMO. Reminders are off for all of
 * them, so they never cause a reminder email.
 */
class DemoDataSeeder extends Seeder
{
    public const REFERRAL_PREFIX = 'DEMO';

    /** name => [phone, address, account number prefix]. Addresses match the ones on the application form. */
    private const BANKS = [
        'Maybank' => ['0320001001', 'Cawangan Jalan Gombak, Selangor.', '5140'],
        'CIMB' => ['0320001002', 'Menara CIMB, Jalan Stesen Sentral 2, Kuala Lumpur Sentral, 50470 Kuala Lumpur.', '8003'],
        'Public Bank' => ['0320001003', 'Menara Public Bank, Jalan Raja Chulan, 50200 Kuala Lumpur.', '3188'],
        'RHB Bank' => ['0320001004', 'RHB Centre, Jalan Tun Razak, 50400 Kuala Lumpur.', '2140'],
        'Hong Leong Bank' => ['0320001005', 'Menara Hong Leong, Damansara City, 50490 Kuala Lumpur.', '0510'],
        'AmBank' => ['0320001006', 'Menara AmBank, Jalan Yap Kwan Seng, 50450 Kuala Lumpur.', '8881'],
        'MBSB' => ['0320001007', 'Menara MBSB, Jalan Kia Peng, 50450 Kuala Lumpur.', '1601'],
    ];

    /*
     * Each row: bank, type, deposit, rate %, tenure in months, type settings, then when.
     * Type settings:
     *   Free FD   -> [auto renewal Y/N, withdrawable Full/Partial/No]
     *   Pledge FD -> [collateral Y (full) / N (partial), pledge value (null = whole deposit)]
     */

    /** Running FDs. When = [months, days] from today until maturity; the first six mature within 90 days. */
    private const ONGOING = [
        ['Maybank', 'FREEFD', 250000, '3.55', 6, ['N', 'Full'], [0, 9]],
        ['CIMB', 'PLEDGEFD', 180000, '3.70', 12, ['Y', null], [0, 35]],
        ['Public Bank', 'FREEFD', 120000, '2.85', 3, ['Y', 'No'], [0, 47]],
        ['RHB Bank', 'FREEFD', 300000, '3.80', 12, ['N', 'Partial'], [0, 58]],
        ['Hong Leong Bank', 'PLEDGEFD', 150000, '3.65', 12, ['N', 70000], [0, 76]],
        ['Maybank', 'FREEFD', 200000, '3.45', 6, ['Y', 'Full'], [0, 88]],
        ['AmBank', 'FREEFD', 100000, '3.75', 12, ['N', 'Full'], [3, 18]],
        ['CIMB', 'FREEFD', 350000, '3.90', 12, ['Y', 'Partial'], [4, 6]],
        ['Public Bank', 'PLEDGEFD', 220000, '4.05', 24, ['Y', null], [4, 21]],
        ['Maybank', 'FREEFD', 280000, '3.85', 12, ['N', 'Full'], [5, 11]],
        ['RHB Bank', 'PLEDGEFD', 160000, '3.95', 18, ['N', 60000], [6, 3]],
        ['Hong Leong Bank', 'FREEFD', 240000, '3.80', 12, ['Y', 'No'], [6, 25]],
        ['CIMB', 'FREEFD', 150000, '3.70', 12, ['N', 'Full'], [7, 14]],
        ['Public Bank', 'FREEFD', 320000, '3.90', 12, ['Y', 'Partial'], [8, 8]],
        ['MBSB', 'PLEDGEFD', 200000, '4.10', 24, ['Y', null], [8, 26]],
        ['Maybank', 'PLEDGEFD', 260000, '3.95', 12, ['N', 120000], [9, 17]],
        ['AmBank', 'FREEFD', 180000, '3.85', 12, ['N', 'Full'], [10, 5]],
        ['RHB Bank', 'FREEFD', 210000, '3.90', 12, ['Y', 'Full'], [11, 2]],
        ['Hong Leong Bank', 'FREEFD', 130000, '4.00', 24, ['N', 'No'], [16, 10]],
        ['CIMB', 'PLEDGEFD', 400000, '4.20', 36, ['Y', null], [20, 0]],
    ];

    /** New FDs still waiting for their certificate. When = days since the start date. */
    private const PENDING = [
        ['Maybank', 'FREEFD', 150000, '3.85', 12, ['Y', 'Full'], 3],
        ['AmBank', 'PLEDGEFD', 90000, '3.60', 6, ['N', 40000], 8],
        ['RHB Bank', 'FREEFD', 200000, '4.05', 24, ['N', 'Partial'], 12],
    ];

    /**
     * FDs that have reached maturity. When = months since the start date, then what happened next:
     *   null                      -> nothing yet
     *   ['withdraw', amount|null] -> withdrawn (null = the whole maturity amount)
     *   ['reinvest', tenure, bySystem] -> reinvested as a new running FD (bySystem = auto-renewal)
     */
    private const MATURED = [
        ['Maybank', 'FREEFD', 150000, '3.10', 12, ['N', 'Full'], 40, ['withdraw', null]],
        ['RHB Bank', 'FREEFD', 200000, '3.20', 12, ['N', 'Full'], 38, ['withdraw', null]],
        ['CIMB', 'PLEDGEFD', 200000, '3.60', 24, ['Y', null], 36, ['reinvest', 18, false]],
        ['Hong Leong Bank', 'PLEDGEFD', 250000, '3.55', 24, ['Y', null], 34, ['reinvest', 12, false]],
        ['CIMB', 'FREEFD', 350000, '3.55', 12, ['N', 'Partial'], 31, ['withdraw', 150000]],
        ['Maybank', 'FREEFD', 150000, '3.40', 12, ['N', 'Full'], 29, ['reinvest', 24, false]],
        ['Public Bank', 'FREEFD', 250000, '3.75', 24, ['N', 'Partial'], 27, ['withdraw', 100000]],
        ['AmBank', 'FREEFD', 300000, '3.60', 12, ['N', 'Full'], 26, ['withdraw', null]],
        ['RHB Bank', 'FREEFD', 300000, '3.70', 18, ['N', 'Full'], 22, null],
        ['Public Bank', 'FREEFD', 250000, '3.50', 12, ['Y', 'No'], 20, ['reinvest', 12, true]],
        ['CIMB', 'FREEFD', 300000, '3.85', 12, ['N', 'Full'], 18, null],
        ['Hong Leong Bank', 'PLEDGEFD', 220000, '3.80', 12, ['N', 100000], 16, null],
        ['AmBank', 'FREEFD', 200000, '3.65', 12, ['N', 'Full'], 14, ['withdraw', null]],
        ['MBSB', 'FREEFD', 280000, '3.30', 6, ['N', 'Partial'], 13, ['withdraw', 80000]],
    ];

    private FixedDepositService $service;

    private int $staffId;

    /** @var array<string, int> bank name => bank_id */
    private array $bankIds = [];

    private int $created = 0;

    public function run(FixedDepositService $service): void
    {
        // The sample FDs are recorded as created by the first active staff member
        $staffId = Staff::where('staff_status', 'Active')->orderBy('staff_id')->value('staff_id');

        if (! $staffId) {
            $this->command?->error('No active staff found. Create an account on the Sign Up page first, then run this again.');

            return;
        }

        $this->service = $service;
        $this->staffId = $staffId;

        DB::transaction(function () {
            self::remove();

            foreach (self::BANKS as $name => [$phone, $address]) {
                $this->bankIds[$name] = Bank::firstOrCreate(
                    ['bank_name' => $name],
                    ['bank_phone' => $phone, 'bank_address' => $address],
                )->bank_id;
            }

            $today = today();

            foreach (self::MATURED as [$bank, $type, $deposit, $rate, $tenure, $settings, $monthsAgo, $outcome]) {
                $start = $today->copy()->subMonthsNoOverflow($monthsAgo);
                $fd = $this->createFd($bank, $type, $deposit, $rate, $tenure, $settings, $start, 'MATURED');
                $this->applyOutcome($fd, $outcome);
            }

            foreach (self::ONGOING as [$bank, $type, $deposit, $rate, $tenure, $settings, [$months, $days]]) {
                $start = $today->copy()->addMonthsNoOverflow($months)->addDays($days)->subMonthsNoOverflow($tenure);
                $this->createFd($bank, $type, $deposit, $rate, $tenure, $settings, $start, 'ONGOING');
            }

            foreach (self::PENDING as [$bank, $type, $deposit, $rate, $tenure, $settings, $daysAgo]) {
                $this->createFd($bank, $type, $deposit, $rate, $tenure, $settings, $today->copy()->subDays($daysAgo), 'PENDING');
            }
        });

        $this->command?->info("Added {$this->created} sample fixed deposits across ".count($this->bankIds).' banks.');
    }

    /**
     * Delete the sample FDs (their settings and transactions go with them) and the sample
     * banks that no FD uses any more.
     *
     * @return int number of FDs removed
     */
    public static function remove(): int
    {
        $removed = FixedDeposit::where('referral_number', 'like', self::REFERRAL_PREFIX.'%')->delete();

        foreach (self::BANKS as $name => [$phone]) {
            Bank::where('bank_name', $name)->where('bank_phone', $phone)->whereDoesntHave('fixedDeposits')->delete();
        }

        return $removed;
    }

    private function createFd(string $bank, string $type, int $deposit, string $rate, int $tenure, array $settings,
        Carbon $start, string $status): FixedDeposit
    {
        $maturityAmount = FixedDeposit::maturityAmountFor($deposit, $rate, $tenure);

        $fd = FixedDeposit::create([
            'acc_number' => self::BANKS[$bank][2].$this->digits(8),
            'deposit_amount' => $deposit,
            'interest_rate' => $rate,
            'start_date' => $start->toDateString(),
            'tenure' => $tenure,
            'maturity_date' => $start->copy()->addMonthsNoOverflow($tenure)->toDateString(),
            'fd_type' => $type,
            'status' => $status,
            'bank_id' => $this->bankIds[$bank],
            'reminder_maturity' => 'N',
            'reminder_incomplete' => 'N',
            // A matured FD holds its maturity amount (see FixedDepositController::update)
            'remaining_balance' => $status === 'MATURED' ? $maturityAmount : $deposit,
            'total_withdrawn' => 0,
        ] + $this->labels($status));

        if ($type === 'FREEFD') {
            FreeFd::create(['fd_id' => $fd->fd_id, 'auto_renewal_status' => $settings[0], 'withdrawable_status' => $settings[1]]);
        } else {
            PledgeFd::create(['fd_id' => $fd->fd_id, 'collateral_status' => $settings[0], 'pledge_value' => $settings[1] ?? $deposit]);
        }

        FdTransaction::create([
            'fd_id' => $fd->fd_id,
            'staff_id' => $this->staffId,
            'transaction_type' => 'CREATE',
            'transaction_date' => $start->copy()->setTime(10, 0),
            'calc_total_profit' => $maturityAmount,
        ]);

        return $fd;
    }

    /** Uses the same service calls as the Update page, so balances and transactions match real ones. */
    private function applyOutcome(FixedDeposit $fd, ?array $outcome): void
    {
        if ($outcome === null) {
            return;
        }

        $maturity = $fd->maturity_date->copy();

        if ($outcome[0] === 'withdraw') {
            $amount = (string) ($outcome[1] ?? $fd->remaining_balance);
            $this->service->withdraw($fd, $this->staffId, $maturity->copy()->addDays(3)->setTime(11, 30), $amount, $fd->maturity_amount);

            return;
        }

        [, $tenure, $bySystem] = $outcome;
        $newStart = $maturity->copy()->addDay();
        $renewedOn = $bySystem ? $maturity->copy()->setTime(0, 5) : $newStart->copy()->setTime(9, 45);

        $newFd = $this->service->reinvest($fd, $newStart, $tenure, $newStart->copy()->addMonthsNoOverflow($tenure),
            $renewedOn, $bySystem ? null : $this->staffId);

        // reinvest() leaves these empty and dates the CREATE transaction today
        $newFd->update($this->labels('ONGOING'));
        $newFd->transactions()->where('transaction_type', 'CREATE')->update(['transaction_date' => $renewedOn]);
    }

    /** Referral number (the DEMO marker) and, except for pending FDs, a certificate number. */
    private function labels(string $status): array
    {
        $this->created++;

        return [
            'referral_number' => sprintf('%s%04d', self::REFERRAL_PREFIX, $this->created),
            'cert_no' => $status === 'PENDING' ? null : 'FDC'.$this->digits(7),
        ];
    }

    /** Digits that look random but are the same on every run and never repeat within one. */
    private function digits(int $length): string
    {
        static $n = 0;
        $n++;

        return str_pad((string) (($n * 7919577) % (10 ** $length)), $length, '0', STR_PAD_LEFT);
    }
}
