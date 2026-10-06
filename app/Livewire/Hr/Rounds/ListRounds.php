<?php

namespace App\Livewire\Hr\Rounds;

use App\Models\CandidateRound;
use App\Support\RoundOptions;
use App\Support\RoundStatusResolver;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.hr', ['title' => 'Recruitment Rounds'])]
class ListRounds extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $typeFilter = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $modeFilter = '';

    public string $sortField = 'date';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public bool $showRoundConfirmation = false;

    #[Locked]
    public ?int $pendingRoundId = null;

    #[Locked]
    public string $pendingRoundAction = '';

    #[Locked]
    public string $pendingCandidateName = '';

    #[Locked]
    public string $pendingRoundType = '';

    protected function roundsQuery(): Builder
    {
        $query = CandidateRound::query()
            ->join('candidates', 'candidate_rounds.candidate_id', '=', 'candidates.id')
            ->select('candidate_rounds.*')
            ->with(['candidate', 'interviewer']);

        [$statusSql, $statusBindings] = RoundStatusResolver::sqlExpression();
        $query->selectRaw("{$statusSql} as computed_status", $statusBindings);

        $searchTerms = preg_split('/\s+/', trim($this->search), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($searchTerms as $searchTerm) {
            $query->where(function (Builder $nameQuery) use ($searchTerm): void {
                $nameQuery
                    ->where('candidates.first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('candidates.last_name', 'like', "%{$searchTerm}%");
            });
        }

        if ($this->typeFilter !== '') {
            $query->where('candidate_rounds.type', $this->typeFilter);
        }

        if ($this->statusFilter !== '') {
            $query->whereRaw("({$statusSql}) = ?", [...$statusBindings, $this->statusFilter]);
        }

        if ($this->modeFilter !== '') {
            $query->where('candidate_rounds.mode', $this->modeFilter);
        }

        return $query;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingModeFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($field !== 'date') {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'typeFilter', 'statusFilter', 'modeFilter']);
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '' || $this->typeFilter !== '' || $this->statusFilter !== '' || $this->modeFilter !== '';
    }

    public function types(): array
    {
        return RoundOptions::types();
    }

    public function statuses(): array
    {
        return RoundOptions::statuses();
    }

    public function modes(): array
    {
        return RoundOptions::modes();
    }

    public function openRoundConfirmation(int $roundId, string $action): void
    {
        abort_unless(in_array($action, ['complete', 'cancel'], true), 404);

        $round = CandidateRound::with('candidate')->findOrFail($roundId);

        if (in_array(RoundStatusResolver::effective($round), ['Completed', 'Cancelled'], true)) {
            return;
        }

        $this->pendingRoundId = $round->id;
        $this->pendingRoundAction = $action;
        $this->pendingCandidateName = trim($round->candidate->first_name.' '.$round->candidate->last_name);
        $this->pendingRoundType = $round->type;
        $this->showRoundConfirmation = true;
    }

    public function confirmRoundAction(): void
    {
        if (! $this->showRoundConfirmation || $this->pendingRoundId === null || ! in_array($this->pendingRoundAction, ['complete', 'cancel'], true)) {
            $this->close();

            return;
        }

        $round = CandidateRound::with('candidate')->findOrFail($this->pendingRoundId);

        if (in_array(RoundStatusResolver::effective($round), ['Completed', 'Cancelled'], true)) {
            $this->close();

            return;
        }

        $round->update([
            'status' => $this->pendingRoundAction === 'complete' ? 'Completed' : 'Cancelled',
        ]);

        $candidateName = $this->pendingCandidateName;
        $status = $this->pendingRoundAction === 'complete' ? 'completed' : 'cancelled';
        $nextStage = $this->pendingRoundAction === 'complete' ? $this->advanceStageIfCurrent($round) : null;

        $this->close();

        session()->flash('success', $nextStage
            ? "{$candidateName}'s round was completed — moved to {$nextStage}."
            : "{$candidateName}'s round was {$status}.");
    }

    /**
     * Default stage progression: completing a round moves the candidate
     * into the next round's stage, but only if they were still sitting at
     * this round's stage — if HR already moved them elsewhere (they have
     * that flexibility on the Pipeline board), completing an old round
     * shouldn't yank them back into the sequence. Completing a Final Round
     * never auto-advances: Selected/Rejected is always an explicit,
     * confirmed decision (see Candidates\Show::concludeAs() and
     * Pipeline::moveToStage()).
     */
    protected function advanceStageIfCurrent(CandidateRound $round): ?string
    {
        $candidate = $round->candidate;

        if ($candidate->current_stage !== $round->type) {
            return null;
        }

        $nextStage = RoundStatusResolver::stageAfterCompleting($round->type);

        if ($nextStage !== null) {
            $candidate->update(['current_stage' => $nextStage]);
        }

        return $nextStage;
    }

    public function close(): void
    {
        $this->reset([
            'showRoundConfirmation',
            'pendingRoundId',
            'pendingRoundAction',
            'pendingCandidateName',
            'pendingRoundType',
        ]);
    }

    public function render()
    {
        $rounds = $this->roundsQuery()
            ->orderBy('candidate_rounds.schedule_at', $this->sortDirection)
            ->paginate($this->perPage)
            ->through(fn (CandidateRound $round): array => [
                'id' => $round->id,
                'candidate_id' => $round->candidate_id,
                'candidate_name' => trim($round->candidate->first_name.' '.$round->candidate->last_name),
                'type' => $round->type,
                'date' => $round->schedule_at->toDateString(),
                'time' => $round->schedule_at->format('h:i A'),
                'mode' => $round->mode,
                'interviewer' => $round->interviewer?->name ?? '—',
                'status' => $round->computed_status,
            ]);

        return view('livewire.hr.rounds.list-rounds', [
            'rounds' => $rounds,
            'totalCount' => CandidateRound::count(),
        ]);
    }
}
