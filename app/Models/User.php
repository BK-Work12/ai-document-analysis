<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\AdminUserConversation;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'receives_notifications',
        'email_unsubscribe_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'receives_notifications' => 'boolean',
        ];
    }

    /**
     * Boot the model - generate unsubscribe token on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->email_unsubscribe_token)) {
                $model->email_unsubscribe_token = bin2hex(random_bytes(32));
            }
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function adminUserConversationsAsClient()
    {
        return $this->hasMany(AdminUserConversation::class, 'client_user_id');
    }

    public function adminUserConversationsAsAdmin()
    {
        return $this->hasMany(AdminUserConversation::class, 'admin_user_id');
    }

    public function ensureEmailUnsubscribeToken(): string
    {
        if (empty($this->email_unsubscribe_token)) {
            $this->email_unsubscribe_token = bin2hex(random_bytes(32));
            $this->saveQuietly();
        }

        return $this->email_unsubscribe_token;
    }
}
