<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\ProfileUpdateMail;
use App\Models\Document;
use App\Models\User;
use App\Models\EmailLog;
use App\Events\DocumentStatusUpdated;
use App\Services\DocumentStatusService;
use App\Services\ApplicationAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected DocumentStatusService $statusService
    ) {}

    public function index()
    {
        $clients = User::where('role', 'client')->get();

        $stats = [
            'total_clients' => $clients->count(),
            'pending_docs' => Document::where('status', 'pending')->count(),
            'needs_correction' => Document::where('status', 'needs_correction')->count(),
            'approved' => Document::where('status', 'approved')->count(),
        ];

        return view('dashboard.admin', [
            'clients' => $clients,
            'stats' => $stats,
        ]);
    }

    public function showClient(User $user)
    {
        if ($user->role !== 'client') {
            abort(404);
        }

        $documents = Document::where('user_id', $user->id)
            ->orderBy('doc_type')
            ->orderByDesc('version')
            ->get();

        $emailLogs = EmailLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $isComplete = $this->statusService->userHasCompletedAllRequirements($user);

        return view('dashboard.admin-client-detail', [
            'user' => $user,
            'documents' => $documents,
            'emailLogs' => $emailLogs,
            'isComplete' => $isComplete,
        ]);
    }

    public function markDocumentStatus(Document $document, $status)
    {
        if (!in_array($status, ['approved', 'needs_correction'])) {
            abort(400, 'Invalid status');
        }

        $previousStatus = $document->status;
        
        $document->update([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'notes' => request('notes') ?? null,
            'correction_feedback' => $status === 'needs_correction' ? request('correction_feedback') : null,
        ]);

        app(ApplicationAuditLogger::class)->log(
            actionType: 'document.status_changed',
            userId: auth()->id(),
            entityType: 'document',
            entityId: $document->id,
            description: "Admin changed document status from {$previousStatus} to {$status}.",
            metadata: [
                'previous_status' => $previousStatus,
                'new_status' => $status,
                'notes' => request('notes'),
            ]
        );

        // Dispatch event for email notifications
        DocumentStatusUpdated::dispatch($document, $previousStatus, $status);

        return back()->with('success', "Document marked as {$status}.");
    }

    public function emailLogs(User $user)
    {
        if ($user->role !== 'client') {
            abort(404);
        }

        $logs = EmailLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.admin-email-logs', [
            'user' => $user,
            'logs' => $logs,
        ]);
    }

    public function flagProfileUpdateRequired(User $user, Request $request)
    {
        if ($user->role !== 'client') {
            abort(404);
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $user->update([
            'profile_update_required' => true,
            'profile_update_note' => $validated['note'],
            'profile_update_requested_at' => now(),
            'profile_update_requested_by' => auth()->id(),
        ]);

        app(ApplicationAuditLogger::class)->log(
            actionType: 'profile.update_required_flagged',
            userId: auth()->id(),
            entityType: 'user',
            entityId: $user->id,
            description: 'Admin flagged profile update required with note.',
            metadata: [
                'note' => $validated['note'],
            ]
        );

        try {
            Mail::to($user->email)->queue(new ProfileUpdateMail($user, $validated['note']));

            EmailLog::create([
                'user_id' => $user->id,
                'type' => 'profile_update_required',
                'subject' => 'Profile Update Required',
                'to' => $user->email,
                'status' => 'queued',
            ]);
        } catch (\Throwable $exception) {
            EmailLog::create([
                'user_id' => $user->id,
                'type' => 'profile_update_required',
                'subject' => 'Profile Update Required',
                'to' => $user->email,
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Profile update request saved, but email failed to queue.');
        }

        return back()->with('success', 'Profile update request sent successfully.');
    }
}
