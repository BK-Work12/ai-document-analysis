<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUserConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_user_id',
        'admin_user_id',
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

    public function clientUser()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function messages()
    {
        return $this->hasMany(AdminUserChatMessage::class)->orderBy('created_at');
    }
}
