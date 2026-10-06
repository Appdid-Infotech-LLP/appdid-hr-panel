@props([
    // list of ['label' => string, 'value' => int]
    'rows',
    'empty' => 'No data for this period.',
])

@php
    $total = array_sum(array_column($rows, 'value'));
    $max = max([1, ...array_column($rows, 'value')]);
@endphp

{{-- Every value is printed beside its bar, so this chart doubles as its own table. --}}
<div {{ $attributes }}>
    @if ($total === 0)
        <p class="py-10 text-center text-sm text-slate-500">{{ $empty }}</p>
    @else
        <ul class="space-y-3">
            @foreach ($rows as $row)
                <li class="flex items-center gap-3 text-sm">
                    <span class="w-28 shrink-0 truncate text-slate-600" title="{{ $row['label'] }}">{{ $row['label'] }}</span>
                    <div class="h-2.5 min-w-0 flex-1">
                        @if ($row['value'] > 0)
                            <div class="h-full rounded-r bg-brand-teal" style="width: {{ max(2, $row['value'] / $max * 100) }}%"></div>
                        @endif
                    </div>
                    <span class="w-16 shrink-0 text-right text-slate-700 tabular-nums">
                        {{ $row['value'] }}
                        <span class="text-xs text-slate-400">{{ round($row['value'] / $total * 100) }}%</span>
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
