<section>
    <header>
        <h2 class="section-title">{{ __('ui.Profile Information') }}</h2>
        <p class="section-sub">{{ __('ui.Update your account profile information and email address.') }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="field">
            <x-input-label for="name" :value="__('ui.Name')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="field">
            <x-input-label for="email" :value="__('ui.Email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />

            @if (! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="section-sub">
                        {{ __('ui.Your email address is unverified.') }}

                        <button form="send-verification" class="link-app" style="background:none;border:0;padding:0;">
                            {{ __('ui.Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="status-ok mt-2">
                            {{ __('ui.A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-actions" style="justify-content:flex-start;">
            <x-primary-button>{{ __('ui.Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="section-sub mb-0">{{ __('ui.Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
