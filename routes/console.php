<?php

use App\Services\FixedDepositService;
use App\Services\ReminderService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
 * Background jobs (replace the Java AutoRenewalScheduler and FDReminderScheduler).
 * On the server, run "php artisan schedule:run" every minute from a cron job.
 */

Artisan::command('fd:auto-renew', function (FixedDepositService $service) {
    $count = $service->autoRenewMaturedFds();
    $this->info("Auto-renewal complete: {$count} FD(s) renewed.");
})->purpose('Auto-renew matured Free FDs that have Auto Renewal = Yes');

Artisan::command('fd:send-reminders', function (ReminderService $service) {
    $service->run();
    $this->info('Reminder task complete.');
})->purpose('Email maturity and incomplete-record reminders to FD creators');

// Daily at 00:05, like AutoRenewalScheduler
Schedule::command('fd:auto-renew')->dailyAt('00:05')->withoutOverlapping();

// Every 3 hours, like FDReminderScheduler
Schedule::command('fd:send-reminders')->everyThreeHours()->withoutOverlapping();
