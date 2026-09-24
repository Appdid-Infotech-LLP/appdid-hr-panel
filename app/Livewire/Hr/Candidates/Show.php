<?php

namespace App\Livewire\Hr\Candidates;

use App\Support\DemoCandidates;
use Livewire\Component;

class Show extends Component
{
    public array $candidate;

    /**
     * TODO — YOUR IMPLEMENTATION
     * Replace with `Candidate::with('rounds', 'notes')->findOrFail($candidate)`
     * once the model/migrations exist, and prefer route-model binding over
     * a raw id (`public function mount(Candidate $candidate)`).
     */
    public function mount(int $candidateId): void
    {
        $data = DemoCandidates::find($candidateId);

        abort_unless($data, 404);

        $this->candidate = $data;
    }

    public function render()
    {
        return view('livewire.hr.candidates.show')
            ->layout('layouts.hr', ['title' => $this->candidate['first_name'].' '.$this->candidate['last_name']]);
    }

    public function moveToNextRound(): void
    {
        // TODO:
        // 1. Determine the next stage after $this->candidate['stage'].
        // 2. Update the candidate's current_stage in the database.
        // 3. Log a candidate_activities entry.
        // 4. Optionally open the Schedule Round modal for the new stage.
    }

    public function reject(): void
    {
        // TODO:
        // 1. Update the candidate's status to "Rejected" in the database.
        // 2. Log a candidate_activities entry.
        // 3. Optionally open the Send Email modal with a rejection template.
    }

    public function sendEmail(): void
    {
        // TODO: Open the Send Email modal (built in Phase 6) for this candidate.
    }

    public function scheduleRound(): void
    {
        // TODO: Open the Schedule Round modal (built in Phase 5) for this candidate.
    }

    public function downloadResume(): void
    {
        // TODO: Stream the stored resume file for download from its disk path.
    }
}
