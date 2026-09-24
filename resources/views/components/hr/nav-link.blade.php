@props(['route', 'active' => null])

@php
    $isActive = $active ?? request()->routeIs($route . '*');
@endphp

<a
    href="{{ route($route) }}"
    @class([
        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
        'bg-brand-yellow text-brand-ink' => $isActive,
        'text-teal-100/80 hover:bg-white/10 hover:text-white' => ! $isActive,
    ])
>
    {{ $slot }}
</a>
