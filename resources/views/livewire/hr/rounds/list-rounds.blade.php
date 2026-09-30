@php
    $sortIcon = function (string $field) use ($sortField, $sortDirection) {
        if ($sortField !== $field) {
            return '';
        }

        return $sortDirection === 'asc' ? '↑' : '↓';
    };
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Recruitment Rounds</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $totalCount }} total · showing {{ $rounds->total() }} matching
                rounds</p>
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

    {{-- Filters --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search by candidate name..."
                    class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
            </div>

            <select wire:model.live="typeFilter"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                <option value="">All Round Types</option>
                @foreach ($this->types() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                <option value="">All Statuses</option>
                @foreach ($this->statuses() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="modeFilter"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                <option value="">All Modes</option>
                @foreach ($this->modes() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @if ($this->hasActiveFilters())
            <div class="mt-3 flex justify-end">
                <button type="button" wire:click="clearFilters"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Clear all filters
                </button>
            </div>
        @endif
    </div>

    <x-hr.section-card>
        @if ($rounds->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-hr.icon name="clock" />
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-900">No rounds found</h3>
                <p class="mt-1 max-w-sm text-sm text-slate-500">Try adjusting your search or filters, or schedule a new
                    round.</p>
                @if ($this->hasActiveFilters())
                    <button type="button" wire:click="clearFilters"
                        class="mt-4 text-sm font-medium text-brand-teal hover:text-brand-teal-dark">Clear all
                        filters</button>
                @endif
            </div>
        @else
            <div class="-mx-5 -mt-5 overflow-x-auto">
                <table class="w-full min-w-200 text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs tracking-wide text-slate-500 uppercase">
                            <th class="px-5 py-3 font-medium">Candidate</th>
                            <th class="px-5 py-3 font-medium">Round</th>
                            <th class="px-5 py-3 font-medium">
                                <button type="button" wire:click="sortBy('date')"
                                    class="flex items-center gap-1 hover:text-slate-700">Date &amp; Time
                                    {{ $sortIcon('date') }}</button>
                            </th>
                            <th class="px-5 py-3 font-medium">Mode</th>
                            <th class="px-5 py-3 font-medium">Interviewer</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rounds as $round)
                            <tr wire:key="round-{{ $round['id'] }}" class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <a href="{{ route('hr.candidates.show', $round['candidate_id']) }}" wire:navigate
                                        class="font-medium text-slate-900 hover:text-brand-teal">
                                        {{ $round['candidate_name'] }}
                                    </a>
                                </td>
                                <td class="px-5 py-3"><x-hr.badge :status="$round['type']" /></td>
                                <td class="px-5 py-3 text-slate-600">
                                    {{ \Illuminate\Support\Carbon::parse($round['date'])->format('M j, Y') }} ·
                                    {{ $round['time'] }}</td>
                                <td class="px-5 py-3"><x-hr.badge :status="$round['mode']" /></td>
                                <td class="px-5 py-3 text-slate-600">{{ $round['interviewer'] }}</td>
                                <td class="px-5 py-3"><x-hr.badge :status="$round['status']" /></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('hr.rounds.edit', ['candidateId' => $round['candidate_id'], 'roundType' => $round['type']]) }}"
                                            wire:navigate title="Edit"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                                            </svg>
                                        </a>

                                        <details class="relative">
                                            <summary
                                                class="flex h-8 w-8 list-none items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 6.75h.007v.008H12V6.75Zm0 5.25h.007v.008H12V12Zm0 5.25h.007v.008H12v-.008Z" />
                                                </svg>
                                            </summary>

                                            <div
                                                class="absolute right-0 z-10 mt-1 w-44 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                                                <button type="button"
                                                    wire:click="markCompleted({{ $round['candidate_id'] }}, '{{ $round['type'] }}')"
                                                    class="block w-full px-3.5 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Mark
                                                    Completed</button>
                                                <button type="button"
                                                    wire:click="cancelRound({{ $round['candidate_id'] }}, '{{ $round['type'] }}')"
                                                    wire:confirm="Cancel this round for {{ $round['candidate_name'] }}?"
                                                    class="block w-full px-3.5 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">Cancel
                                                    Round</button>
                                            </div>
                                        </details>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $rounds->links('livewire::tailwind') }}
            </div>
        @endif
    </x-hr.section-card>
</div>
