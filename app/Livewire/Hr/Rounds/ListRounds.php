<?php

namespace App\Livewire\Hr\Rounds;

use App\Support\DemoCandidates;
use App\Support\RoundOptions;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
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

    /**
     * TODO — YOUR IMPLEMENTATION
     * Replace with a RecruitmentRound query once that model/migration exist:
     *   RecruitmentRound::with('candidate')
     *       ->when($this->search, fn ($q) => $q->whereHas('candidate', ...))
     *       ->when($this->typeFilter, fn ($q) => $q->where('round_type', $this->typeFilter))
     *       ->orderBy($this->sortField, $this->sortDirection)
     *       ->paginate($this->perPage);
     */
    protected function filteredRounds(): Collection
    {
        $search = trim(mb_strtolower($this->search));

        return collect(DemoCandidates::allRoundsFlattened())
            ->when($search !== '', fn (Collection $rows) => $rows->filter(
                fn (array $row) => str_contains(mb_strtolower($row['candidate_name']), $search)
            ))
            ->when($this->typeFilter !== '', fn (Collection $rows) => $rows->where('type', $this->typeFilter))
            ->when($this->statusFilter !== '', fn (Collection $rows) => $rows->where('status', $this->statusFilter))
            ->when($this->modeFilter !== '', fn (Collection $rows) => $rows->where('mode', $this->modeFilter))
            ->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'desc')
            ->values();
    }

    protected function paginate(Collection $items): LengthAwarePaginator
    {
        $page = $this->getPage();

        return new LengthAwarePaginator(
            $items->forPage($page, $this->perPage)->values(),
            $items->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page'],
        );
    }

    // Row actions — UI placeholders only.
    public function markCompleted(int $candidateId, string $roundType): void
    {
        // TODO: Update this round's status to "Completed" and log an activity.
    }

    public function cancelRound(int $candidateId, string $roundType): void
    {
        // TODO: Update this round's status to "Cancelled", notify the candidate, and log an activity.
    }

    public function render()
    {
        return view('livewire.hr.rounds.list-rounds', [
            'rounds' => $this->paginate($this->filteredRounds()),
            'totalCount' => count(DemoCandidates::allRoundsFlattened()),
        ]);
    }
}
