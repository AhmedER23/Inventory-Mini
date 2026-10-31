<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-app btn-app-danger']) }}>
    {{ $slot }}
</button>
