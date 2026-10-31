<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if (! $user->verification_code || ($user->verification_code_expires_at && $user->verification_code_expires_at->isPast())) {
            $code = $user->generateVerificationCode();
            \Illuminate\Support\Facades\Log::info("Verification code for {$user->email}: {$code}");
            try {
                Mail::to($user->email)->send(new VerificationCodeMail($code, $user->name));
            } catch (\Throwable $e) {
            }
            session()->flash('dev_code', app()->environment('local') ? $code : null);
        }

        return view('auth.verify-email');
    }
}
