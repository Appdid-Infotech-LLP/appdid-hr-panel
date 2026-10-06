<?php

namespace App\Livewire\Hr;

use App\Enums\RoundType;
use App\Models\Candidate;
use App\Models\CandidateRound;
use App\Support\RoundStatusResolver;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    protected const LIST_LIMIT = 5;

    /**
     * Candidates currently sitting in each stage — the same grouping the
     * Pipeline board uses, so the dashboard and the board always agree.
     *
     * @return Collection<string, int>
     */
    protected function stageCounts(): Collection
    {
        return Candidate::query()
            ->selectRaw('current_stage, count(*) as total')
            ->groupBy('current_stage')
            ->pluck('total', 'current_stage');
    }

    /**
     * @return list<array{label: string, value: int, accent: string, icon: string}>
     */
    protected function stats(Collection $stageCounts): array
    {
        return [
            ['label' => 'Total Candidates', 'value' => $stageCounts->sum(), 'accent' => 'teal', 'icon' => 'users'],
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
     * Future rounds that are still live. "Live" uses the same effective
     * status as the Rounds list, so a round of a candidate who was already
     * rejected/selected (or whose round was completed/cancelled) is skipped.
     *
     * @return list<array<string, string>>
     */
    protected function upcomingInterviews(): array
    {
        [$statusSql, $statusBindings] = RoundStatusResolver::sqlExpression();

        return CandidateRound::query()
            ->join('candidates', 'candidate_rounds.candidate_id', '=', 'candidates.id')
            ->select('candidate_rounds.*')
            ->selectRaw("{$statusSql} as computed_status", $statusBindings)
            ->with(['candidate', 'interviewer'])
            ->where('candidate_rounds.schedule_at', '>=', now())
            ->whereRaw("({$statusSql}) not in (?, ?)", [...$statusBindings, 'Completed', 'Cancelled'])
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
     * @return list<array<string, mixed>>
     */
    protected function recentCandidates(): array
    {
        return Candidate::query()
            ->latest()
            ->limit(self::LIST_LIMIT)
            ->get()
            ->map(fn (Candidate $candidate): array => [
                'id' => $candidate->id,
                'name' => $this->fullName($candidate),
                'initials' => mb_strtoupper(mb_substr($candidate->first_name, 0, 1).mb_substr($candidate->last_name, 0, 1)),
                'role' => $candidate->current_designation ?: 'Role not set',
                'stage' => $candidate->current_stage,
                'added' => $candidate->created_at->diffForHumans(),
            ])
            ->all();
    }

    /**
     * There's no audit log table, so activity is derived from what the data
     * itself records: candidates being added, rounds being scheduled, and
     * rounds being completed/cancelled (stamped by the row's updated_at).
     *
     * @return list<array{text: string, time: string}>
     */
    protected function recentActivity(): array
    {
        $limit = self::LIST_LIMIT;

        $added = Candidate::query()->latest()->limit($limit)->get()
            ->map(fn (Candidate $candidate): array => [
                'at' => $candidate->created_at,
                'text' => "{$this->fullName($candidate)} was added as a new candidate",
            ]);

        $rounds = CandidateRound::query()->with('candidate')->latest()->limit($limit)->get();

        $scheduled = $rounds->map(fn (CandidateRound $round): array => [
            'at' => $round->created_at,
            'text' => "{$this->fullName($round->candidate)} was scheduled for {$round->type}",
        ]);

        $concluded = CandidateRound::query()->with('candidate')
            ->whereIn('status', ['Completed', 'Cancelled'])
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
                'time' => $activity['at']->diffForHumans(),
            ])
            ->values()
            ->all();
    }

    protected function fullName(Candidate $candidate): string
    {
        return trim("{$candidate->first_name} {$candidate->last_name}");
    }

    public function render()
    {
        $stageCounts = $this->stageCounts();

        return view('livewire.hr.dashboard', [
            'greetingName' => strtok(trim((string) auth()->user()?->name), ' ') ?: 'HR',
            'stats' => $this->stats($stageCounts),
            'funnel' => $this->funnel($stageCounts),
            'upcomingInterviews' => $this->upcomingInterviews(),
            'recentCandidates' => $this->recentCandidates(),
            'recentActivity' => $this->recentActivity(),
        ]);
    }
}
