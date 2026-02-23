<?php

namespace App\Models;

use App\Services\ApplicationAuditLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (EmailLog $emailLog) {
            app(ApplicationAuditLogger::class)->log(
                actionType: 'email.logged',
                userId: $emailLog->user_id,
                entityType: 'email_log',
                entityId: $emailLog->id,
                description: 'Email event logged.',
                metadata: [
                    'type' => $emailLog->type,
                    'status' => $emailLog->status,
                    'to' => $emailLog->to,
                    'subject' => $emailLog->subject,
                ]
            );
        });
    }

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'to',
        'status',
        'provider_message_id',
        'payload',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
