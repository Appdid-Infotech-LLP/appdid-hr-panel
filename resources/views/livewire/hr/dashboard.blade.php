<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Welcome back, {{ $greetingName }} 👋</h2>
            <p class="mt-1 text-sm text-slate-500">
                @if ($monthLabel)
                    Showing candidates added and rounds scheduled in <span class="font-medium text-slate-700">{{ $monthLabel }}</span>.
                @else
                    Here's what's happening with your recruitment pipeline today.
                @endif
            </p>
        </div>

        {{-- Scopes the entire dashboard; clearing it goes back to all time. --}}
        <x-hr.select2 name="month" :options="$monthOptions" :value="$month" placeholder="All time" class="w-full sm:w-56" />
    </div>

    {{-- Previous render stays visible (dimmed) while a new period loads. --}}
    <div class="space-y-6 transition-opacity" wire:loading.class="opacity-60" wire:target="month, weeks">
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($stats as $stat)
            <x-hr.stat-card :label="$stat['label']" :value="$stat['value']" :accent="$stat['accent']">
                <x-slot:icon>
                    <x-hr.icon :name="$stat['icon']" />
                </x-slot:icon>
            </x-hr.stat-card>
        @endforeach
    </div>

    {{-- Analytics: scoped by the month picker above; in all-time view the weeks toggle sets the span. --}}
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Analytics</h3>
                <p class="text-xs text-slate-500">{{ $monthLabel ? 'Day by day through '.$monthLabel : 'Weekly trends for the selected period' }}</p>
            </div>

            @if (! $monthLabel)
            <div class="inline-flex rounded-lg border border-slate-200 bg-white p-0.5 text-xs font-medium">
                @foreach ($weekRanges as $range)
                    <button type="button" wire:click="$set('weeks', {{ $range }})" @class([
                        'rounded-md px-3 py-1.5 transition-colors',
                        'bg-brand-teal text-white' => $weeks === $range,
                        'text-slate-600 hover:bg-slate-50' => $weeks !== $range,
                    ])>
                        {{ $range }} weeks
                    </button>
                @endforeach
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-hr.section-card title="Candidates Added" :subtitle="$candidatesAddedTotal.' added '.$periodLabel">
                <x-hr.chart.columns :columns="$candidatesPerBucket" :series="[['name' => 'Candidates added', 'color' => 'var(--color-brand-teal)']]" :first-header="$monthLabel ? 'Day' : 'Week'" />
            </x-hr.section-card>

            <x-hr.section-card title="Interviews by Round" :subtitle="$interviewsTotal.' rounds scheduled '.$periodLabel">
                <x-hr.chart.columns :columns="$interviewsPerBucket" :series="$roundTypeSeries" :first-header="$monthLabel ? 'Day' : 'Week'" />
            </x-hr.section-card>

            <x-hr.section-card title="Round Outcomes" subtitle="Rounds in this period by current status">
                <x-hr.chart.bars :rows="$roundOutcomes" empty="No rounds scheduled in this period." />
            </x-hr.section-card>

            <x-hr.section-card title="Candidates by Location" subtitle="Where candidates added in this period are based">
                <x-hr.chart.bars :rows="$candidatesByLocation" empty="No candidates added in this period." />
            </x-hr.section-card>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-hr.section-card title="Recruitment Funnel" :subtitle="$monthLabel ? 'Where candidates added in '.$monthLabel.' are now' : 'Candidates currently at each stage'">
                <div class="space-y-3">
                    @foreach ($funnel as $stage)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700">{{ $stage['label'] }}</span>
                                <span class="text-slate-500">{{ $stage['count'] }}</span>
                            </div>
                            <div class="h-2.5 w-full rounded-full bg-slate-100">
                                <div class="h-2.5 rounded-full bg-brand-teal" style="width: {{ $stage['percent'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-hr.section-card>

            <x-hr.section-card :title="$monthLabel ? 'Interviews in '.$monthLabel : 'Upcoming Interviews'" :subtitle="$monthLabel ? 'Earliest rounds scheduled this month' : 'Next scheduled rounds across all candidates'">
                <x-slot:actions>
                    <a href="{{ $monthRange ? route('hr.rounds.index', ['dateFrom' => $monthRange[0]->toDateString(), 'dateTo' => $monthRange[1]->toDateString()]) : route('hr.rounds.index') }}" wire:navigate class="text-xs font-medium text-brand-teal hover:underline">View all</a>
                </x-slot:actions>

                @if (empty($interviews))
                    <p class="py-8 text-center text-sm text-slate-500">{{ $monthLabel ? 'No interviews scheduled in '.$monthLabel.'.' : 'No interviews scheduled yet.' }}</p>
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
                                @foreach ($interviews as $interview)
                                    <tr>
                                        <td class="px-5 py-3 font-medium text-slate-900">
                                            <a href="{{ route('hr.candidates.show', $interview['candidate_id']) }}" wire:navigate class="hover:text-brand-teal">{{ $interview['candidate'] }}</a>
                                        </td>
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
            <x-hr.section-card :title="$monthLabel ? 'Added in '.$monthLabel : 'Recent Candidates'">
                <x-slot:actions>
                    <a href="{{ route('hr.candidates.index') }}" wire:navigate class="text-xs font-medium text-brand-teal hover:underline">View all</a>
                </x-slot:actions>

                @if (empty($recentCandidates))
                    <p class="py-8 text-center text-sm text-slate-500">{{ $monthLabel ? 'No candidates added in '.$monthLabel.'.' : 'No candidates added yet.' }}</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($recentCandidates as $candidate)
                            <li class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-teal-light text-xs font-semibold text-brand-teal">
                                    {{ $candidate['initials'] }}
                                </div>
                                <a href="{{ route('hr.candidates.show', $candidate['id']) }}" wire:navigate class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-900">{{ $candidate['name'] }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $candidate['role'] }} · {{ $candidate['added'] }}</p>
                                </a>
                                <x-hr.badge :status="$candidate['stage']" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-hr.section-card>

            <x-hr.section-card :title="$monthLabel ? 'Activity in '.$monthLabel : 'Recent Activity'">
                @if (empty($recentActivity))
                    <p class="py-8 text-center text-sm text-slate-500">{{ $monthLabel ? 'No activity in '.$monthLabel.'.' : 'No activity yet.' }}</p>
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
</div>
