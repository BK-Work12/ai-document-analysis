<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Conversation Model (Placeholder for Days 13-15)
 * 
 * Stores AI chat conversations and messages
 * Database table created in Day 15
 */
class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'knowledge_base_id',
        'title',
        'started_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ConversationMessage::class);
    }
}
