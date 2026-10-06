@php
    $weekdayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Calendar</h2>
            <p class="mt-1 text-sm text-slate-500">All scheduled rounds across every candidate, by day.</p>
        </div>

        <a href="{{ route('hr.rounds.schedule') }}" wire:navigate
            class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-4 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Schedule Round
        </a>
    </div>

    {{-- Month navigation + filters --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4">
        <div class="flex items-center gap-2">
            <button type="button" wire:click="previousMonth" wire:loading.attr="disabled" wire:target="previousMonth,nextMonth,goToToday"
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            <h3 class="flex w-40 items-center justify-center gap-2 text-center text-base font-semibold text-slate-900">
                <span>{{ $monthLabel }}</span>
                <svg wire:loading wire:target="previousMonth,nextMonth,goToToday" class="h-3.5 w-3.5 shrink-0 animate-spin text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V2.5A9.5 9.5 0 0 0 2.5 12H4Z" />
                </svg>
            </h3>

            <button type="button" wire:click="nextMonth" wire:loading.attr="disabled" wire:target="previousMonth,nextMonth,goToToday"
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            <button type="button" wire:click="goToToday" wire:loading.attr="disabled" wire:target="previousMonth,nextMonth,goToToday"
                class="ml-1 rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50 disabled:opacity-50">
                Today
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-hr.select2 name="typeFilter" :options="$this->types()" :value="$typeFilter" placeholder="All Round Types" class="w-48" />

            <x-hr.select2 name="modeFilter" :options="$this->modes()" :value="$modeFilter" placeholder="All Modes" class="w-40" />

            @if ($this->hasActiveFilters())
                <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Clear
                </button>
            @endif
        </div>
    </div>

    {{-- Month grid --}}
    <div wire:loading.class="opacity-50" wire:target="previousMonth,nextMonth,goToToday,typeFilter,modeFilter"
        class="overflow-hidden rounded-xl border border-slate-200 bg-white transition-opacity duration-150">
        <div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50 text-xs font-medium tracking-wide text-slate-500 uppercase">
            @foreach ($weekdayLabels as $label)
                <div class="px-3 py-2 text-center">{{ $label }}</div>
            @endforeach
        </div>

        <div class="divide-y divide-slate-100">
            @foreach ($weeks as $week)
                <div class="grid grid-cols-7 divide-x divide-slate-100">
                    @foreach ($week as $cell)
                        <div wire:key="cell-{{ $cell['date'] }}"
                            @class([
                                'min-h-28 p-2 align-top',
                                'bg-white' => $cell['inMonth'],
                                'bg-slate-50/60' => ! $cell['inMonth'],
                            ])>
                            <button type="button" wire:click="viewDay('{{ $cell['date'] }}')"
                                @class([
                                    'mb-1.5 flex h-6 w-6 items-center justify-center rounded-full text-xs font-medium',
                                    'bg-brand-teal text-white' => $cell['isToday'],
                                    'text-slate-700 hover:bg-slate-100' => ! $cell['isToday'] && $cell['inMonth'],
                                    'text-slate-400 hover:bg-slate-100' => ! $cell['inMonth'],
                                ])>
                                {{ $cell['day'] }}
                            </button>

                            <div class="space-y-1">
                                @foreach (array_slice($cell['rounds'], 0, 3) as $round)
                                    <button type="button" wire:click="viewDay('{{ $cell['date'] }}')"
                                        wire:key="chip-{{ $round['id'] }}"
                                        class="block w-full truncate rounded-md bg-brand-teal-light px-1.5 py-1 text-left text-[11px] font-medium text-brand-teal hover:bg-brand-teal/20">
                                        {{ $round['time'] }} · {{ $round['candidate_name'] }}
                                    </button>
                                @endforeach

                                @if (count($cell['rounds']) > 3)
                                    <button type="button" wire:click="viewDay('{{ $cell['date'] }}')"
                                        class="block w-full text-left text-[11px] font-medium text-slate-500 hover:text-slate-700">
                                        +{{ count($cell['rounds']) - 3 }} more
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
        <span class="font-medium text-slate-600">Status:</span>
        @foreach (['Pending', 'Scheduled', 'Completed', 'Cancelled'] as $status)
            <x-hr.badge :status="$status" />
        @endforeach
    </div>

    <x-hr.modal :show="$showDay" title="{{ $selectedDateLabel }}" maxWidth="max-w-xl">
        @if (empty($selectedDayRounds))
            <p class="py-6 text-center text-sm text-slate-500">No rounds scheduled on this day.</p>
        @else
            <div class="space-y-3">
                @foreach ($selectedDayRounds as $round)
                    <div wire:key="day-round-{{ $round['id'] }}" class="rounded-lg border border-slate-200 p-3">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <a href="{{ route('hr.candidates.show', $round['candidate_id']) }}" wire:navigate
                                    class="font-medium text-slate-900 hover:text-brand-teal">
                                    {{ $round['candidate_name'] }}
                                </a>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $round['time'] }} · {{ $round['interviewer'] }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5">
                                <x-hr.badge :status="$round['type']" />
                                <x-hr.badge :status="$round['mode']" />
                                <x-hr.badge :status="$round['status']" />
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-2.5">
                            <a href="{{ route('hr.rounds.edit', ['candidateId' => $round['candidate_id'], 'roundType' => $round['type']]) }}"
                                wire:navigate class="text-xs font-medium text-slate-600 hover:text-brand-teal">
                                Edit Round
                            </a>

                            @if ($round['meeting_link'])
                                <a href="{{ $round['meeting_link'] }}" target="_blank"
                                    class="text-xs font-medium text-brand-teal hover:underline">
                                    Join Meeting
                                </a>
                            @endif

                            <button type="button" wire:click="createCalendarEvent({{ $round['id'] }})"
                                wire:loading.attr="disabled" wire:target="createCalendarEvent({{ $round['id'] }})"
                                class="text-xs font-medium text-slate-600 hover:text-brand-teal disabled:opacity-60">
                                {{ $round['calendar_event_id'] ? 'Resync to Calendar' : 'Add to Google Calendar' }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <x-slot:footer>
            <button type="button" wire:click="close"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Close
            </button>
        </x-slot:footer>
    </x-hr.modal>
</div>
