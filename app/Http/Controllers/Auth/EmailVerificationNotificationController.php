<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $code = $user->generateVerificationCode();

        \Illuminate\Support\Facades\Log::info("Verification code for {$user->email}: {$code}");

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->name));
        } catch (\Throwable $e) {
        }

        return back()
            ->with('status', 'verification-link-sent')
            ->with('dev_code', app()->environment('local') ? $code : null);
    }
}
