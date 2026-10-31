<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-app btn-app-secondary']) }}>
    {{ $slot }}
</button>
