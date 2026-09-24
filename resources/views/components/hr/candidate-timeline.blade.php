@props(['stage', 'status', 'rounds' => []])

@php
    $roundTypes = ['HR Round', 'Task Round', 'Technical Round', 'Final Round'];
    $stageOrder = array_merge(['New'], $roundTypes, ['Selected']);
    $isRejected = $status === 'Rejected';

    // Position of the candidate's current stage in the pipeline order. A
    // rejected candidate keeps the position they were at when rejected.
    $currentIndex = array_search($stage, $stageOrder, true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;

    $roundsByType = collect($rounds)->keyBy('type');

    $nodes = [
        ['key' => 'New', 'label' => 'Candidate Added'],
        ...array_map(fn ($type) => ['key' => $type, 'label' => $type], $roundTypes),
        ['key' => 'Outcome', 'label' => $isRejected ? 'Rejected' : ($status === 'Selected' ? 'Selected' : 'Selected / Rejected')],
    ];
@endphp

<div class="flex flex-col gap-0">
    @foreach ($nodes as $index => $node)
        @php
            $isOutcomeNode = $node['key'] === 'Outcome';
            $nodeIndex = $isOutcomeNode ? count($stageOrder) - 1 + 1 : array_search($node['key'], $stageOrder, true);

            // A rejected candidate still *completed* the round they were
            // rejected at/after — only rounds after that point are
            // cancelled. Only the dedicated outcome node turns red.
            $state = match (true) {
                $isOutcomeNode && $isRejected => 'rejected',
                $isOutcomeNode && $status === 'Selected' => 'completed',
                $isOutcomeNode => 'upcoming',
                $isRejected && $nodeIndex > $currentIndex => 'cancelled',
                $isRejected && $nodeIndex <= $currentIndex => 'completed',
                $nodeIndex < $currentIndex => 'completed',
                $nodeIndex === $currentIndex => 'current',
                default => 'upcoming',
            };

            $round = $roundsByType->get($node['key']);
        @endphp

        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div @class([
                    'flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 text-xs font-semibold',
                    'border-emerald-500 bg-emerald-500 text-white' => $state === 'completed',
                    'border-brand-teal bg-brand-teal text-white ring-4 ring-brand-teal-light' => $state === 'current',
                    'border-slate-300 bg-white text-slate-400' => $state === 'upcoming',
                    'border-rose-400 bg-rose-50 text-rose-500' => in_array($state, ['rejected', 'cancelled']),
                ])>
                    @if ($state === 'completed')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    @elseif (in_array($state, ['rejected', 'cancelled']))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    @else
                        {{ $index + 1 }}
                    @endif
                </div>

                @if ($index < count($nodes) - 1)
                    <div @class([
                        'my-0.5 w-0.5 flex-1',
                        'bg-emerald-400' => in_array($state, ['completed']),
                        'bg-slate-200' => ! in_array($state, ['completed']),
                    ]) style="min-height: 2.25rem"></div>
                @endif
            </div>

            <div class="pb-9">
                <p @class([
                    'text-sm font-semibold',
                    'text-slate-900' => in_array($state, ['completed', 'current']),
                    'text-slate-400' => $state === 'upcoming',
                    'text-rose-500' => in_array($state, ['rejected', 'cancelled']),
                ])>
                    {{ $node['label'] }}
                    @if ($state === 'current')
                        <span class="ml-1.5 rounded-full bg-brand-teal-light px-2 py-0.5 text-[11px] font-medium text-brand-teal">Current</span>
                    @endif
                </p>

                @if ($round)
                    <p class="mt-0.5 text-xs text-slate-500">
                        {{ \Illuminate\Support\Carbon::parse($round['date'])->format('M j, Y') }} · {{ $round['time'] }} · <x-hr.badge :status="$round['mode']" />
                    </p>
                    @if (! empty($round['notes']))
                        <p class="mt-1 text-xs text-slate-500">{{ $round['notes'] }}</p>
                    @endif
                @elseif ($state === 'upcoming')
                    <p class="mt-0.5 text-xs text-slate-400">Not scheduled yet</p>
                @endif
            </div>
        </div>
    @endforeach
</div>
