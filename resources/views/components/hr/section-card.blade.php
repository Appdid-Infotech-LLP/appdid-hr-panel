@props(['title' => null, 'subtitle' => null, 'demo' => false])

<div {{ $attributes->class(['rounded-xl border border-slate-200 bg-white']) }}>
    @if ($title)
        <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
                @if ($subtitle)
                    <p class="mt-0.5 text-xs text-slate-500">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if ($demo)
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-500">Demo data</span>
                @endif

                {{ $actions ?? '' }}
            </div>
        </div>
    @endif

    <div class="p-5">
        {{ $slot }}
    </div>
</div>
