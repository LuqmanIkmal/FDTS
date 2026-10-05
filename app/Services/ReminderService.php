<?php

namespace App\Services;

use App\Models\FixedDeposit;
use App\Models\Staff;
use Illuminate\Support\Facades\Log;

/**
 * Email reminders sent to the staff member who created each FD
 * (replaces FDReminderScheduler and the reminder emails in SubmitFDServlet):
 *  1. FDs maturing within 7 days
 *  2. Incomplete FD records (missing certificate number or file), 1 day after creation
 */
class ReminderService
{
    private const LINE = '==================================================';

    public function run(): void
    {
        Log::info('Running FD reminder task');

        $sentMaturity = 0;
        $sentIncomplete = 0;

        foreach ($this->fdsWithCreator() as $fd) {
            try {
                if ($this->dueForMaturityReminder($fd)) {
                    $sentMaturity += $this->sendToCreator($fd, 'Reminder: FD Maturing in 7 Days - '.$fd->acc_number,
                        fn (Staff $staff) => $this->maturityBody($fd, $staff)) ? 1 : 0;
                }

                if ($this->dueForIncompleteReminder($fd)) {
                    $sentIncomplete += $this->sendToCreator($fd, 'Reminder: Incomplete FD Record - '.$fd->acc_number,
                        fn (Staff $staff) => $this->incompleteBody($fd, $staff)) ? 1 : 0;
                }
            } catch (\Throwable $e) {
                Log::error("Error processing reminders for FD{$fd->fd_id}: {$e->getMessage()}");
            }
        }

        Log::info("Sent {$sentMaturity} maturity reminders and {$sentIncomplete} incomplete reminders");
    }

    /** FDs that have a CREATE transaction, with the creator's staff ID and creation date. */
    private function fdsWithCreator()
    {
        return FixedDeposit::query()
            ->join('fixed_deposit_transactions as t', 't.fd_id', '=', 'fixed_deposit_records.fd_id')
            ->where('t.transaction_type', 'CREATE')
            ->with('bank')
            ->select('fixed_deposit_records.*', 't.staff_id as creator_staff_id', 't.transaction_date as creation_date')
            ->orderByDesc('fixed_deposit_records.fd_id')
            ->get();
    }

    private function dueForMaturityReminder(FixedDeposit $fd): bool
    {
        if ($fd->status !== 'ONGOING' || $fd->reminder_maturity !== 'Y' || ! $fd->maturity_date) {
            return false;
        }

        $daysUntilMaturity = today()->diffInDays($fd->maturity_date, false);

        return $daysUntilMaturity >= 0 && $daysUntilMaturity <= 7;
    }

    private function dueForIncompleteReminder(FixedDeposit $fd): bool
    {
        if ($fd->reminder_incomplete !== 'Y' || ! $fd->creation_date) {
            return false;
        }

        // Only exactly 1 day after creation
        $created = \Carbon\Carbon::parse($fd->creation_date)->startOfDay();
        if ((int) $created->diffInDays(today(), false) !== 1) {
            return false;
        }

        return self::isIncomplete($fd->cert_no, $fd->fd_cert);
    }

    public static function isIncomplete(?string $certNo, ?string $certFile): bool
    {
        return $certNo === null || trim($certNo) === '' || empty($certFile);
    }

    private function sendToCreator(FixedDeposit $fd, string $subject, callable $body): bool
    {
        $staff = $fd->creator_staff_id ? Staff::find($fd->creator_staff_id) : null;

        if (! $staff || ! $staff->staff_email) {
            Log::warning("No creator email for FD {$fd->acc_number}");

            return false;
        }

        try {
            Mailer::send($staff->staff_email, $subject, $body($staff));

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    // ---------- Email bodies (same wording as the Java version) ----------

    public static function maturityEmail(string $staffName, string $accNumber, ?string $bankName, $depositAmount,
        $interestRate, $tenure, string $startDate, string $maturityDate, $maturityAmount): string
    {
        return "Dear {$staffName},\n\n"
            ."This is a friendly reminder that your Fixed Deposit will mature in 7 days.\n\n"
            .self::LINE."\nFD DETAILS\n".self::LINE."\n"
            ."Account Number: {$accNumber}\n"
            .'Bank: '.($bankName ?? 'null')."\n"
            .'Deposit Amount: RM '.number_format((float) $depositAmount, 2)."\n"
            ."Interest Rate: {$interestRate}%\n"
            ."Tenure: {$tenure} months\n"
            ."Start Date: {$startDate}\n"
            ."Maturity Date: {$maturityDate}\n"
            .'Maturity Amount: RM '.number_format((float) $maturityAmount, 2)."\n"
            .self::LINE."\n\n"
            ."Please take necessary action before the maturity date.\n\n"
            ."Best regards,\nFixed Deposit Tracking System\n";
    }

    public static function incompleteEmail(string $staffName, string $accNumber, ?string $bankName, string $status,
        bool $certNoMissing, bool $certFileMissing): string
    {
        $body = "Dear {$staffName},\n\n"
            ."This is a reminder that your Fixed Deposit record is incomplete.\n\n"
            .self::LINE."\nFD DETAILS\n".self::LINE."\n"
            ."Account Number: {$accNumber}\n"
            .'Bank: '.($bankName ?? 'null')."\n"
            ."Status: {$status}\n"
            .self::LINE."\n\n"
            ."MISSING INFORMATION:\n";

        if ($certNoMissing) {
            $body .= "- Certificate Number\n";
        }
        if ($certFileMissing) {
            $body .= "- Certificate File/Document\n";
        }

        return $body."\nPlease update the FD record with the missing information.\n\n"
            ."Best regards,\nFixed Deposit Tracking System\n";
    }

    private function maturityBody(FixedDeposit $fd, Staff $staff): string
    {
        return self::maturityEmail($staff->staff_name, $fd->acc_number, $fd->bank_name, $fd->deposit_amount,
            $this->plainRate($fd->interest_rate), $fd->tenure, $fd->start_date->toDateString(),
            $fd->maturity_date->toDateString(), $fd->maturity_amount);
    }

    private function incompleteBody(FixedDeposit $fd, Staff $staff): string
    {
        return self::incompleteEmail($staff->staff_name, $fd->acc_number, $fd->bank_name, (string) $fd->status,
            $fd->cert_no === null || trim($fd->cert_no) === '', empty($fd->fd_cert));
    }

    private function plainRate($rate): string
    {
        return rtrim(rtrim((string) $rate, '0'), '.');
    }
}
