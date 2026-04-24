<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('subs:sync')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/subs-sync.log'));

// Daily at 02:00 — terminate past_due subs whose 7-day grace period has expired.
// Was previously defined in app/Console/Kernel.php, which Laravel 12 does NOT load.
// Registering here so the OS cron (which already runs `php artisan schedule:run`
// every minute per the active subs:sync job) picks it up.
Schedule::command('subscriptions:terminate-failed')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/subs-terminate.log'));