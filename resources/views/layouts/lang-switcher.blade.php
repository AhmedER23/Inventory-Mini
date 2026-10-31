@php
    $segments = request()->segments();

    if (in_array($segments[0] ?? null, ['en', 'ar', 'tr'], true)) {
        array_shift($segments);
    }

    $path = count($segments) ? '/' . implode('/', $segments) : '';
    $query = request()->getQueryString();
    $suffix = $path . ($query ? '?' . $query : '');
@endphp

<div class="lang-switcher">
    <a href="{{ url('/en' . $suffix) }}" class="lang-link {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
    <a href="{{ url('/ar' . $suffix) }}" class="lang-link {{ app()->getLocale() === 'ar' ? 'active' : '' }}">AR</a>
    <a href="{{ url('/tr' . $suffix) }}" class="lang-link {{ app()->getLocale() === 'tr' ? 'active' : '' }}">TR</a>
</div>
