<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\TerminateFailedSubscriptions::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Runs every day at 2:00 AM — terminates past_due subscriptions
        // whose 7-day grace period has expired
        $schedule->command('subscriptions:terminate-failed')->dailyAt('02:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}