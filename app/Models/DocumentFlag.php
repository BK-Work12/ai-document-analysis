<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'flagged_by',
        'flag_type',
        'description',
        'severity',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    public function resolve()
    {
        $this->update(['resolved_at' => now()]);
    }

    public function getReadableFlagType(): string
    {
        return match($this->flag_type) {
            'fraud_suspected' => '🚨 Fraud Suspected',
            'incomplete_data' => '⚠️ Incomplete Data',
            'quality_issue' => '📸 Quality Issue',
            'missing_fields' => '📋 Missing Fields',
            'expiration_alert' => '⏰ Expiration Alert',
            default => '🏷️ Other',
        };
    }

    public function getSeverityColor(): string
    {
        return match($this->severity) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'blue',
            default => 'gray',
        };
    }
}
