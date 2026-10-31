@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label-app']) }}>
    {{ $value ?? $slot }}
</label>
