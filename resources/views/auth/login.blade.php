<x-guest-layout>
    <h1 class="guest-title">{{ __('ui.Log in') }}</h1>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <x-input-label for="email" :value="__('ui.Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="field">
            <x-input-label for="password" :value="__('ui.Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label for="remember_me" class="check-row">
            <input id="remember_me" type="checkbox" name="remember">
            <span>{{ __('ui.Remember me') }}</span>
        </label>

        <div class="form-actions">
            @if (Route::has('password.request'))
                <a class="link-app" href="{{ route('password.request') }}">{{ __('ui.Forgot your password?') }}</a>
            @endif

            <x-primary-button>{{ __('ui.Log in') }}</x-primary-button>
        </div>

        <div class="text-center mt-4">
            <a class="link-app" href="{{ route('register') }}">{{ __('ui.Register') }}</a>
        </div>
    </form>
</x-guest-layout>
