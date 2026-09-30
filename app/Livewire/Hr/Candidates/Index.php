<?php

namespace App\Livewire\Hr\Candidates;

use App\Models\Candidate;
use App\Support\CandidateOptions;
use Illuminate\Database\Eloquent\Builder;
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
    public string $experienceFilter = '';

    #[Url]
    public string $locationFilter = '';

    #[Url]
    public string $dateFilter = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 8;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStageFilter(): void
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
        $this->reset(['search', 'stageFilter', 'experienceFilter', 'locationFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function stages(): array
    {
        return CandidateOptions::stages();
    }

    public function locations(): array
    {
        return CandidateOptions::locations();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->stageFilter !== ''
            || $this->experienceFilter !== ''
            || $this->locationFilter !== ''
            || $this->dateFilter !== '';
    }

    protected function filteredCandidatesQuery(): Builder
    {
        $search = trim($this->search);

        return Candidate::query()
            ->when($search !== '', fn (Builder $q) => $q->where(function (Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->when($this->stageFilter !== '', fn (Builder $q) => $q->where('current_stage', $this->stageFilter))
            ->when($this->locationFilter !== '', fn (Builder $q) => $q->where('location', $this->locationFilter))
            ->when($this->experienceFilter !== '', function (Builder $q) {
                [$min, $max] = match ($this->experienceFilter) {
                    '0-2' => [0, 2],
                    '2-5' => [2, 5],
                    '5-10' => [5, 10],
                    '10+' => [10, PHP_INT_MAX],
                    default => [0, PHP_INT_MAX],
                };

                $q->whereBetween('experience_years', [$min, $max]);
            })
            ->when($this->dateFilter !== '', fn (Builder $q) => $q->where('created_at', '>=', now()->subDays((int) $this->dateFilter)));
    }

    // Row actions — UI placeholders only, per the project's "don't build
    // the recruitment backend" rule. Wire up real logic when you're ready.
    public function scheduleRound(int $candidateId): void
    {
        // TODO: Open the Schedule Round modal (built in Phase 5) for this candidate.
    }

    public function reject(int $candidateId): void
    {
        // TODO:
        // 1. Update the candidate's current_stage to "Rejected".
        // 2. Log a candidate_activities entry.
    }

    public function sendEmail(int $candidateId): void
    {
        $this->dispatch('open-send-email-modal', candidateId: $candidateId);
    }

    public function render()
    {
        return view('livewire.hr.candidates.index', [
            'candidates' => $this->filteredCandidatesQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'totalCount' => Candidate::count(),
        ]);
    }
}
