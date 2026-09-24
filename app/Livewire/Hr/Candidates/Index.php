<?php

namespace App\Livewire\Hr\Candidates;

use App\Support\CandidateOptions;
use App\Support\DemoCandidates;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.hr', ['title' => 'Candidates'])]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $stageFilter = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $experienceFilter = '';

    #[Url]
    public string $locationFilter = '';

    #[Url]
    public string $dateFilter = '';

    public string $sortField = 'added_days_ago';

    public string $sortDirection = 'asc';

    public int $perPage = 8;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStageFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingExperienceFilter(): void
    {
        $this->resetPage();
    }

    public function updatingLocationFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
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
        $this->reset(['search', 'stageFilter', 'statusFilter', 'experienceFilter', 'locationFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function stages(): array
    {
        return CandidateOptions::stages();
    }

    public function statuses(): array
    {
        return CandidateOptions::statuses();
    }

    public function locations(): array
    {
        return CandidateOptions::locations();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->stageFilter !== ''
            || $this->statusFilter !== ''
            || $this->experienceFilter !== ''
            || $this->locationFilter !== ''
            || $this->dateFilter !== '';
    }

    /**
     * Filtering/sorting the in-memory demo array here is just UI plumbing
     * (so the search box and dropdowns feel alive) — it's not recruitment
     * business logic, so it's safe to leave in place.
     *
     * TODO — YOUR IMPLEMENTATION
     * Once the Candidate model exists, replace this whole method with a
     * query, e.g.:
     *   Candidate::query()
     *       ->when($this->search, fn ($q) => $q->where(fn ($q) => $q
     *           ->where('first_name', 'like', "%{$this->search}%")
     *           ->orWhere('email', 'like', "%{$this->search}%")))
     *       ->when($this->stageFilter, fn ($q) => $q->where('current_stage', $this->stageFilter))
     *       ->orderBy($this->sortField, $this->sortDirection)
     *       ->paginate($this->perPage);
     */
    protected function filteredCandidates(): Collection
    {
        $search = trim(mb_strtolower($this->search));

        return collect(DemoCandidates::all())
            ->when($search !== '', fn (Collection $rows) => $rows->filter(function (array $row) use ($search) {
                $haystack = mb_strtolower($row['first_name'].' '.$row['last_name'].' '.$row['email'].' '.$row['phone']);

                return str_contains($haystack, $search);
            }))
            ->when($this->stageFilter !== '', fn (Collection $rows) => $rows->where('stage', $this->stageFilter))
            ->when($this->statusFilter !== '', fn (Collection $rows) => $rows->where('status', $this->statusFilter))
            ->when($this->locationFilter !== '', fn (Collection $rows) => $rows->where('location', $this->locationFilter))
            ->when($this->experienceFilter !== '', function (Collection $rows) {
                [$min, $max] = match ($this->experienceFilter) {
                    '0-2' => [0, 2],
                    '2-5' => [2, 5],
                    '5-10' => [5, 10],
                    '10+' => [10, PHP_INT_MAX],
                    default => [0, PHP_INT_MAX],
                };

                return $rows->whereBetween('experience_years', [$min, $max]);
            })
            ->when($this->dateFilter !== '', fn (Collection $rows) => $rows->where('added_days_ago', '<=', (int) $this->dateFilter))
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

    // Row actions — UI placeholders only, per the project's "don't build
    // the recruitment backend" rule. Wire up real logic when you're ready.
    public function scheduleRound(int $candidateId): void
    {
        // TODO: Open the Schedule Round modal (built in Phase 5) for this candidate.
    }

    public function moveToNextRound(int $candidateId): void
    {
        // TODO:
        // 1. Determine the candidate's next stage.
        // 2. Update current_stage in the database.
        // 3. Log a candidate_activities entry.
    }

    public function reject(int $candidateId): void
    {
        // TODO:
        // 1. Update the candidate's status to "Rejected".
        // 2. Log a candidate_activities entry.
    }

    public function sendEmail(int $candidateId): void
    {
        // TODO: Open the Send Email modal (built in Phase 6) for this candidate.
    }

    public function render()
    {
        return view('livewire.hr.candidates.index', [
            'candidates' => $this->paginate($this->filteredCandidates()),
            'totalCount' => count(DemoCandidates::all()),
        ]);
    }
}
