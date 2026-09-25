<?php

namespace App\Livewire\Hr\Candidates;

use App\Models\Candidate;
use Livewire\Component;

class Show extends Component
{
    public array $candidate;

    public function mount(int $candidateId): void
    {
        $c = Candidate::findOrFail($candidateId);

        $this->candidate = [
            'id' => $c->id,
            'first_name' => $c->first_name,
            'last_name' => $c->last_name,
            'email' => $c->email,
            'phone' => $c->phone,
            'date_of_birth' => $c->date_of_birth,
            'gender' => $c->gender,
            'location' => $c->location,
            'address' => $c->address,
            'highest_qualification' => $c->highest_qualification,
            'college' => $c->college,
            'experience_years' => $c->experience_years,
            'current_company' => $c->current_company,
            'current_designation' => $c->current_designation,
            'current_salary' => $c->current_salary,
            'expected_salary' => $c->expected_salary,
            'notice_period' => $c->notice_period,
            'skills' => $c->skills ?? [],
            'contacted_on' => $c->contacted_on,
            'tech_stack' => $c->tech_stack ?? [],
            'agreed_to_bond' => $c->agreed_to_bond,
            'expected_joining_date' => $c->expected_joining_date,
            'reason_for_leaving' => $c->reason_for_leaving,
            'linkedin_url' => $c->linkedin_url,
            'portfolio_url' => $c->portfolio_url,
            'resume_path' => $c->resume_path,
            'notes' => $c->notes,
            'status' => $c->status,
            'stage' => $c->current_stage,

            'rounds' => [],
        ];
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
        $this->dispatch('open-send-email-modal', candidateId: $this->candidate['id']);
    }

    public function downloadResume(): void
    {
        // TODO: Stream the stored resume file for download from its disk path.
    }
}
