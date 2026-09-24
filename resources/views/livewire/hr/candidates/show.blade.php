@php
    $fullName = $candidate['first_name'].' '.$candidate['last_name'];
    $initials = mb_substr($candidate['first_name'], 0, 1).mb_substr($candidate['last_name'], 0, 1);
@endphp

<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('hr.candidates.index') }}" wire:navigate class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="text-xl font-semibold text-slate-900">Candidate Profile</h2>
    </div>

    {{-- Header --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-brand-teal-light text-lg font-semibold text-brand-teal">
                    {{ $initials }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $fullName }}</h3>
                        <x-hr.badge :status="$candidate['stage']" />
                        <x-hr.badge :status="$candidate['status']" />
                    </div>
                    <p class="mt-1 text-sm text-slate-500">{{ $candidate['current_designation'] }} @if($candidate['current_company']) · {{ $candidate['current_company'] }} @endif</p>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                            {{ $candidate['email'] }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                            {{ $candidate['phone'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-2">
                <button type="button" wire:click="downloadResume" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3.5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 12m0 0 4.5-4.5M12 12V3" /></svg>
                    Resume
                </button>
                <a href="{{ route('hr.candidates.edit', $candidate['id']) }}" wire:navigate class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3.5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                    Edit
                </a>
                <button type="button" wire:click="sendEmail" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-teal px-3.5 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                    Send Email
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-hr.section-card title="Personal Information">
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div><dt class="text-xs text-slate-400">Date of Birth</dt><dd class="mt-0.5 text-sm text-slate-700">{{ \Illuminate\Support\Carbon::parse($candidate['date_of_birth'])->format('M j, Y') }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Gender</dt><dd class="mt-0.5 text-sm text-slate-700">{{ $candidate['gender'] }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Alternate Phone</dt><dd class="mt-0.5 text-sm text-slate-700">{{ $candidate['alternate_phone'] ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Current Location</dt><dd class="mt-0.5 text-sm text-slate-700">{{ $candidate['location'] }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs text-slate-400">Address</dt><dd class="mt-0.5 text-sm text-slate-700">{{ $candidate['address'] }}</dd></div>
                </dl>
            </x-hr.section-card>

            <x-hr.section-card title="Professional Information">
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div><dt class="text-xs text-slate-400">Highest Qualification</dt><dd class="mt-0.5 text-sm text-slate-700">{{ $candidate['highest_qualification'] }}</dd></div>
                    <div><dt class="text-xs text-slate-400">College / University</dt><dd class="mt-0.5 text-sm text-slate-700">{{ $candidate['college'] }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Experience</dt><dd class="mt-0.5 text-sm text-slate-700">{{ $candidate['experience_years'] }} years</dd></div>
                    <div><dt class="text-xs text-slate-400">Notice Period</dt><dd class="mt-0.5 text-sm text-slate-700">{{ $candidate['notice_period'] }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Current Salary</dt><dd class="mt-0.5 text-sm text-slate-700">₹{{ $candidate['current_salary'] ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Expected Salary</dt><dd class="mt-0.5 text-sm text-slate-700">₹{{ $candidate['expected_salary'] }}</dd></div>
                </dl>

                <div class="mt-5">
                    <dt class="text-xs text-slate-400">Skills</dt>
                    <dd class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach ($candidate['skills'] as $skill)
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $skill }}</span>
                        @endforeach
                    </dd>
                </div>
            </x-hr.section-card>

            <x-hr.section-card title="Links">
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-slate-400">LinkedIn</dt>
                        <dd class="mt-0.5 text-sm">
                            @if ($candidate['linkedin_url'])
                                <a href="{{ $candidate['linkedin_url'] }}" target="_blank" rel="noopener" class="text-brand-teal hover:underline">{{ $candidate['linkedin_url'] }}</a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Portfolio</dt>
                        <dd class="mt-0.5 text-sm">
                            @if ($candidate['portfolio_url'])
                                <a href="{{ $candidate['portfolio_url'] }}" target="_blank" rel="noopener" class="text-brand-teal hover:underline">{{ $candidate['portfolio_url'] }}</a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-hr.section-card>

            <x-hr.section-card title="Resume">
                <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="flex items-center gap-3 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="font-medium text-slate-700">{{ $candidate['resume_filename'] }}</span>
                        <span class="text-slate-400">({{ $candidate['resume_size_kb'] }} KB)</span>
                    </div>
                    <button type="button" wire:click="downloadResume" class="text-sm font-medium text-brand-teal hover:text-brand-teal-dark">Download</button>
                </div>
                <p class="mt-2 text-xs text-slate-400">Preview isn't wired up yet — this button just needs a working download route.</p>
            </x-hr.section-card>

            @if ($candidate['notes'])
                <x-hr.section-card title="Notes">
                    <p class="text-sm text-slate-600">{{ $candidate['notes'] }}</p>
                </x-hr.section-card>
            @endif
        </div>

        <div class="space-y-6">
            <x-hr.section-card title="Quick Actions">
                <div class="space-y-2">
                    <a href="{{ route('hr.rounds.schedule', ['candidateId' => $candidate['id']]) }}" wire:navigate class="block w-full rounded-lg border border-slate-300 px-3.5 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Schedule Round
                    </a>
                    <button type="button" wire:click="moveToNextRound" class="w-full rounded-lg bg-brand-teal px-3.5 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark">
                        Move to Next Round
                    </button>
                    <button
                        type="button" wire:click="reject" wire:confirm="Reject {{ $fullName }}? This cannot be undone."
                        class="w-full rounded-lg border border-rose-200 px-3.5 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50"
                    >
                        Reject Candidate
                    </button>
                </div>
            </x-hr.section-card>

            <x-hr.section-card title="Recruitment Timeline">
                <x-hr.candidate-timeline :stage="$candidate['stage']" :status="$candidate['status']" :rounds="$candidate['rounds']" />
            </x-hr.section-card>
        </div>
    </div>
</div>
