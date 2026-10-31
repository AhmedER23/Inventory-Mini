@php
    $roleInfo = match(Auth::user()->role) {
        'admin' => ['label' => __('ui.Admin'), 'badge' => 'badge-soft ok'],
        'manager' => ['label' => __('ui.Manager'), 'badge' => 'badge-soft warn'],
        default => ['label' => __('ui.User (Viewer)'), 'badge' => 'badge-soft'],
    };
@endphp
<header class="topbar">
    <div class="d-flex align-items-center" style="gap: 12px;">
        <div class="topbar-title" style="margin: 0;">{{ __('ui.Welcome') }}, {{ Auth::user()->name }}</div>
        <span class="{{ $roleInfo['badge'] }}" style="font-size: 0.8rem; font-weight: 600; padding: 4px 12px; border-radius: 9999px;">
            {{ $roleInfo['label'] }}
        </span>
    </div>

    <div class="topbar-actions">
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm btn-pill">{{ __('ui.Profile') }}</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm btn-pill">{{ __('ui.Logout') }}</button>
        </form>
    </div>
</header>
