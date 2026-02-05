<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentMessage;

class Document extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'doc_type',
        's3_key',
        'version',
        'status',
        'original_filename',
        'detected_mime',
        'size_bytes',
        'uploaded_at',
        'reviewed_at',
        'reviewed_by',
        'notes',
        'correction_feedback',
        'correction_requested_at',
        // TextExtract fields
        'extracted_text',
        'text_extraction_metadata',
        'extraction_status',
        'extraction_error',
        'extraction_started_at',
        'extraction_completed_at',
        // Bedrock Analysis fields
        'analysis_result',
        'analysis_metadata',
        'analysis_status',
        'analysis_error',
        'analysis_started_at',
        'analysis_completed_at',
        // Classification & Risk
        'classified_doc_type',
        'risk_flags',
        'missing_fields',
        'confidence_score',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'correction_requested_at' => 'datetime',
            'extraction_started_at' => 'datetime',
            'extraction_completed_at' => 'datetime',
            'analysis_started_at' => 'datetime',
            'analysis_completed_at' => 'datetime',
            'text_extraction_metadata' => 'json',
            'analysis_metadata' => 'json',
            'risk_flags' => 'json',
            'missing_fields' => 'json',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function messages()
    {
        return $this->hasMany(DocumentMessage::class)->orderBy('created_at');
    }

    public function flags()
    {
        return $this->hasMany(DocumentFlag::class);
    }

    public function reuploadRequests()
    {
        return $this->hasMany(DocumentReuploadRequest::class);
    }

    public function activities()
    {
        return $this->hasMany(DocumentActivity::class)->orderBy('created_at', 'desc');
    }

    public function activeFlaggedIssues()
    {
        return $this->flags()->whereNull('resolved_at');
    }

    public function pendingReuploadRequest()
    {
        return $this->reuploadRequests()->where('status', 'pending')->latest()->first();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'document_tags')
            ->withPivot('reason', 'confidence', 'manual', 'tagged_by', 'tagged_at')
            ->withTimestamps();
    }

    public function conversations()
    {
        return $this->hasMany(DocumentConversation::class);
    }

    public function review()
    {
        return $this->hasOne(DocumentReview::class);
    }
}
