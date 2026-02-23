<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ApplicationAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        app(ApplicationAuditLogger::class)->log(
            actionType: 'auth.login',
            userId: Auth::id(),
            entityType: 'user',
            entityId: Auth::id(),
            description: 'User logged in successfully.'
        );

        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Auth::guard('web')->logout();

        app(ApplicationAuditLogger::class)->log(
            actionType: 'auth.logout',
            userId: $userId,
            entityType: 'user',
            entityId: $userId,
            description: 'User logged out.'
        );

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
