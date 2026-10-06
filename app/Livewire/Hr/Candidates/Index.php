<?php

namespace App\Livewire\Hr\Candidates;

use App\Enums\RoundStatus;
use App\Enums\RoundType;
use App\Models\Candidate;
use App\Models\CandidateRound;
use App\Support\CandidateOptions;
use App\Support\RoundStatusResolver;
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

    protected const CONCLUDED_STAGES = ['Selected', 'Rejected'];

    /**
     * Opens Schedule Round for this candidate, pre-selecting the round that
     * matches their stage (a "New" candidate starts at the HR Round). A
     * concluded candidate can't have rounds scheduled — same rule as
     * Rounds\Schedule.
     */
    public function scheduleRound(int $candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);
        $name = trim("{$candidate->first_name} {$candidate->last_name}");

        if (in_array($candidate->current_stage, self::CONCLUDED_STAGES, true)) {
            return $this->reloadWithFlash('warning', "{$name} has already been marked {$candidate->current_stage}, so no more rounds can be scheduled.");
        }

        $roundTypes = array_map(fn (RoundType $type): string => $type->value, RoundType::cases());

        $roundType = in_array($candidate->current_stage, $roundTypes, true)
            ? $candidate->current_stage
            : RoundType::Hr->value;

        return redirect()->route('hr.rounds.schedule', ['candidateId' => $candidate->id, 'roundType' => $roundType]);
    }

    public function reject(int $candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);
        $name = trim("{$candidate->first_name} {$candidate->last_name}");

        if (in_array($candidate->current_stage, self::CONCLUDED_STAGES, true)) {
            return $this->reloadWithFlash('warning', "{$name} has already been marked {$candidate->current_stage}.");
        }

        $candidate->update(['current_stage' => 'Rejected']);

        return $this->reloadWithFlash('success', "{$name} was marked as Rejected.");
    }

    /**
     * Flash messages render in the layout, which a Livewire update doesn't
     * re-render — so reload the same page (filters and page number are in the
     * URL) to actually show the message.
     */
    protected function reloadWithFlash(string $type, string $message)
    {
        session()->flash($type, $message);

        return redirect(url()->previous(route('hr.candidates.index')));
    }

    public function sendEmail(int $candidateId): void
    {
        $this->dispatch('open-send-email-modal', candidateId: $candidateId);
    }

    /**
     * The soonest upcoming round that's still open — Completed/Cancelled/
     * No Show rounds, and anything already in the past, don't
     * count as "next".
     */
    protected function nextRound(Candidate $candidate): ?CandidateRound
    {
        $closed = [
            RoundStatus::Completed->value,
            RoundStatus::Cancelled->value,
            RoundStatus::NoShow->value,
        ];

        return $candidate->rounds
            ->filter(fn (CandidateRound $round) => $round->schedule_at->isFuture()
                && ! in_array(RoundStatusResolver::effective($round), $closed, true))
            ->sortBy('schedule_at')
            ->first();
    }

    public function render()
    {
        $candidates = $this->filteredCandidatesQuery()
            ->with('rounds')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->through(function (Candidate $candidate): array {
                // RoundStatusResolver reads $round->candidate; hand it the
                // one we already have instead of querying it per round.
                $candidate->rounds->each->setRelation('candidate', $candidate);

                $nextRound = $this->nextRound($candidate);

                return [
                    'id' => $candidate->id,
                    'first_name' => $candidate->first_name,
                    'last_name' => $candidate->last_name,
                    'email' => $candidate->email,
                    'phone' => $candidate->phone,
                    'current_company' => $candidate->current_company,
                    'experience_years' => $candidate->experience_years,
                    'stage' => $candidate->current_stage ?: 'New',
                    'next_round' => $nextRound?->type,
                    'next_round_date' => $nextRound?->schedule_at->format('M j, Y · h:i A'),
                ];
            });

        return view('livewire.hr.candidates.index', [
            'candidates' => $candidates,
            'totalCount' => Candidate::count(),
        ]);
    }
}
