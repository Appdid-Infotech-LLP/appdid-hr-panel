<?php

namespace App\Livewire\Hr;

use App\Enums\RoundStatus;
use App\Enums\RoundType;
use App\Models\Candidate;
use App\Models\CandidateRound;
use App\Support\RoundStatusResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    protected const LIST_LIMIT = 5;

    protected const WEEK_RANGES = [4, 12, 26];

    /** Years before the current one offered in the picker, even with no data in them. */
    protected const PAST_YEARS = 5;

    /**
     * Year ("2026") and month ("01"–"12") that scope the whole dashboard.
     * A year alone covers that whole year; both blank is all time. With a
     * period picked, candidate figures cover candidates *added* in it and
     * round figures cover rounds *scheduled* in it.
     */
    #[Url]
    public string $year = '';

    #[Url]
    public string $month = '';

    /** All-time view only: how many weeks the analytics charts span. */
    #[Url]
    public int $weeks = 12;

    public function mount(): void
    {
        // Older links carried the period as a single ?month=Y-m.
        if (preg_match('/^(\d{4})-(\d{2})$/', $this->month, $matches)) {
            [, $this->year, $this->month] = $matches;
        }
    }

    /** Picking a month with no year means that month this year. */
    public function updatedMonth(): void
    {
        if ($this->month !== '' && $this->year === '') {
            $this->year = (string) now()->year;
        }
    }

    /** Clearing the year goes back to all time. */
    public function updatedYear(): void
    {
        if ($this->year === '') {
            $this->month = '';
        }
    }

    /**
     * The picked period's [start, end], or null for all time. A hand-edited
     * year that isn't real falls back to all time, a bad month to the year.
     *
     * @return array{Carbon, Carbon}|null
     */
    protected function periodRange(): ?array
    {
        if (! preg_match('/^\d{4}$/', $this->year)) {
            $this->year = '';
            $this->month = '';

            return null;
        }

        if (! array_key_exists($this->month, $this->monthOptions())) {
            $this->month = '';
        }

        if ($this->month === '') {
            $start = Carbon::create((int) $this->year)->startOfYear();

            return [$start, $start->copy()->endOfYear()];
        }

        $start = Carbon::create((int) $this->year, (int) $this->month)->startOfMonth();

        return [$start, $start->copy()->endOfMonth()];
    }

    /**
     * Every year from whichever is earlier of the first candidate/round or
     * PAST_YEARS ago, up to whichever is later of now or the last scheduled
     * round, newest first.
     *
     * @return array<string, string>
     */
    protected function yearOptions(): array
    {
        $earliest = collect([Candidate::min('created_at'), CandidateRound::min('schedule_at')])
            ->filter()
            ->map(fn (string $date): int => Carbon::parse($date)->year)
            ->push(now()->year - self::PAST_YEARS)
            ->min();

        $lastRound = CandidateRound::max('schedule_at');
        $latest = max(now()->year, $lastRound ? Carbon::parse($lastRound)->year : 0);

        return collect(range($latest, $earliest))
            ->mapWithKeys(fn (int $year): array => [(string) $year => (string) $year])
            ->all();
    }

    /**
     * @return array<string, string> "01" => "January"
     */
    protected function monthOptions(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month): array => [sprintf('%02d', $month) => Carbon::create(2000, $month)->format('F')])
            ->all();
    }

    /**
     * @param  array{Carbon, Carbon}|null  $range
     */
    protected function candidatesIn(?array $range): Builder
    {
        return Candidate::query()->when($range, fn (Builder $query) => $query->whereBetween('created_at', $range));
    }

    /**
     * Candidates in each stage — the same grouping the Pipeline board uses,
     * so (for all time) the dashboard and the board always agree.
     *
     * @param  array{Carbon, Carbon}|null  $range
     * @return Collection<string, int>
     */
    protected function stageCounts(?array $range): Collection
    {
        return $this->candidatesIn($range)
            ->selectRaw('current_stage, count(*) as total')
            ->groupBy('current_stage')
            ->pluck('total', 'current_stage');
    }

    /**
     * @return list<array{label: string, value: int, accent: string, icon: string}>
     */
    protected function stats(Collection $stageCounts, bool $monthly): array
    {
        return [
            ['label' => $monthly ? 'Candidates Added' : 'Total Candidates', 'value' => $stageCounts->sum(), 'accent' => 'teal', 'icon' => 'users'],
            ['label' => 'New Candidates', 'value' => $stageCounts->get('New', 0), 'accent' => 'slate', 'icon' => 'user-plus'],
            ['label' => RoundType::Hr->value, 'value' => $stageCounts->get(RoundType::Hr->value, 0), 'accent' => 'teal', 'icon' => 'clock'],
            ['label' => RoundType::Task->value, 'value' => $stageCounts->get(RoundType::Task->value, 0), 'accent' => 'teal', 'icon' => 'clipboard'],
            ['label' => RoundType::Technical->value, 'value' => $stageCounts->get(RoundType::Technical->value, 0), 'accent' => 'teal', 'icon' => 'code'],
            ['label' => RoundType::Final->value, 'value' => $stageCounts->get(RoundType::Final->value, 0), 'accent' => 'yellow', 'icon' => 'flag'],
            ['label' => 'Selected', 'value' => $stageCounts->get('Selected', 0), 'accent' => 'green', 'icon' => 'check'],
            ['label' => 'Rejected', 'value' => $stageCounts->get('Rejected', 0), 'accent' => 'red', 'icon' => 'x'],
        ];
    }

    /**
     * Bars are sized relative to the widest stage (the total, in practice).
     *
     * @return list<array{label: string, count: int, percent: int}>
     */
    protected function funnel(Collection $stageCounts): array
    {
        $stages = [
            ['label' => 'Candidates', 'count' => $stageCounts->sum()],
            ...array_map(
                fn (RoundType $type): array => ['label' => $type->value, 'count' => $stageCounts->get($type->value, 0)],
                RoundType::cases(),
            ),
            ['label' => 'Selected', 'count' => $stageCounts->get('Selected', 0)],
        ];

        $max = max(1, ...array_column($stages, 'count'));

        return array_map(
            fn (array $stage): array => $stage + ['percent' => $stage['count'] > 0 ? max(8, intval($stage['count'] / $max * 100)) : 0],
            $stages,
        );
    }

    /**
     * All time: future rounds that are still live ("live" uses the same
     * effective status as the Rounds list, so a rejected/selected
     * candidate's or a completed/cancelled round is skipped).
     * A month: every round scheduled in it, whatever its status.
     *
     * @param  array{Carbon, Carbon}|null  $range
     * @return list<array<string, mixed>>
     */
    protected function interviews(?array $range): array
    {
        [$statusSql, $statusBindings] = RoundStatusResolver::sqlExpression();

        return CandidateRound::query()
            ->join('candidates', 'candidate_rounds.candidate_id', '=', 'candidates.id')
            ->select('candidate_rounds.*')
            ->selectRaw("{$statusSql} as computed_status", $statusBindings)
            ->with(['candidate', 'interviewer'])
            ->when(
                $range,
                fn (Builder $query) => $query->whereBetween('candidate_rounds.schedule_at', $range),
                fn (Builder $query) => $query
                    ->where('candidate_rounds.schedule_at', '>=', now())
                    ->whereRaw("({$statusSql}) not in (?, ?)", [...$statusBindings, 'Completed', 'Cancelled']),
            )
            ->orderBy('candidate_rounds.schedule_at')
            ->limit(self::LIST_LIMIT)
            ->get()
            ->map(fn (CandidateRound $round): array => [
                'candidate_id' => $round->candidate_id,
                'candidate' => $this->fullName($round->candidate),
                'round' => $round->type,
                'date' => $round->schedule_at->format('M j, Y'),
                'time' => $round->schedule_at->format('h:i A'),
                'mode' => $round->mode,
                'interviewer' => $round->interviewer?->name ?? '—',
                'status' => $round->computed_status,
            ])
            ->all();
    }

    /**
     * @param  array{Carbon, Carbon}|null  $range
     * @return list<array<string, mixed>>
     */
    protected function recentCandidates(?array $range): array
    {
        return $this->candidatesIn($range)
            ->latest()
            ->limit(self::LIST_LIMIT)
            ->get()
            ->map(fn (Candidate $candidate): array => [
                'id' => $candidate->id,
                'name' => $this->fullName($candidate),
                'initials' => mb_strtoupper(mb_substr($candidate->first_name, 0, 1).mb_substr($candidate->last_name, 0, 1)),
                'role' => $candidate->current_designation ?: 'Role not set',
                'stage' => $candidate->current_stage,
                'added' => $range ? $candidate->created_at->format('M j') : $candidate->created_at->diffForHumans(),
            ])
            ->all();
    }

    /**
     * There's no audit log table, so activity is derived from what the data
     * itself records: candidates being added, rounds being scheduled, and
     * rounds being completed/cancelled (stamped by the row's updated_at).
     *
     * @param  array{Carbon, Carbon}|null  $range
     * @return list<array{text: string, time: string}>
     */
    protected function recentActivity(?array $range): array
    {
        $limit = self::LIST_LIMIT;
        $within = fn (string $column) => fn (Builder $query) => $query->when($range, fn (Builder $query) => $query->whereBetween($column, $range));

        $added = Candidate::query()->tap($within('created_at'))->latest()->limit($limit)->get()
            ->map(fn (Candidate $candidate): array => [
                'at' => $candidate->created_at,
                'text' => "{$this->fullName($candidate)} was added as a new candidate",
            ]);

        $scheduled = CandidateRound::query()->with('candidate')->tap($within('created_at'))->latest()->limit($limit)->get()
            ->map(fn (CandidateRound $round): array => [
                'at' => $round->created_at,
                'text' => "{$this->fullName($round->candidate)} was scheduled for {$round->type}",
            ]);

        $concluded = CandidateRound::query()->with('candidate')
            ->whereIn('status', ['Completed', 'Cancelled'])
            ->tap($within('updated_at'))
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (CandidateRound $round): array => [
                'at' => $round->updated_at,
                'text' => "{$this->fullName($round->candidate)}'s {$round->type} was ".strtolower($round->status),
            ]);

        return $added->concat($scheduled)->concat($concluded)
            ->sortByDesc('at')
            ->take($limit)
            ->map(fn (array $activity): array => [
                'text' => $activity['text'],
                'time' => $range ? $activity['at']->format('M j, g:i A') : $activity['at']->diffForHumans(),
            ])
            ->values()
            ->all();
    }

    /**
     * What the analytics charts span and how they're bucketed: a picked
     * year is shown month by month, a picked month day by day; all time is
     * the last $weeks weeks (Monday-based, including the current one),
     * week by week.
     *
     * @param  array{Carbon, Carbon}|null  $range
     * @return array{start: Carbon, end: Carbon, buckets: Collection<string, array{label: string, title: string}>, key: \Closure(Carbon): string}
     */
    protected function chartWindow(?array $range): array
    {
        if ($range && $this->month === '') {
            [$start, $end] = $range;

            return [
                'start' => $start,
                'end' => $end,
                'buckets' => collect(range(0, 11))->mapWithKeys(function (int $offset) use ($start): array {
                    $month = $start->copy()->addMonths($offset);

                    return [$month->format('Y-m') => ['label' => $month->format('M'), 'title' => $month->format('F Y')]];
                }),
                'key' => fn (Carbon $at): string => $at->format('Y-m'),
            ];
        }

        if ($range) {
            [$start, $end] = $range;

            return [
                'start' => $start,
                'end' => $end,
                'buckets' => collect(range(0, $start->daysInMonth - 1))->mapWithKeys(function (int $offset) use ($start): array {
                    $day = $start->copy()->addDays($offset);

                    return [$day->toDateString() => ['label' => $day->format('j'), 'title' => $day->format('D, M j')]];
                }),
                'key' => fn (Carbon $at): string => $at->toDateString(),
            ];
        }

        if (! in_array($this->weeks, self::WEEK_RANGES, true)) {
            $this->weeks = 12;
        }

        $start = now()->startOfWeek()->subWeeks($this->weeks - 1);

        return [
            'start' => $start,
            'end' => now()->endOfWeek(),
            'buckets' => collect(range(0, $this->weeks - 1))->mapWithKeys(function (int $offset) use ($start): array {
                $week = $start->copy()->addWeeks($offset);

                return [$week->toDateString() => ['label' => $week->format('M j'), 'title' => 'Week of '.$week->format('M j, Y')]];
            }),
            'key' => fn (Carbon $at): string => $at->copy()->startOfWeek()->toDateString(),
        ];
    }

    /**
     * @return list<array{label: string, title: string, values: array<string, int>}>
     */
    protected function candidatesPerBucket(array $window): array
    {
        $added = Candidate::query()
            ->whereBetween('created_at', [$window['start'], $window['end']])
            ->pluck('created_at')
            ->countBy($window['key']);

        return $window['buckets']
            ->map(fn (array $bucket, string $key): array => $bucket + ['values' => ['Candidates added' => $added->get($key, 0)]])
            ->values()
            ->all();
    }

    /**
     * Rounds by when they're scheduled (so the current period also counts
     * what's still coming up), split by round type.
     *
     * @return list<array{label: string, title: string, values: array<string, int>}>
     */
    protected function interviewsPerBucket(array $window): array
    {
        $rounds = CandidateRound::query()
            ->whereBetween('schedule_at', [$window['start'], $window['end']])
            ->get(['type', 'schedule_at'])
            ->groupBy(fn (CandidateRound $round): string => $window['key']($round->schedule_at));

        return $window['buckets']
            ->map(function (array $bucket, string $key) use ($rounds): array {
                $byType = $rounds->get($key, collect())->countBy('type');

                return $bucket + [
                    'values' => collect(RoundType::cases())
                        ->mapWithKeys(fn (RoundType $type): array => [$type->value => $byType->get($type->value, 0)])
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Round types are an ordered sequence, so they share one teal ramp,
     * light (HR) to dark (Final), rather than unrelated hues. Validated as
     * an ordinal ramp against the white card surface.
     *
     * @return list<array{name: string, color: string}>
     */
    protected function roundTypeSeries(): array
    {
        $ramp = ['#7fbfbc', '#3f9f9b', '#0f7a78', '#0b4f4d'];

        return array_map(
            fn (RoundType $type, string $color): array => ['name' => $type->value, 'color' => $color],
            RoundType::cases(),
            $ramp,
        );
    }

    /**
     * Rounds scheduled in the window, by the same effective status the
     * Rounds list shows.
     *
     * @return list<array{label: string, value: int}>
     */
    protected function roundOutcomes(array $window): array
    {
        [$statusSql, $statusBindings] = RoundStatusResolver::sqlExpression();

        $counts = CandidateRound::query()
            ->join('candidates', 'candidate_rounds.candidate_id', '=', 'candidates.id')
            ->whereBetween('candidate_rounds.schedule_at', [$window['start'], $window['end']])
            ->selectRaw("{$statusSql} as computed_status, count(*) as total", $statusBindings)
            ->groupBy('computed_status')
            ->pluck('total', 'computed_status');

        return array_map(
            fn (RoundStatus $status): array => ['label' => $status->value, 'value' => (int) $counts->get($status->value, 0)],
            RoundStatus::cases(),
        );
    }

    /**
     * Where candidates added in the window are based — top five, the rest
     * folded into "Other" so the chart stays readable.
     *
     * @return list<array{label: string, value: int}>
     */
    protected function candidatesByLocation(array $window): array
    {
        $counts = Candidate::query()
            ->whereBetween('created_at', [$window['start'], $window['end']])
            ->pluck('location')
            ->map(fn (?string $location): string => trim((string) $location) ?: 'Not specified')
            ->countBy()
            ->sortDesc();

        $rows = $counts->take(5)
            ->map(fn (int $count, string $location): array => ['label' => $location, 'value' => $count])
            ->values();

        if ($counts->count() > 5) {
            $rows->push(['label' => 'Other', 'value' => $counts->skip(5)->sum()]);
        }

        return $rows->all();
    }

    protected function fullName(Candidate $candidate): string
    {
        return trim("{$candidate->first_name} {$candidate->last_name}");
    }

    public function render()
    {
        $range = $this->periodRange();
        $periodName = $range ? ($this->month === '' ? $this->year : $range[0]->format('F Y')) : null;
        $stageCounts = $this->stageCounts($range);
        $window = $this->chartWindow($range);

        $candidatesPerBucket = $this->candidatesPerBucket($window);
        $interviewsPerBucket = $this->interviewsPerBucket($window);
        $sum = fn (array $buckets): int => array_sum(array_map(fn (array $bucket): int => array_sum($bucket['values']), $buckets));

        return view('livewire.hr.dashboard', [
            'greetingName' => strtok(trim((string) auth()->user()?->name), ' ') ?: 'HR',
            'yearOptions' => $this->yearOptions(),
            'monthOptions' => $this->monthOptions(),
            'periodName' => $periodName,
            'periodRange' => $range,
            'periodLabel' => $range ? "in {$periodName}" : "in the last {$this->weeks} weeks",
            'bucketHeader' => match (true) {
                ! $range => 'Week',
                $this->month === '' => 'Month',
                default => 'Day',
            },
            'weekRanges' => self::WEEK_RANGES,
            'stats' => $this->stats($stageCounts, (bool) $range),
            'funnel' => $this->funnel($stageCounts),
            'interviews' => $this->interviews($range),
            'recentCandidates' => $this->recentCandidates($range),
            'recentActivity' => $this->recentActivity($range),
            'candidatesPerBucket' => $candidatesPerBucket,
            'candidatesAddedTotal' => $sum($candidatesPerBucket),
            'interviewsPerBucket' => $interviewsPerBucket,
            'interviewsTotal' => $sum($interviewsPerBucket),
            'roundTypeSeries' => $this->roundTypeSeries(),
            'roundOutcomes' => $this->roundOutcomes($window),
            'candidatesByLocation' => $this->candidatesByLocation($window),
        ]);
    }
}
