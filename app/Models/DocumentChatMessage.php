<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DocumentChatMessage Model
 * 
 * Individual messages in a document conversation
 */
class DocumentChatMessage extends Model
{
    use HasFactory;

    protected $table = 'document_chat_messages';

    protected $fillable = [
        'document_conversation_id',
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
        return $this->belongsTo(DocumentConversation::class, 'document_conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if message is from AI
     */
    public function isFromAi(): bool
    {
        return $this->sender_type === 'ai';
    }

    /**
     * Check if message is from user
     */
    public function isFromUser(): bool
    {
        return $this->sender_type === 'user';
    }
}
