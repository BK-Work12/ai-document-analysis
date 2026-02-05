<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DocumentReview Model
 * 
 * Auto-review results and manual review tracking
 */
class DocumentReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'review_status',
        'review_notes',
        'auto_review_results',
        'quality_score',
        'reviewed_by',
        'reviewed_at',
        'auto_reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'auto_review_results' => 'json',
            'reviewed_at' => 'datetime',
            'auto_reviewed_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if document is approved
     */
    public function isApproved(): bool
    {
        return $this->review_status === 'approved';
    }

    /**
     * Check if document needs revision
     */
    public function needsRevision(): bool
    {
        return $this->review_status === 'needs_revision';
    }
}
