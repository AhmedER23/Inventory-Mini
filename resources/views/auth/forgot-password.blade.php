<x-guest-layout>
    <h1 class="guest-title">{{ __('ui.Forgot your password?') }}</h1>

    <p class="section-sub mb-3">
        {{ __('ui.Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
    </p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <x-input-label for="email" :value="__('ui.Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-actions">
            <x-primary-button>{{ __('ui.Email Password Reset Link') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
