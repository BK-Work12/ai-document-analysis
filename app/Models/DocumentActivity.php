<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'action_type',
        'old_value',
        'new_value',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'json',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReadableAction(): string
    {
        return match($this->action_type) {
            'upload' => 'Document Uploaded',
            'status_change' => 'Status Changed',
            'flag_added' => 'Flag Added',
            'flag_resolved' => 'Flag Resolved',
            'reupload_requested' => 'Re-upload Requested',
            'comment_added' => 'Comment Added',
            'corrected' => 'Document Corrected',
            'approved' => 'Document Approved',
            'rejected' => 'Document Rejected',
            default => ucwords(str_replace('_', ' ', $this->action_type)),
        };
    }

    public function getIconForAction(): string
    {
        return match($this->action_type) {
            'upload' => '📤',
            'status_change' => '🔄',
            'flag_added' => '🚩',
            'flag_resolved' => '✅',
            'reupload_requested' => '🔁',
            'comment_added' => '💬',
            'corrected' => '✏️',
            'approved' => '✓',
            'rejected' => '✗',
            default => '📝',
        };
    }

    public function getActionSummary(): string
    {
        $actor = $this->user ? $this->user->name : 'System';
        $icon = $this->getIconForAction();
        
        return match($this->action_type) {
            'upload' => "$icon $actor uploaded the document",
            'status_change' => "$icon Status changed from {$this->old_value} to {$this->new_value} by $actor",
            'flag_added' => "$icon {$this->new_value} flag added by $actor",
            'reupload_requested' => "$icon Re-upload requested by $actor: {$this->new_value}",
            'comment_added' => "$icon $actor added a comment",
            'approved' => "$icon Document approved by $actor",
            'rejected' => "$icon Document rejected by $actor",
            default => "$icon {$this->getReadableAction()} by $actor",
        };
    }
}
