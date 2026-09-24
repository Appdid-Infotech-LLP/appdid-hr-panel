@props(['status'])

@php
    // Central place mapping a status/stage/mode string to a badge color.
    // Add new statuses here as later phases introduce them.
    $variants = [
        'new' => 'bg-sky-50 text-sky-700',
        'pending' => 'bg-slate-100 text-slate-600',
        'scheduled' => 'bg-brand-teal-light text-brand-teal',
        'completed' => 'bg-emerald-50 text-emerald-600',
        'cancelled' => 'bg-rose-50 text-rose-600',
        'rescheduled' => 'bg-amber-50 text-amber-700',
        'no show' => 'bg-orange-50 text-orange-700',
        'selected' => 'bg-emerald-50 text-emerald-600',
        'rejected' => 'bg-rose-50 text-rose-600',
        'hr round' => 'bg-brand-teal-light text-brand-teal',
        'task round' => 'bg-brand-teal-light text-brand-teal',
        'technical round' => 'bg-brand-teal-light text-brand-teal',
        'final round' => 'bg-brand-teal-light text-brand-teal',
        'virtual' => 'bg-slate-100 text-slate-600',
        'in person' => 'bg-slate-100 text-slate-600',
        'yes' => 'bg-emerald-50 text-emerald-600',
        'not yet' => 'bg-slate-100 text-slate-600',
    ];

    $variant = $variants[strtolower($status)] ?? 'bg-slate-100 text-slate-600';
@endphp

<span {{ $attributes->class(["inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap", $variant]) }}>
    {{ $status }}
</span>
