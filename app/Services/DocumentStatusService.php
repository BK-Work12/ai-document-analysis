<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentRequirement;
use App\Models\User;

class DocumentStatusService
{
    /**
     * Check and update document status based on requirements
     * Mark document as 'approved' if all user requirements are met
     */
    public function checkAndUpdateDocumentStatus(Document $document): void
    {
        if ($document->extraction_status !== 'completed' || $document->analysis_status !== 'completed') {
            return;
        }

        if (!$document->analysis_result) {
            return;
        }

        $user = $document->user;
        
        // Get all active document requirements
        $requirements = DocumentRequirement::where('active', true)->get();
        
        foreach ($requirements as $requirement) {
            $userDocument = Document::where('user_id', $user->id)
                ->where('doc_type', $requirement->doc_type)
                ->first();
            
            // If any required document is missing or not approved, can't proceed
            if (!$userDocument || $userDocument->status !== 'approved') {
                return;
            }
        }
        
        // All requirements met - mark document as approved
        $document->update(['status' => 'approved']);
    }

    /**
     * Check if user has completed all document requirements
     */
    public function userHasCompletedAllRequirements(User $user): bool
    {
        $requirements = DocumentRequirement::where('active', true)->where('required', true)->get();
        
        if ($requirements->isEmpty()) {
            return true;
        }
        
        foreach ($requirements as $requirement) {
            $userDocument = Document::where('user_id', $user->id)
                ->where('doc_type', $requirement->doc_type)
                ->where('status', 'approved')
                ->first();
            
            if (!$userDocument) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get missing documents for a user
     */
    public function getMissingDocuments(User $user): array
    {
        $requirements = DocumentRequirement::where('active', true)
            ->where('required', true)
            ->get();
        
        $missing = [];
        
        foreach ($requirements as $requirement) {
            $userDocument = Document::where('user_id', $user->id)
                ->where('doc_type', $requirement->doc_type)
                ->where('status', 'approved')
                ->first();
            
            if (!$userDocument) {
                $missing[] = [
                    'doc_type' => $requirement->doc_type,
                    'description' => $requirement->description,
                ];
            }
        }
        
        return $missing;
    }

    /**
     * Get documents that need correction
     */
    public function getDocumentsNeedingCorrection(User $user)
    {
        return Document::where('user_id', $user->id)
            ->where('status', 'needs_correction')
            ->get();
    }

    /**
     * Mark document as needing correction with feedback
     */
    public function markForCorrection(Document $document, string $feedback): void
    {
        $document->update([
            'status' => 'needs_correction',
            'correction_feedback' => $feedback,
            'correction_requested_at' => now(),
        ]);
    }
}
