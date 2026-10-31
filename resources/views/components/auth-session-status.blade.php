@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'status-ok']) }}>
        {{ $status }}
    </div>
@endif
