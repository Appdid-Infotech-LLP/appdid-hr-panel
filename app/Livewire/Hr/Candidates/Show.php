<?php

namespace App\Livewire\Hr\Candidates;

use App\Models\Candidate;
use Livewire\Component;

class Show extends Component
{
    public array $candidate;

    public function mount(int $candidateId): void
    {
        $c = Candidate::with('rounds.interviewer')->findOrFail($candidateId);

        $rounds = $c->rounds
            ->sortBy('schedule_at')
            ->map(fn ($round) => [
                'type' => $round->type,
                'date' => $round->schedule_at->format('Y-m-d'),
                'time' => $round->schedule_at->format('h:i A'),
                'mode' => $round->mode,
                'meeting_link' => $round->meeting_link,
                'interviewer' => $round->interviewer?->name,
                'notes' => $round->notes,
            ])
            ->values()
            ->all();

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
            'stage' => $c->current_stage,

            'rounds' => $rounds,
        ];
    }

    public function render()
    {
        return view('livewire.hr.candidates.show')
            ->layout('layouts.hr', ['title' => $this->candidate['first_name'].' '.$this->candidate['last_name']]);
    }

    public function markAsSelected(): void
    {
        $this->concludeAs('Selected');
    }

    public function reject(): void
    {
        $this->concludeAs('Rejected');
    }

    protected function concludeAs(string $outcome): void
    {
        Candidate::whereKey($this->candidate['id'])->update([
            'current_stage' => $outcome,
        ]);

        $this->candidate['stage'] = $outcome;

        session()->flash('success', trim($this->candidate['first_name'].' '.$this->candidate['last_name'])." was marked as {$outcome}.");
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
