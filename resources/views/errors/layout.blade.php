<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') - @yield('title')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { margin: 0; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f4f7fb; color: #17202a; }
        .box { max-width: 480px; margin: 14vh auto 0; text-align: center; padding: 0 16px; }
        .code { font-size: 4rem; font-weight: 700; line-height: 1; color: #0d1b2a; }
        h1 { font-size: 1.4rem; margin: 12px 0 8px; }
        p { color: #64748b; }
        a { display: inline-block; margin-top: 18px; padding: 10px 18px; border-radius: 10px; background: #0d1b2a; color: #fff; text-decoration: none; font-weight: 600; font-size: .9rem; }
    </style>
</head>
<body>
    <div class="box">
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="{{ url('/') }}">{{ __('ui.Back to dashboard') }}</a>
    </div>
</body>
</html>
