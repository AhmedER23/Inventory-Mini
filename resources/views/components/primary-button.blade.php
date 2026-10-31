<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-app btn-app-primary']) }}>
    {{ $slot }}
</button>
