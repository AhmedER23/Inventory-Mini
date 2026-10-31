@props(['name', 'show' => false])

<div
    x-data="{ show: @js($show) }"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="show = false"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    class="app-modal"
>
    <div class="app-modal-backdrop" x-on:click="show = false"></div>

    <div class="app-modal-dialog">
        {{ $slot }}
    </div>
</div>
