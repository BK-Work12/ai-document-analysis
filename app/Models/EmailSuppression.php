<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSuppression extends Model
{
    protected $fillable = [
        'email',
        'reason',
        'bounce_type',
        'complaint_type',
        'suppressed_at',
    ];

    protected $casts = [
        'suppressed_at' => 'datetime',
    ];

    /**
     * Mark an email as suppressed due to bounce
     */
    public static function suppressBounce($email, $bounceType = 'Undetermined')
    {
        return static::updateOrCreate(
            ['email' => $email],
            [
                'reason' => 'bounce',
                'bounce_type' => $bounceType,
                'suppressed_at' => now(),
            ]
        );
    }

    /**
     * Mark an email as suppressed due to complaint
     */
    public static function suppressComplaint($email, $complaintType = 'Undetermined')
    {
        return static::updateOrCreate(
            ['email' => $email],
            [
                'reason' => 'complaint',
                'complaint_type' => $complaintType,
                'suppressed_at' => now(),
            ]
        );
    }

    /**
     * Check if email is suppressed
     */
    public static function isSuppressed($email): bool
    {
        return static::where('email', $email)->exists();
    }
}
