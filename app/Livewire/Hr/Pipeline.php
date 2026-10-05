<?php

namespace App\Livewire\Hr;

use App\Enums\RoundType;
use App\Models\Candidate;
use App\Support\CandidateOptions;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Recruitment Pipeline'])]
class Pipeline extends Component
{
    protected const CONCLUDED_STAGES = ['Selected', 'Rejected'];

    #[Url]
    public string $search = '';

    public function stages(): array
    {
        return CandidateOptions::stages();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '';
    }

    public function clearFilters(): void
    {
        $this->reset('search');
    }

    /**
     * Moves a candidate to a different stage column — used by both the
     * card's dropdown and the drag-and-drop drop handler. A concluded
     * candidate (Selected/Rejected) mirrors Candidates\Show::concludeAs()
     * and can't be moved again from here.
     */
    public function moveToStage(int $candidateId, string $stage): void
    {
        abort_unless(array_key_exists($stage, CandidateOptions::stages()), 404);

        $candidate = Candidate::findOrFail($candidateId);
        $name = trim("{$candidate->first_name} {$candidate->last_name}");

        if (in_array($candidate->current_stage, self::CONCLUDED_STAGES, true)) {
            session()->flash('warning', "{$name} has already been concluded and can't be moved.");

            return;
        }

        if ($candidate->current_stage === $stage) {
            return;
        }

        $candidate->update(['current_stage' => $stage]);

        session()->flash('success', "{$name} moved to {$stage}.");
    }

    protected function candidatesQuery(): Builder
    {
        $search = trim($this->search);

        return Candidate::query()
            ->with(['rounds:id,candidate_id,type,schedule_at'])
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search): void {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            }));
    }

    public function render()
    {
        $roundTypes = array_map(fn (RoundType $type): string => $type->value, RoundType::cases());

        $candidatesByStage = $this->candidatesQuery()
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('current_stage');

        $columns = collect($this->stages())
            ->map(function (string $label, string $stage) use ($candidatesByStage, $roundTypes): array {
                return [
                    'stage' => $stage,
                    'label' => $label,
                    'concluded' => in_array($stage, self::CONCLUDED_STAGES, true),
                    'candidates' => $candidatesByStage->get($stage, collect())
                        ->map(function (Candidate $candidate) use ($roundTypes): array {
                            // A stage like "Technical Round" implies a round of that
                            // type should exist — if the Pipeline moved them here
                            // without one ever being scheduled, flag it so HR notices.
                            // If one does exist, show its date/time instead (latest
                            // wins, same "reschedule" convention as Rounds\Edit).
                            $isRoundStage = in_array($candidate->current_stage, $roundTypes, true);
                            $matchingRound = $isRoundStage
                                ? $candidate->rounds->where('type', $candidate->current_stage)->sortByDesc('schedule_at')->first()
                                : null;

                            return [
                                'id' => $candidate->id,
                                'name' => trim("{$candidate->first_name} {$candidate->last_name}"),
                                'initials' => mb_substr($candidate->first_name, 0, 1).mb_substr($candidate->last_name, 0, 1),
                                'designation' => $candidate->current_designation,
                                'experience' => $candidate->experience_years,
                                'location' => $candidate->location,
                                'missing_round' => $isRoundStage && ! $matchingRound,
                                'scheduled_label' => $matchingRound?->schedule_at->format('M j, g:i A'),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();

        return view('livewire.hr.pipeline', [
            'columns' => $columns,
            'totalCount' => array_sum(array_map(fn (array $column): int => count($column['candidates']), $columns)),
        ]);
    }
}
