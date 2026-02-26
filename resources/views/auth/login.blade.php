<x-guest-layout>
    <!-- Header -->
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold text-teal-900 mb-2">Welcome Back</h2>
        <p class="text-teal-700">Sign in to continue your document review</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-teal-800 font-medium mb-2" />
            <x-text-input id="email" class="block mt-1 w-full auth-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-teal-800 font-medium mb-2" />
            <x-text-input id="password" class="block mt-1 w-full auth-input"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-teal-200 text-teal-700 shadow-sm focus:ring-teal-700" name="remember">
                <span class="ms-2 text-sm text-teal-700">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm auth-link" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full px-4 py-3 auth-btn-primary">
                Sign In
            </button>
        </div>

        <!-- Register Link -->
        @if (Route::has('register'))
            <div class="text-center pt-4 border-t border-teal-100">
                <p class="text-sm text-teal-700">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="auth-link">
                        Sign up
                    </a>
                </p>
            </div>
        @endif
    </form>
</x-guest-layout>
