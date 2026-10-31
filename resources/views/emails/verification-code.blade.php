<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7fb; margin: 0; padding: 24px; color: #1e293b; }
        .card { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 36px 32px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .logo { font-size: 20px; font-weight: 800; color: #2563eb; margin-bottom: 24px; }
        h1 { font-size: 20px; font-weight: 700; margin: 0 0 12px; color: #0f172a; }
        p { font-size: 15px; line-height: 1.5; color: #475569; margin: 0 0 20px; }
        .code-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0; }
        .code { font-size: 34px; font-weight: 800; letter-spacing: 8px; color: #0f172a; font-family: monospace; }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Inventory Mini</div>
        <h1>{{ __('ui.Verify Your Email Address') }}</h1>
        <p>{{ __('ui.Hello :name, thank you for registering! Please use the 6-digit verification code below to confirm your account.', ['name' => $userName]) }}</p>
        
        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>

        <p style="font-size: 13px; color: #64748b;">{{ __('ui.This code will expire in 15 minutes. If you did not create an account, no further action is required.') }}</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} Inventory Mini. {{ __('ui.All rights reserved.') }}
        </div>
    </div>
</body>
</html>

