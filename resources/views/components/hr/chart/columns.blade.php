@props([
    // list of ['label' => short x label, 'title' => tooltip heading, 'values' => [series name => int]]
    'columns',
    // list of ['name' => string, 'color' => css color], bottom-of-stack first.
    // Colour follows the series name, so it never shifts with the data.
    'series',
    'firstHeader' => 'Period',
])

@php
    $totals = array_map(fn (array $column): int => array_sum($column['values']), $columns);
    $rawMax = max([0, ...$totals]);

    // Four even gridline steps on a "nice" scale (1, 2, 5, 10, ...).
    $step = collect([1, 2, 5, 10, 20, 25, 50, 100, 200, 250, 500, 1000])->first(fn (int $s): bool => $s * 4 >= $rawMax) ?? (int) ceil($rawMax / 4);
    $max = $step * 4;
    $ticks = range($max, 0, -$step);

    $count = count($columns);
    $labelEvery = $count > 16 ? 4 : ($count > 8 ? 2 : 1);
    $multi = count($series) > 1;
@endphp

<div x-data="{ table: false }" {{ $attributes }}>
    <div class="mb-4 flex items-center justify-between gap-3">
        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
            @if ($multi)
                @foreach ($series as $s)
                    <span class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $s['color'] }}"></span>
                        {{ $s['name'] }}
                    </span>
                @endforeach
            @endif
        </div>
        <button type="button" x-on:click="table = ! table" class="shrink-0 text-xs font-medium text-brand-teal hover:underline"
            x-text="table ? 'View chart' : 'View table'">View table</button>
    </div>

    <div x-show="! table">
        <div class="flex gap-2">
            {{-- y-axis --}}
            <div class="relative h-40 w-6 shrink-0">
                @foreach ($ticks as $tick)
                    <span class="absolute right-0 translate-y-1/2 text-[11px] leading-none text-slate-400 tabular-nums" style="bottom: {{ $tick / $max * 100 }}%">{{ $tick }}</span>
                @endforeach
            </div>

            <div class="min-w-0 flex-1">
                <div class="relative h-40">
                    @foreach ($ticks as $tick)
                        <div @class(['absolute inset-x-0 h-px', $tick === 0 ? 'bg-slate-300' : 'bg-slate-100']) style="bottom: {{ $tick / $max * 100 }}%"></div>
                    @endforeach

                    <div class="absolute inset-0 flex items-end gap-0.5">
                        @foreach ($columns as $i => $column)
                            @php
                                $total = $totals[$i];
                                $segments = collect($series)->filter(fn (array $s): bool => ($column['values'][$s['name']] ?? 0) > 0)->values();
                                $tooltipAlign = match (true) {
                                    $i < 2 => 'left-0',
                                    $i >= $count - 2 => 'right-0',
                                    default => 'left-1/2 -translate-x-1/2',
                                };
                            @endphp

                            {{-- The whole column slot is the hover/focus target, not just the bar. --}}
                            <div tabindex="0" aria-label="{{ $column['title'] }}: {{ $total }}"
                                class="group relative flex h-full flex-1 items-end justify-center rounded-sm outline-none hover:bg-slate-400/10 focus-visible:bg-slate-400/10">
                                <div class="relative flex w-full max-w-8 flex-col-reverse gap-0.5" style="height: {{ $total / $max * 100 }}%">
                                    @foreach ($segments as $j => $s)
                                        <div @class(['w-full min-h-0.5', 'rounded-t' => $j === $segments->count() - 1])
                                            style="flex: {{ $column['values'][$s['name']] }} 1 0; background: {{ $s['color'] }}"></div>
                                    @endforeach

                                    <div class="pointer-events-none absolute bottom-full z-20 mb-1.5 hidden rounded-md bg-slate-900 px-2.5 py-1.5 text-xs whitespace-nowrap text-white shadow-lg group-hover:block group-focus-visible:block {{ $tooltipAlign }}">
                                        <p class="font-medium">{{ $column['title'] }}</p>
                                        @if ($multi)
                                            @foreach (array_reverse($series) as $s)
                                                <p class="mt-0.5 flex items-center gap-1.5 text-slate-300">
                                                    <span class="h-2 w-2 rounded-sm" style="background: {{ $s['color'] }}"></span>
                                                    {{ $s['name'] }}
                                                    <span class="ml-auto pl-3 text-white tabular-nums">{{ $column['values'][$s['name']] ?? 0 }}</span>
                                                </p>
                                            @endforeach
                                            <p class="mt-1 flex border-t border-white/15 pt-1 text-slate-300">Total <span class="ml-auto pl-3 text-white tabular-nums">{{ $total }}</span></p>
                                        @else
                                            <p class="mt-0.5 text-slate-300">{{ $series[0]['name'] }}: <span class="text-white tabular-nums">{{ $total }}</span></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- x-axis --}}
                <div class="mt-2 flex gap-0.5">
                    @foreach ($columns as $i => $column)
                        <div class="flex-1 text-center text-[11px] whitespace-nowrap text-slate-400">
                            {{ $i % $labelEvery === 0 ? $column['label'] : '' }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div x-show="table" x-cloak class="max-h-56 overflow-y-auto">
        <table class="w-full text-left text-sm">
            <thead class="sticky top-0 bg-white">
                <tr class="border-b border-slate-100 text-xs text-slate-500">
                    <th class="py-1.5 pr-3 font-medium">{{ $firstHeader }}</th>
                    @foreach ($series as $s)
                        <th class="py-1.5 pr-3 text-right font-medium">{{ $s['name'] }}</th>
                    @endforeach
                    @if ($multi)
                        <th class="py-1.5 text-right font-medium">Total</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-slate-700 tabular-nums">
                @foreach ($columns as $i => $column)
                    <tr>
                        <td class="py-1.5 pr-3">{{ $column['title'] }}</td>
                        @foreach ($series as $s)
                            <td class="py-1.5 pr-3 text-right">{{ $column['values'][$s['name']] ?? 0 }}</td>
                        @endforeach
                        @if ($multi)
                            <td class="py-1.5 text-right font-medium">{{ $totals[$i] }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
