<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('hr.candidates.show', $candidateId) }}" wire:navigate class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Edit Round</h2>
            <p class="text-sm text-slate-500">{{ $roundType }} for <span class="font-medium text-slate-700">{{ $candidateName }}</span></p>
        </div>
    </div>

    <form wire:submit="updateRound">
        <x-hr.section-card>
            <div class="space-y-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <span class="mb-1.5 block text-sm font-medium text-slate-700">Candidate</span>
                        <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $candidateName }}</p>
                    </div>
                    <div>
                        <span class="mb-1.5 block text-sm font-medium text-slate-700">Round</span>
                        <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $roundType }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-hr.datepicker name="date" label="Date" :value="$date" />

                    <div>
                        <label for="time" class="mb-1.5 block text-sm font-medium text-slate-700">Time</label>
                        <input
                            type="time" id="time" wire:model="time"
                            @class([
                                'w-full rounded-lg border px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2',
                                'border-rose-300 focus:border-rose-400 focus:ring-rose-100' => $errors->has('time'),
                                'border-slate-300 focus:border-brand-teal focus:ring-brand-teal/20' => ! $errors->has('time'),
                            ])
                        >
                        @error('time')
                            <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <span class="mb-1.5 block text-sm font-medium text-slate-700">Mode</span>
                    <div class="inline-flex rounded-lg border border-slate-300 p-1">
                        @foreach (['Virtual', 'In Person'] as $option)
                            <button
                                type="button" wire:click="$set('mode', '{{ $option }}')"
                                @class([
                                    'rounded-md px-4 py-1.5 text-sm font-medium transition-colors',
                                    'bg-brand-teal text-white' => $mode === $option,
                                    'text-slate-600 hover:bg-slate-50' => $mode !== $option,
                                ])
                            >{{ $option }}</button>
                        @endforeach
                    </div>
                </div>

                @if ($mode === 'Virtual')
                    <div>
                        <span class="mb-1.5 block text-sm font-medium text-slate-700">Meeting Link</span>
                        @if ($meetingLink)
                            <a href="{{ $meetingLink }}" target="_blank" class="block truncate rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-brand-teal hover:underline">{{ $meetingLink }}</a>
                        @else
                            <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">Generated automatically once synced to Google Calendar.</p>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-hr.select2 name="interviewer" label="Interviewer" :options="$this->interviewers()" :value="$interviewer" placeholder="Select interviewer" />
                    <x-hr.select name="status" label="Status" :options="$this->statuses()" placeholder="Select status" />
                </div>

                <x-hr.textarea name="notes" label="Notes" :rows="3" />
            </div>
        </x-hr.section-card>

        <x-hr.section-card title="Google Calendar" class="mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <x-hr.badge :status="$calendarStatus" />
                        @if ($calendarEventId)
                            <span class="text-xs text-slate-400">Event ID: {{ $calendarEventId }}</span>
                        @endif
                    </div>
                    @unless ($this->calendarConnected())
                        <p class="mt-1.5 text-xs text-amber-600">
                            Your Google Calendar isn't connected.
                            <a href="{{ route('hr.settings') }}" wire:navigate class="font-medium underline hover:text-amber-700">Connect it in Settings</a>
                            to sync this round.
                        </p>
                    @endunless
                </div>

                <button
                    type="button" wire:click="createCalendarEvent"
                    class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                    wire:loading.attr="disabled" wire:target="createCalendarEvent"
                    @disabled(! $this->calendarConnected())
                >
                    <span wire:loading.remove wire:target="createCalendarEvent">{{ $calendarStatus === 'Synced' ? 'Resync Event' : 'Add to Google Calendar' }}</span>
                    <span wire:loading wire:target="createCalendarEvent">Syncing...</span>
                </button>
            </div>
        </x-hr.section-card>

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('hr.candidates.show', $candidateId) }}" wire:navigate class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-5 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark disabled:opacity-60" wire:loading.attr="disabled" wire:target="updateRound">
                <span wire:loading.remove wire:target="updateRound">Save Changes</span>
                <span wire:loading wire:target="updateRound">Saving...</span>
            </button>
        </div>
    </form>

    <x-hr.modal :show="$showRescheduleConfirmation" title="Reschedule this round?">
        <div class="flex gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>

            <div class="min-w-0 flex-1 space-y-4">
                <p class="text-sm text-slate-600">
                    You're changing the date or time of <span class="font-medium text-slate-900">{{ $candidateName }}</span>'s {{ $roundType }}.
                    The round will be marked <x-hr.badge status="Rescheduled" /> and the candidate will be emailed the new schedule.
                </p>

                @if ($showRescheduleConfirmation)
                    <div class="grid grid-cols-1 gap-2 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm sm:grid-cols-[1fr_auto_1fr] sm:items-center">
                        <div>
                            <p class="text-xs font-medium tracking-wide text-slate-400 uppercase">Currently</p>
                            <p class="mt-0.5 text-slate-500 line-through">{{ \Illuminate\Support\Carbon::parse($originalScheduleAt)->format('M j, Y · h:i A') }}</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4 text-slate-400 sm:block" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                        <div>
                            <p class="text-xs font-medium tracking-wide text-slate-400 uppercase">New</p>
                            <p class="mt-0.5 font-medium text-slate-900">{{ \Illuminate\Support\Carbon::parse("{$date} {$time}")->format('M j, Y · h:i A') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <x-slot:footer>
            <button type="button" wire:click="close"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Go Back
            </button>
            <button type="button" wire:click="confirmReschedule" wire:loading.attr="disabled" wire:target="confirmReschedule"
                class="inline-flex items-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 disabled:opacity-60">
                <span wire:loading.remove wire:target="confirmReschedule">Yes, Reschedule</span>
                <span wire:loading wire:target="confirmReschedule">Rescheduling...</span>
            </button>
        </x-slot:footer>
    </x-hr.modal>
</div>
