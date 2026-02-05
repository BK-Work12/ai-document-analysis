<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\EmailLog;
use App\Events\DocumentStatusUpdated;
use App\Services\DocumentStatusService;

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
}
