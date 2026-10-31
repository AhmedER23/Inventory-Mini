<x-guest-layout>
    <div style="text-align: center;">
        <h1 class="guest-title" style="margin-bottom: 8px;">{{ __('ui.Verify Your Email Address') }}</h1>

        <p class="section-sub mb-4">
            {{ __('ui.We have sent a 6-digit verification code to :email. Please enter it below to activate your account.', ['email' => auth()->user()->email]) }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success py-2 px-3 mb-3" style="border-radius: 10px; font-size: 0.9rem;">
                {{ __('ui.A new verification code has been sent to your email address.') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.code') }}" class="mb-4">
            @csrf

            <div class="field mb-3">
                <input type="text"
                       id="code"
                       name="code"
                       class="form-control text-center @error('code') is-invalid @enderror"
                       style="letter-spacing: 12px; font-size: 28px; font-weight: 700; font-family: monospace; height: 56px;"
                       maxlength="6"
                       inputmode="numeric"
                       pattern="[0-9]{6}"
                       placeholder="••••••"
                       required
                       autofocus>

                @error('code')
                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-app btn-app-primary w-100 justify-content-center" style="height: 48px; font-size: 1rem;">
                {{ __('ui.Verify Code') }} &rarr;
            </button>
        </form>

        <div class="d-flex align-items-center justify-content-between pt-3 border-top" style="gap: 12px; flex-wrap: wrap;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none p-0" style="font-size: 0.88rem; color: var(--primary);">
                    {{ __('ui.Resend Code') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none text-muted p-0" style="font-size: 0.88rem;">
                    {{ __('ui.Logout') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
