<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmailPreferencesController extends Controller
{
    /**
     * Unsubscribe a user from email notifications
     */
    public function unsubscribe(Request $request, $token)
    {
        $user = User::where('email_unsubscribe_token', $token)->first();

        if (!$user) {
            return view('email-preferences.error', [
                'message' => 'Invalid unsubscribe link. Please try again or contact support.',
            ]);
        }

        $user->update(['receives_notifications' => false]);

        return view('email-preferences.unsubscribed', [
            'user' => $user,
        ]);
    }

    /**
     * Resubscribe a user to email notifications
     */
    public function resubscribe(Request $request, $token)
    {
        $user = User::where('email_unsubscribe_token', $token)->first();

        if (!$user) {
            return view('email-preferences.error', [
                'message' => 'Invalid resubscribe link. Please try again or contact support.',
            ]);
        }

        $user->update(['receives_notifications' => true]);

        return view('email-preferences.resubscribed', [
            'user' => $user,
        ]);
    }
}
