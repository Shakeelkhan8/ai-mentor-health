<x-guest-layout>
    <h1 class="text-lg font-medium text-gray-900">Sign in</h1>
    <p class="mt-1 mb-6 text-sm text-gray-600">Welcome back to {{ config('mentor.name') }}.</p>

    @if (config('mentor.demo.enabled'))
        <form method="POST" action="{{ route('demo.login') }}" class="mb-6">
            @csrf
            <button type="submit" class="btn w-full border border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100 focus-visible:ring-brand-500">
                Try the demo (no signup)
            </button>
            <p class="mt-1 text-xs text-gray-500">Opens a shared account with sample habits, plans and coach chat.</p>
        </form>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex flex-col gap-1 text-sm">
                <a class="text-gray-600 underline hover:text-gray-900" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                <a class="text-gray-600 underline hover:text-gray-900" href="{{ route('register') }}">{{ __('Create an account') }}</a>
            </div>

            <x-primary-button class="ms-3">{{ __('Log in') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
