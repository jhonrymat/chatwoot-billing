@props([
    'type' => 'info',
    'icon' => null,
    'dismissible' => false,
    'colorClasses',
])

<div {{ $attributes->merge(['class' => 'fi-alert flex gap-3 rounded-xl p-4 ' . $colorClasses]) }}>
    @if ($icon)
        <div class="flex-shrink-0">
            <x-heroicon-o-{{ $icon }} class="fi-alert-icon h-5 w-5" aria-hidden="true" />
        </div>
    @endif

    <div class="fi-alert-content flex-1 text-sm">
        {{ $slot }}
    </div>

    @if ($dismissible)
        <button type="button" class="fi-alert-close-btn -m-1.5 ms-auto p-1.5 opacity-70 hover:opacity-100">
            <x-heroicon-o-x-mark class="h-5 w-5" />
        </button>
    @endif
</div>
