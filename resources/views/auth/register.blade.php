<x-guest-layout>
    <!-- Header -->
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold text-teal-900 mb-2">Create your account</h2>
        <p class="text-teal-700">Start your eligibility and document journey</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-teal-800 font-medium mb-2" />
            <x-text-input id="name" class="block mt-1 w-full auth-input" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Jane Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-teal-800 font-medium mb-2" />
            <x-text-input id="email" class="block mt-1 w-full auth-input" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-teal-800 font-medium mb-2" />

            <x-text-input id="password" class="block mt-1 w-full auth-input"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="Create a strong password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-teal-800 font-medium mb-2" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full auth-input"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Re-enter your password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full px-4 py-3 auth-btn-primary">
                Create Account
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center pt-4 border-t border-teal-100">
            <p class="text-sm text-teal-700">
                {{ __('Already registered?') }}
                <a href="{{ route('login') }}" class="auth-link">
                    Sign in
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
