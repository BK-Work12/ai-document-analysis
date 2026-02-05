<?php

namespace App\Jobs;

use App\Events\DocumentStatusUpdated;
use App\Mail\MissingDocumentsMail;
use App\Mail\DocumentCorrectionNeededMail;
use App\Models\EmailLog;
use App\Services\DocumentStatusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public DocumentStatusUpdated $event,
    ) {}

    public function handle(DocumentStatusService $statusService): void
    {
        $document = $this->event->document;
        $user = $document->user;

        try {
            // If document marked for correction, send correction email
            if ($this->event->newStatus === 'needs_correction') {
                Mail::to($user->email)->queue(new DocumentCorrectionNeededMail(
                    $user,
                    $document->doc_type,
                    $document->correction_feedback ?? 'Please review and resubmit this document.'
                ));

                $this->logEmail($user->id, 'correction_needed', $user->email);
            }
            
            // If document approved, check if all requirements complete
            elseif ($this->event->newStatus === 'approved') {
                if ($statusService->userHasCompletedAllRequirements($user)) {
                    // All documents complete - could trigger acceptance email or next steps
                }
            }
        } catch (\Exception $e) {
            $this->logEmail($user->id, 'error', $user->email, $e->getMessage());
        }
    }

    private function logEmail(int $userId, string $type, string $to, ?string $error = null): void
    {
        EmailLog::create([
            'user_id' => $userId,
            'type' => $type,
            'to' => $to,
            'status' => $error ? 'failed' : 'queued',
            'error' => $error,
        ]);
    }
}
