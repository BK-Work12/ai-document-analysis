<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReuploadRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'requested_by',
        'reason',
        'instructions',
        'deadline',
        'status',
        'original_version',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->deadline && $this->deadline->isPast());
    }

    public function markCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function getDaysRemaining(): int
    {
        if (!$this->deadline) {
            return null;
        }

        $days = now()->diffInDays($this->deadline);
        return max(0, $days);
    }
}
