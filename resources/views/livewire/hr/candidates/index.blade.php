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
            <h2 class="text-xl font-semibold text-slate-900">Candidates</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $totalCount }} total · showing {{ $candidates->total() }} matching candidates</p>
        </div>

        <a href="{{ route('hr.candidates.create') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-4 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Candidate
        </a>
    </div>

    {{-- Filters --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        type="search" wire:model.live.debounce.400ms="search" placeholder="Search by name, email or phone..."
                        class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20"
                    >
                </div>
            </div>

            <select wire:model.live="stageFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                <option value="">All Stages</option>
                @foreach ($this->stages() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                <option value="">All Statuses</option>
                @foreach ($this->statuses() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="experienceFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                <option value="">Any Experience</option>
                <option value="0-2">0 - 2 years</option>
                <option value="2-5">2 - 5 years</option>
                <option value="5-10">5 - 10 years</option>
                <option value="10+">10+ years</option>
            </select>

            <select wire:model.live="locationFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                <option value="">All Locations</option>
                @foreach ($this->locations() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <select wire:model.live="dateFilter" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
                <option value="">Added Any Time</option>
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>

            @if ($this->hasActiveFilters())
                <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Clear all filters
                </button>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <x-hr.section-card>
        @if ($candidates->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-900">No candidates found</h3>
                <p class="mt-1 max-w-sm text-sm text-slate-500">Try adjusting your search or filters, or add a new candidate.</p>
                @if ($this->hasActiveFilters())
                    <button type="button" wire:click="clearFilters" class="mt-4 text-sm font-medium text-brand-teal hover:text-brand-teal-dark">Clear all filters</button>
                @endif
            </div>
        @else
            <div class="-mx-5 -mt-5 overflow-x-auto">
                <table class="w-full min-w-240 text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs tracking-wide text-slate-500 uppercase">
                            <th class="px-5 py-3 font-medium">
                                <button type="button" wire:click="sortBy('first_name')" class="flex items-center gap-1 hover:text-slate-700">Candidate {{ $sortIcon('first_name') }}</button>
                            </th>
                            <th class="px-5 py-3 font-medium">Email</th>
                            <th class="px-5 py-3 font-medium">Phone</th>
                            <th class="px-5 py-3 font-medium">Company</th>
                            <th class="px-5 py-3 font-medium">
                                <button type="button" wire:click="sortBy('experience_years')" class="flex items-center gap-1 hover:text-slate-700">Experience {{ $sortIcon('experience_years') }}</button>
                            </th>
                            <th class="px-5 py-3 font-medium">Stage</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Next Round</th>
                            <th class="px-5 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($candidates as $candidate)
                            <tr wire:key="candidate-{{ $candidate['id'] }}" class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <a href="{{ route('hr.candidates.show', $candidate['id']) }}" wire:navigate class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-teal-light text-xs font-semibold text-brand-teal">
                                            {{ mb_substr($candidate['first_name'], 0, 1) }}{{ mb_substr($candidate['last_name'], 0, 1) }}
                                        </div>
                                        <span class="font-medium text-slate-900 hover:text-brand-teal">{{ $candidate['first_name'] }} {{ $candidate['last_name'] }}</span>
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-slate-600">{{ $candidate['email'] }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $candidate['phone'] }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $candidate['current_company'] ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $candidate['experience_years'] }} yrs</td>
                                <td class="px-5 py-3"><x-hr.badge :status="$candidate['stage']" /></td>
                                <td class="px-5 py-3"><x-hr.badge :status="$candidate['status']" /></td>
                                <td class="px-5 py-3 text-slate-600">
                                    @if ($candidate['next_round'])
                                        {{ $candidate['next_round'] }}
                                        <span class="block text-xs text-slate-400">{{ $candidate['next_round_date'] }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('hr.candidates.show', $candidate['id']) }}" wire:navigate title="View" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </a>
                                        <a href="{{ route('hr.candidates.edit', $candidate['id']) }}" wire:navigate title="Edit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                        </a>

                                        {{--
                                            Zero-JS dropdown via the native <details>/<summary> element (no
                                            Alpine, no click-outside handling — it stays open until toggled
                                            again, which is an accepted tradeoff for a JS-free menu).
                                        --}}
                                        <details class="relative">
                                            <summary class="flex h-8 w-8 list-none items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75h.007v.008H12V6.75Zm0 5.25h.007v.008H12V12Zm0 5.25h.007v.008H12v-.008Z" /></svg>
                                            </summary>

                                            <div class="absolute right-0 z-10 mt-1 w-48 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                                                <button type="button" wire:click="scheduleRound({{ $candidate['id'] }})" class="block w-full px-3.5 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Schedule Round</button>
                                                <button type="button" wire:click="moveToNextRound({{ $candidate['id'] }})" class="block w-full px-3.5 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Move to Next Round</button>
                                                <button type="button" wire:click="sendEmail({{ $candidate['id'] }})" class="block w-full px-3.5 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Send Email</button>
                                                <button
                                                    type="button" wire:click="reject({{ $candidate['id'] }})"
                                                    wire:confirm="Reject {{ $candidate['first_name'] }} {{ $candidate['last_name'] }}?"
                                                    class="block w-full px-3.5 py-2 text-left text-sm text-rose-600 hover:bg-rose-50"
                                                >Reject</button>
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
                {{ $candidates->links('livewire::tailwind') }}
            </div>
        @endif
    </x-hr.section-card>
</div>
