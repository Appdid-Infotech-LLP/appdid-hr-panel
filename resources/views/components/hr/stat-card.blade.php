@props(['label', 'value', 'accent' => 'teal'])

@php
    $accents = [
        'teal' => 'bg-brand-teal-light text-brand-teal',
        'yellow' => 'bg-brand-yellow/20 text-brand-yellow-dark',
        'green' => 'bg-emerald-50 text-emerald-600',
        'red' => 'bg-rose-50 text-rose-600',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
@endphp

<div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5">
    <div @class([
        'flex h-11 w-11 shrink-0 items-center justify-center rounded-lg',
        $accents[$accent] ?? $accents['teal'],
    ])>
        {{ $icon }}
    </div>

    <div class="min-w-0">
        <p class="text-2xl font-semibold text-slate-900">{{ $value }}</p>
        <p class="truncate text-sm text-slate-500">{{ $label }}</p>
    </div>
</div>
