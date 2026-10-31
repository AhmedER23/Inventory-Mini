@php
    $appName = \App\Models\Setting::get('app_name', config('app.name'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $appName }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="welcome-shell">
        <header class="welcome-nav">
            <div class="welcome-brand">
                <span class="app-brand-badge">IM</span>
                <span>{{ $appName }}</span>
            </div>

            <div class="d-flex align-items-center" style="gap: 16px; flex-wrap: wrap;">
                @include('layouts.lang-switcher')

                @auth
                    <a href="{{ route('dashboard') }}" class="btn-app btn-app-primary">{{ __('ui.Open Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn-app btn-app-secondary">{{ __('ui.Log in') }}</a>
                    <a href="{{ route('register') }}" class="btn-app btn-app-primary">{{ __('ui.Register') }}</a>
                @endauth
            </div>
        </header>

        <section class="welcome-hero">
            <div class="welcome-pill">
                <span>{{ __('ui.Smart & Fast Inventory Management') }}</span>
            </div>

            <h1>{{ __('ui.Manage Your Inventory with Confidence') }}</h1>
            <p>{{ __('ui.Streamline product tracking, stock movements, low stock alerts, and periodic reporting with multi-role permissions in one central hub.') }}</p>

            <div class="welcome-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-app btn-app-primary" style="padding: 12px 28px; font-size: 1.05rem;">
                        {{ __('ui.Open Dashboard') }} &rarr;
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-app btn-app-primary" style="padding: 12px 28px; font-size: 1.05rem;">
                        {{ __('ui.Get Started') }} &rarr;
                    </a>
                    <a href="{{ route('login') }}" class="btn-app btn-app-secondary" style="padding: 12px 24px; font-size: 1.05rem;">
                        {{ __('ui.Log in') }}
                    </a>
                @endauth
            </div>
        </section>

        <section class="welcome-grid" style="margin-bottom: 64px;">
            <div class="welcome-feature-card">
                <h3>{{ __('ui.Products') }}</h3>
                <p>{{ __('ui.Manage your inventory items, stock levels and thresholds') }}</p>
            </div>

            <div class="welcome-feature-card">
                <h3>{{ __('ui.Stock Movement Tracking') }}</h3>
                <p>{{ __('ui.Detailed stock in and stock out history with before and after quantity tracking.') }}</p>
            </div>

            <div class="welcome-feature-card">
                <h3>{{ __('ui.Instant Low Stock Alerts') }}</h3>
                <p>{{ __('ui.Get notified immediately when item quantities drop below your defined thresholds.') }}</p>
            </div>

            <div class="welcome-feature-card">
                <h3>{{ __('ui.Advanced Reports & Exports') }}</h3>
                <p>{{ __('ui.Export your inventory data and transaction history to Excel, PDF, CSV, or Print.') }}</p>
            </div>
        </section>

        <footer class="welcome-footer">
            <div>&copy; {{ date('Y') }} {{ $appName }}. {{ __('ui.All rights reserved.') ?? 'All rights reserved.' }}</div>
        </footer>
    </div>
</body>
</html>
