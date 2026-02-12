<?php

namespace App\Jobs;

use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleSESComplaint implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $complaintData
    ) {}

    public function handle(): void
    {
        // Handle complaints (spam reports)
        foreach ($this->complaintData['complainedRecipients'] ?? [] as $recipient) {
            EmailSuppression::suppressComplaint(
                $recipient['emailAddress'],
                $this->complaintData['complaintFeedbackType'] ?? 'Undetermined'
            );
        }
    }
}
