@props(['align' => 'right', 'width' => '48'])

<div class="position-relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-cloak
         class="position-absolute mt-2 bg-white border rounded-3 shadow-sm py-1 {{ $align === 'left' ? 'start-0' : 'end-0' }}"
         style="z-index: 50; min-width: 160px;"
         @click="open = false">
        {{ $content }}
    </div>
</div>
