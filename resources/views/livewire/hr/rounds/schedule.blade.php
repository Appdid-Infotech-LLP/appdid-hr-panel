<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('hr.rounds.index') }}" wire:navigate
            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Schedule Round</h2>
            <p class="text-sm text-slate-500">
                @if ($this->selectedCandidateName())
                    Scheduling a round for <span
                        class="font-medium text-slate-700">{{ $this->selectedCandidateName() }}</span>.
                @else
                    Pick a candidate and set up their next round.
                @endif
            </p>
        </div>
    </div>

    <form wire:submit="scheduleRound">
        <x-hr.section-card>
            <div class="space-y-5">
                <x-hr.select2 name="candidateId" label="Candidate" :options="$this->candidates()" :value="$candidateId"
                    placeholder="Select candidate" />

                <x-hr.select name="roundType" label="Round" :options="$this->roundTypes()" placeholder="Select round type"
                    required />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-hr.datepicker name="date" label="Date" :value="$date" />

                    <div>
                        <label for="time" class="mb-1.5 block text-sm font-medium text-slate-700">Time</label>
                        <input type="time" id="time" wire:model="time"
                            class="w-full rounded-lg border px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2
                                @error('time') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @else border-slate-300 focus:border-brand-teal focus:ring-brand-teal/20 @enderror">

                        @error('time')
                            <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <span class="mb-1.5 block text-sm font-medium text-slate-700">Mode</span>
                    <div class="inline-flex rounded-lg border border-slate-300 p-1">
                        @foreach (['Virtual', 'In Person'] as $option)
                            <button type="button" wire:click="$set('mode', '{{ $option }}')"
                                @class([
                                    'rounded-md px-4 py-1.5 text-sm font-medium transition-colors',
                                    'bg-brand-teal text-white' => $mode === $option,
                                    'text-slate-600 hover:bg-slate-50' => $mode !== $option,
                                ])>{{ $option }}</button>
                        @endforeach
                    </div>
                </div>

                @if ($mode === 'Virtual')
                    <p class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">
                        A Google Meet link will be generated automatically once this round is scheduled.
                    </p>
                @endif

                <x-hr.select2 name="interviewer" label="Interviewer" :options="$this->interviewers()" :value="$interviewer"
                    placeholder="Select interviewer" />

                <x-hr.textarea name="notes" label="Notes" placeholder="e.g. Please keep your resume handy..."
                    hint="Included in the email and calendar invite sent to the candidate — don't put internal comments here."
                    hint-icon="mail" :rows="3" />

                <x-hr.textarea name="feedback" label="Feedback" placeholder="Interviewer's internal feedback..."
                    hint="Internal only — never included in the email or calendar invite." hint-icon="lock"
                    :rows="3" />
            </div>
        </x-hr.section-card>

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('hr.rounds.index') }}" wire:navigate
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-5 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark disabled:opacity-60"
                wire:loading.attr="disabled" wire:target="scheduleRound">
                <span wire:loading.remove wire:target="scheduleRound">Schedule Round</span>
                <span wire:loading wire:target="scheduleRound">Scheduling...</span>
            </button>
        </div>
    </form>
</div>
