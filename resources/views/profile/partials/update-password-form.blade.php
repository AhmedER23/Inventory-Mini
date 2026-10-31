<section>
    <header>
        <h2 class="section-title">{{ __('ui.Update Password') }}</h2>
        <p class="section-sub">{{ __('ui.Ensure your account is using a long, random password to stay secure.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="field">
            <x-input-label for="update_password_current_password" :value="__('ui.Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="field">
            <x-input-label for="update_password_password" :value="__('ui.New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="field">
            <x-input-label for="update_password_password_confirmation" :value="__('ui.Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="form-actions" style="justify-content:flex-start;">
            <x-primary-button>{{ __('ui.Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="section-sub mb-0">{{ __('ui.Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
