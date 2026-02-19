<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUserChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_conversation_id',
        'sender_type',
        'user_id',
        'message',
        'role',
        'metadata',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'json',
            'sent_at' => 'datetime',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(AdminUserConversation::class, 'admin_user_conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
