<x-guest-layout>
    <h1 class="guest-title">{{ __('ui.Confirm Password') }}</h1>

    <p class="section-sub mb-3">
        {{ __('ui.This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="field">
            <x-input-label for="password" :value="__('ui.Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="form-actions">
            <x-primary-button>{{ __('ui.Confirm') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
