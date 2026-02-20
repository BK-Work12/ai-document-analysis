<?php

namespace App\Console;

use App\Console\Commands\CheckMissingDocumentsCommand;
use App\Console\Commands\ResolveStalePendingDocumentsCommand;
use App\Console\Commands\RetryFailedEmailsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for missing documents daily at 9 AM
        $schedule->command(CheckMissingDocumentsCommand::class)
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('Missing documents check completed successfully');
            })
            ->onFailure(function () {
                \Log::error('Missing documents check failed');
            });

        // Retry failed emails every 2 hours
        $schedule->command(RetryFailedEmailsCommand::class)
            ->everyTwoHours()
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('Failed email retry completed');
            });

        // Resolve stale pending documents every 30 minutes
        $schedule->command(ResolveStalePendingDocumentsCommand::class, ['--hours' => 2])
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('Stale pending document resolution completed');
            })
            ->onFailure(function () {
                \Log::error('Stale pending document resolution failed');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
