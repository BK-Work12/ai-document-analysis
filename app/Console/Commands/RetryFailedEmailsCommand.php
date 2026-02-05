<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use Illuminate\Console\Command;

class RetryFailedEmailsCommand extends Command
{
    protected $signature = 'emails:retry-failed';
    protected $description = 'Retry failed emails up to 3 times';

    public function handle()
    {
        $this->info('Retrying failed emails...');

        $failedEmails = EmailLog::where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->where('updated_at', '<', now()->subHours(1))
            ->get();

        foreach ($failedEmails as $email) {
            $email->increment('retry_count');
            $this->info("Retrying email to {$email->to}");
        }

        $this->info("Queued " . $failedEmails->count() . " emails for retry.");
    }
}
