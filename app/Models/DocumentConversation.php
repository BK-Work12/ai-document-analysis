<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DocumentConversation Model
 * 
 * Represents a chat conversation between admin and Bedrock about a specific document
 */
class DocumentConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'title',
        'status',
        'context',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
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

    public function messages()
    {
        return $this->hasMany(DocumentChatMessage::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(DocumentChatMessage::class)->latestOfMany('created_at');
    }
}
