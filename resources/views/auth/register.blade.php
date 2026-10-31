<x-guest-layout>
    <h1 class="guest-title">{{ __('ui.Register') }}</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <x-input-label for="name" :value="__('ui.Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="field">
            <x-input-label for="email" :value="__('ui.Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="field">
            <x-input-label for="password" :value="__('ui.Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="field">
            <x-input-label for="password_confirmation" :value="__('ui.Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="form-actions">
            <a class="link-app" href="{{ route('login') }}">{{ __('ui.Already registered?') }}</a>

            <x-primary-button>{{ __('ui.Register') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
