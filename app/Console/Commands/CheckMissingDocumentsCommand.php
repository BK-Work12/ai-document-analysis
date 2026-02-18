<?php

namespace App\Console\Commands;

use App\Mail\MissingDocumentsMail;
use App\Models\User;
use App\Models\EmailLog;
use App\Services\DocumentStatusService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckMissingDocumentsCommand extends Command
{
    protected $signature = 'documents:check-missing';
    protected $description = 'Check for users with missing required documents and send reminders';

    public function handle(DocumentStatusService $statusService)
    {
        $this->info('Checking for missing documents...');

        $users = User::where('role', 'client')
            ->where('email_verified_at', '!=', null)
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $missingDocs = $statusService->getMissingDocuments($user);

            if (!empty($missingDocs)) {
                try {
                    Mail::to($user->email)->queue(new MissingDocumentsMail($user, $missingDocs));
                    
                    EmailLog::create([
                        'user_id' => $user->id,
                        'type' => 'missing_documents_reminder',
                        'to' => $user->email,
                        'status' => 'queued',
                        'subject' => 'Missing Documents Reminder',
                    ]);

                    $sent++;
                } catch (\Exception $e) {
                    $this->error("Failed to send email to {$user->email}: {$e->getMessage()}");
                    
                    EmailLog::create([
                        'user_id' => $user->id,
                        'type' => 'missing_documents_reminder',
                        'to' => $user->email,
                        'status' => 'failed',
                        'subject' => 'Missing Documents Reminder',
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Sent {$sent} missing document reminders.");
    }
}
