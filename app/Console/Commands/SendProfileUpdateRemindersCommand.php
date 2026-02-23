<?php

namespace App\Console\Commands;

use App\Mail\ProfileUpdateMail;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendProfileUpdateRemindersCommand extends Command
{
    protected $signature = 'profiles:send-update-reminders {--user-id= : Send reminder only to a specific client user ID}';

    protected $description = 'Send automated profile update reminders to clients with outdated profiles';

    public function handle(): int
    {
        $this->info('Checking clients for profile update reminders...');

        $targetUserId = $this->option('user-id');

        $query = User::query()
            ->where('role', 'client')
            ->whereNotNull('email_verified_at')
            ->where('receives_notifications', true)
            ->where('updated_at', '<', now()->subMonths(6));

        if (!empty($targetUserId)) {
            $query->where('id', (int) $targetUserId);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info('No clients require profile update reminders at this time.');
            return self::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $recentReminderExists = EmailLog::where('user_id', $user->id)
                ->where('type', 'profile_update_required')
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            if ($recentReminderExists) {
                $skipped++;
                continue;
            }

            try {
                $message = 'Please review and update your profile information to keep your account details current.';

                Mail::to($user->email)->queue(new ProfileUpdateMail($user, $message));

                EmailLog::create([
                    'user_id' => $user->id,
                    'type' => 'profile_update_required',
                    'to' => $user->email,
                    'status' => 'queued',
                    'subject' => 'Profile Update Required',
                ]);

                $sent++;
            } catch (\Throwable $exception) {
                EmailLog::create([
                    'user_id' => $user->id,
                    'type' => 'profile_update_required',
                    'to' => $user->email,
                    'status' => 'failed',
                    'subject' => 'Profile Update Required',
                    'error' => $exception->getMessage(),
                ]);

                $this->error("Failed for {$user->email}: {$exception->getMessage()}");
            }
        }

        $this->info("Profile reminders queued: {$sent}");
        $this->info("Skipped (recently reminded): {$skipped}");

        return self::SUCCESS;
    }
}
