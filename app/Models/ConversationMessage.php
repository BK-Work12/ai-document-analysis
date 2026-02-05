<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Conversation Message Model (Placeholder for Days 13-15)
 * 
 * Individual messages within a conversation
 */
class ConversationMessage extends Model
{
    use HasFactory;

    protected $table = 'conversation_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'sources',
    ];

    protected function casts(): array
    {
        return [
            'sources' => 'json',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
