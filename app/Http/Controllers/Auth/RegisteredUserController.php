<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $isFirstUser = User::count() === 0;
        $isDesignatedAdmin = strtolower(trim($request->email)) === strtolower(trim($adminEmail));
        $role = ($isDesignatedAdmin || $isFirstUser) ? 'admin' : 'user';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        $code = $user->generateVerificationCode();

        Log::info("Verification code for {$user->email}: {$code}");

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->name));
        } catch (\Throwable $e) {
            Log::error("Failed to send verification email to {$user->email}: " . $e->getMessage());
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
