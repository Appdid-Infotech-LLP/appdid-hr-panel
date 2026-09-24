<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-semibold text-slate-900">Welcome back, HR 👋</h2>
        <p class="mt-1 text-sm text-slate-500">Here's what's happening with your recruitment pipeline today.</p>
    </div>

    {{-- Stat cards. Demo data — see Dashboard::mount(). --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($stats as $stat)
            <x-hr.stat-card :label="$stat['label']" :value="$stat['value']" :accent="$stat['accent']">
                <x-slot:icon>
                    <x-hr.icon :name="$stat['icon']" />
                </x-slot:icon>
            </x-hr.stat-card>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-hr.section-card title="Recruitment Funnel" subtitle="Candidates remaining at each stage" :demo="true">
                <div class="space-y-3">
                    @foreach ($funnel as $stage)
                        @php
                            $percent = max(8, intval($stage['count'] / $this->funnelMax * 100));
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700">{{ $stage['label'] }}</span>
                                <span class="text-slate-500">{{ $stage['count'] }}</span>
                            </div>
                            <div class="h-2.5 w-full rounded-full bg-slate-100">
                                <div class="h-2.5 rounded-full bg-brand-teal" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-hr.section-card>

            <x-hr.section-card title="Upcoming Interviews" subtitle="Next scheduled rounds across all candidates" :demo="true">
                @if (empty($upcomingInterviews))
                    <p class="py-8 text-center text-sm text-slate-500">No interviews scheduled yet.</p>
                @else
                    <div class="-mx-5 overflow-x-auto">
                        <table class="w-full min-w-160 text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-xs tracking-wide text-slate-500 uppercase">
                                    <th class="px-5 py-2 font-medium">Candidate</th>
                                    <th class="px-5 py-2 font-medium">Round</th>
                                    <th class="px-5 py-2 font-medium">Date &amp; Time</th>
                                    <th class="px-5 py-2 font-medium">Mode</th>
                                    <th class="px-5 py-2 font-medium">Interviewer</th>
                                    <th class="px-5 py-2 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($upcomingInterviews as $interview)
                                    <tr>
                                        <td class="px-5 py-3 font-medium text-slate-900">{{ $interview['candidate'] }}</td>
                                        <td class="px-5 py-3 text-slate-600">{{ $interview['round'] }}</td>
                                        <td class="px-5 py-3 text-slate-600">{{ $interview['date'] }} · {{ $interview['time'] }}</td>
                                        <td class="px-5 py-3"><x-hr.badge :status="$interview['mode']" /></td>
                                        <td class="px-5 py-3 text-slate-600">{{ $interview['interviewer'] }}</td>
                                        <td class="px-5 py-3"><x-hr.badge :status="$interview['status']" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-hr.section-card>
        </div>

        <div class="space-y-6">
            <x-hr.section-card title="Recent Candidates" :demo="true">
                @if (empty($recentCandidates))
                    <p class="py-8 text-center text-sm text-slate-500">No candidates added yet.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($recentCandidates as $candidate)
                            <li class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-teal-light text-xs font-semibold text-brand-teal">
                                    {{ collect(explode(' ', $candidate['name']))->map(fn ($part) => mb_substr($part, 0, 1))->join('') }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-900">{{ $candidate['name'] }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $candidate['role'] }} · {{ $candidate['added'] }}</p>
                                </div>
                                <x-hr.badge :status="$candidate['stage']" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-hr.section-card>

            <x-hr.section-card title="Recent Activity" :demo="true">
                @if (empty($recentActivity))
                    <p class="py-8 text-center text-sm text-slate-500">No activity yet.</p>
                @else
                    <ul class="space-y-4">
                        @foreach ($recentActivity as $activity)
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-teal"></span>
                                <div class="min-w-0">
                                    <p class="text-sm text-slate-700">{{ $activity['text'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $activity['time'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-hr.section-card>
        </div>
    </div>
</div>
