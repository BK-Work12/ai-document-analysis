<?php

namespace App\Http\Controllers\Admin;

use App\Models\Document;
use App\Models\DocumentFlag;
use App\Models\DocumentReuploadRequest;
use App\Models\DocumentActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Admin Document Management Controller
 * 
 * Handles:
 * - Flagging documents with issues
 * - Requesting document re-uploads
 * - Tracking document activity history
 */
class DocumentManagementController extends Controller
{
    /**
     * Flag a document with a specific issue
     * 
     * POST /admin/documents/{document}/flag
     */
    public function flagIssue(Document $document, Request $request)
    {
        $this->authorize('flag-document');

        $validated = $request->validate([
            'flag_type' => 'required|in:fraud_suspected,incomplete_data,quality_issue,missing_fields,expiration_alert,other',
            'description' => 'required|string|max:1000',
            'severity' => 'required|in:low,medium,high',
        ]);

        try {
            // Create the flag
            $flag = DocumentFlag::create([
                'document_id' => $document->id,
                'flagged_by' => auth()->id(),
                'flag_type' => $validated['flag_type'],
                'description' => $validated['description'],
                'severity' => $validated['severity'],
            ]);

            // Log activity
            DocumentActivity::create([
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'action_type' => 'flag_added',
                'new_value' => $validated['flag_type'],
                'metadata' => [
                    'severity' => $validated['severity'],
                    'description' => $validated['description'],
                ],
            ]);

            // TODO: Send notification to user
            // $document->user->notify(new DocumentFlagged($document, $flag));

            return response()->json([
                'success' => true,
                'flag' => $flag,
                'message' => 'Document flagged successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to flag document: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve a flag on a document
     * 
     * PATCH /admin/documents/flags/{flag}/resolve
     */
    public function resolveFlag(DocumentFlag $flag, Request $request)
    {
        $this->authorize('flag-document');

        try {
            $flag->resolve();

            // Log activity
            DocumentActivity::create([
                'document_id' => $flag->document_id,
                'user_id' => auth()->id(),
                'action_type' => 'flag_resolved',
                'new_value' => $flag->flag_type,
                'metadata' => [
                    'resolved_at' => now(),
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Flag resolved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve flag: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request a document re-upload
     * 
     * POST /admin/documents/{document}/request-reupload
     */
    public function requestReupload(Document $document, Request $request)
    {
        $this->authorize('request-reupload');

        // Validate input
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'instructions' => 'nullable|string|max:1000',
            'deadline' => 'nullable|date|after:today',
        ]);

        try {
            // Create re-upload request
            $reuploadRequest = DocumentReuploadRequest::create([
                'document_id' => $document->id,
                'requested_by' => auth()->id(),
                'reason' => $validated['reason'],
                'instructions' => $validated['instructions'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'original_version' => $document->version,
                'status' => 'pending',
            ]);

            // Update document status
            $document->update(['status' => 'reupload_requested']);

            // Log activity
            DocumentActivity::create([
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'action_type' => 'reupload_requested',
                'new_value' => $validated['reason'],
                'metadata' => [
                    'instructions' => $validated['instructions'],
                    'deadline' => $validated['deadline'],
                ],
            ]);

            // TODO: Send email notification to user
            // $document->user->notify(new ReuploadRequested($document, $reuploadRequest));

            return response()->json([
                'success' => true,
                'request' => $reuploadRequest,
                'message' => 'Re-upload request sent to user',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to request re-upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get document activity history
     * 
     * GET /admin/documents/{document}/history
     */
    public function getHistory(Document $document, Request $request)
    {
        $this->authorize('view-document-history');

        try {
            // Get paginated activities
            $activities = $document->activities()
                ->with('user')
                ->paginate(20);

            // Transform activities for response
            $transformedActivities = $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'action_type' => $activity->action_type,
                    'action_readable' => $activity->getReadableAction(),
                    'action_icon' => $activity->getIconForAction(),
                    'action_summary' => $activity->getActionSummary(),
                    'user' => $activity->user ? [
                        'id' => $activity->user->id,
                        'name' => $activity->user->name,
                        'email' => $activity->user->email,
                    ] : null,
                    'old_value' => $activity->old_value,
                    'new_value' => $activity->new_value,
                    'metadata' => $activity->metadata,
                    'created_at' => $activity->created_at->toIso8601String(),
                ];
            });

            return response()->json([
                'success' => true,
                'activities' => $transformedActivities,
                'total' => $document->activities()->count(),
                'pagination' => [
                    'current_page' => $activities->currentPage(),
                    'total_pages' => $activities->lastPage(),
                    'per_page' => $activities->perPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch history: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get document flags
     * 
     * GET /admin/documents/{document}/flags
     */
    public function getFlags(Document $document)
    {
        $this->authorize('view-document-history');

        try {
            $flags = $document->flags()
                ->with('admin')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($flag) {
                    return [
                        'id' => $flag->id,
                        'flag_type' => $flag->flag_type,
                        'flag_type_readable' => $flag->getReadableFlagType(),
                        'severity' => $flag->severity,
                        'severity_color' => $flag->getSeverityColor(),
                        'description' => $flag->description,
                        'flagged_by' => [
                            'id' => $flag->admin->id,
                            'name' => $flag->admin->name,
                            'email' => $flag->admin->email,
                        ],
                        'is_resolved' => $flag->isResolved(),
                        'resolved_at' => $flag->resolved_at?->toIso8601String(),
                        'created_at' => $flag->created_at->toIso8601String(),
                    ];
                });

            return response()->json([
                'success' => true,
                'flags' => $flags,
                'active_flags' => $flags->where('is_resolved', false)->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch flags: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get re-upload requests for document
     * 
     * GET /admin/documents/{document}/reupload-requests
     */
    public function getReuploadRequests(Document $document)
    {
        $this->authorize('view-document-history');

        try {
            $requests = $document->reuploadRequests()
                ->with('admin')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'reason' => $request->reason,
                        'instructions' => $request->instructions,
                        'deadline' => $request->deadline?->toIso8601String(),
                        'days_remaining' => $request->deadline ? $request->getDaysRemaining() : null,
                        'status' => $request->status,
                        'is_pending' => $request->isPending(),
                        'is_completed' => $request->isCompleted(),
                        'is_expired' => $request->isExpired(),
                        'original_version' => $request->original_version,
                        'requested_by' => [
                            'id' => $request->admin->id,
                            'name' => $request->admin->name,
                            'email' => $request->admin->email,
                        ],
                        'completed_at' => $request->completed_at?->toIso8601String(),
                        'created_at' => $request->created_at->toIso8601String(),
                    ];
                });

            return response()->json([
                'success' => true,
                'requests' => $requests,
                'pending_count' => $requests->where('is_pending', true)->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reupload requests: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get document with all related admin data
     * 
     * GET /admin/documents/{document}/details
     */
    public function getDetails(Document $document)
    {
        $this->authorize('view-document-history');

        try {
            return response()->json([
                'success' => true,
                'document' => [
                    'id' => $document->id,
                    'user_id' => $document->user_id,
                    'doc_type' => $document->doc_type,
                    'original_filename' => $document->original_filename,
                    'version' => $document->version,
                    'status' => $document->status,
                    'size_bytes' => $document->size_bytes,
                    'uploaded_at' => $document->uploaded_at?->toIso8601String(),
                    'reviewed_at' => $document->reviewed_at?->toIso8601String(),
                    'reviewed_by' => $document->reviewer ? [
                        'id' => $document->reviewer->id,
                        'name' => $document->reviewer->name,
                    ] : null,
                    'notes' => $document->notes,
                ],
                'flags' => $document->activeFlaggedIssues()->count(),
                'pending_reupload' => $document->pendingReuploadRequest()?->only(['id', 'reason', 'deadline']),
                'activity_count' => $document->activities()->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch document details: ' . $e->getMessage(),
            ], 500);
        }
    }
}
