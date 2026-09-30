@props(['status', 'rounds' => []])

@php
    $roundTypes = array_column(\App\Enums\RoundType::cases(), 'value');
    $isConcluded = in_array($status, ['Selected', 'Rejected'], true);
    $roundsByType = collect($rounds)->groupBy('type');

    $nodes = [
        ['key' => 'New', 'label' => 'Candidate Added', 'state' => 'completed', 'round' => null],
    ];

    if ($isConcluded) {
        // A candidate can be marked Selected/Rejected after ANY round — only
        // show the rounds that actually happened, then stop. A round type
        // with no record means the process never got there, so don't render
        // it (or anything after it) as a hollow "upcoming" step — that would
        // suggest it's still coming.
        foreach ($roundTypes as $type) {
            $typeRounds = $roundsByType->get($type);

            if (! $typeRounds) {
                break;
            }

            foreach ($typeRounds as $round) {
                $nodes[] = ['key' => $type, 'label' => $type, 'state' => 'completed', 'round' => $round];
            }
        }
    } else {
        // Driven by which rounds actually exist, not current_stage — that
        // column only updates once stage-transition logic is implemented,
        // so it can lag behind rounds that have already been scheduled.
        //
        // A round type with no record isn't rendered at all — only rounds
        // that have actually been scheduled show up as their own node, one
        // node per round (a type booked more than once, e.g. two Technical
        // Rounds, gets a node for each).
        $hasUnscheduledType = false;

        foreach ($roundTypes as $type) {
            $typeRounds = $roundsByType->get($type, []);

            if (count($typeRounds) === 0) {
                $hasUnscheduledType = true;

                continue;
            }

            foreach ($typeRounds as $round) {
                $state = \Illuminate\Support\Carbon::parse($round['date'].' '.$round['time'])->isPast()
                    ? 'completed'
                    : 'current';

                $nodes[] = ['key' => $type, 'label' => $type, 'state' => $state, 'round' => $round];
            }
        }

        // A single generic placeholder for whatever comes next, so the
        // timeline always shows there's more to schedule without naming a
        // specific round type that hasn't actually been booked yet.
        if ($hasUnscheduledType) {
            $nodes[] = ['key' => 'Next', 'label' => 'Next Round', 'state' => 'upcoming', 'round' => null];
        }
    }

    $nodes[] = [
        'key' => 'Outcome',
        'label' => match ($status) {
            'Selected' => 'Selected',
            'Rejected' => 'Rejected',
            default => 'Selected / Rejected',
        },
        'state' => match ($status) {
            'Selected' => 'completed',
            'Rejected' => 'rejected',
            default => 'upcoming',
        },
        'round' => null,
    ];
@endphp

<div class="flex flex-col gap-0">
    @foreach ($nodes as $index => $node)
        @php $state = $node['state']; $round = $node['round']; @endphp

        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div @class([
                    'flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 text-xs font-semibold',
                    'border-emerald-500 bg-emerald-500 text-white' => $state === 'completed',
                    'border-brand-teal bg-brand-teal text-white ring-4 ring-brand-teal-light' => $state === 'current',
                    'border-slate-300 bg-white text-slate-400' => $state === 'upcoming',
                    'border-rose-400 bg-rose-50 text-rose-500' => $state === 'rejected',
                ])>
                    @if ($state === 'completed')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    @elseif ($state === 'rejected')
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
                        'bg-emerald-400' => $state === 'completed',
                        'bg-slate-200' => $state !== 'completed',
                    ]) style="min-height: 2.25rem"></div>
                @endif
            </div>

            <div class="pb-9">
                <p @class([
                    'text-sm font-semibold',
                    'text-slate-900' => in_array($state, ['completed', 'current']),
                    'text-slate-400' => $state === 'upcoming',
                    'text-rose-500' => $state === 'rejected',
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
                    @if (! empty($round['interviewer']))
                        <p class="mt-0.5 text-xs text-slate-500">Interviewer: {{ $round['interviewer'] }}</p>
                    @endif
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
