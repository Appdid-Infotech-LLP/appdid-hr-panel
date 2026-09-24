<?php

namespace App\Livewire\Hr\Rounds;

use App\Support\DemoCandidates;
use App\Support\RoundOptions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Schedule Round'])]
class Schedule extends Component
{
    #[Url]
    public ?int $candidateId = null;

    public string $roundType = '';

    public string $date = '';

    public string $time = '';

    public string $mode = 'Virtual';

    public string $meetingLink = '';

    public string $interviewer = '';

    public string $notes = '';

    public function candidates(): array
    {
        return collect(DemoCandidates::all())
            ->mapWithKeys(fn ($candidate) => [
                $candidate['id'] => $candidate['first_name'].' '.$candidate['last_name'].' — '.($candidate['current_designation'] ?? 'Candidate'),
            ])
            ->all();
    }

    public function roundTypes(): array
    {
        return RoundOptions::types();
    }

    public function interviewers(): array
    {
        return RoundOptions::interviewers();
    }

    public function selectedCandidateName(): ?string
    {
        $candidate = $this->candidateId ? DemoCandidates::find($this->candidateId) : null;

        return $candidate ? $candidate['first_name'].' '.$candidate['last_name'] : null;
    }

    /**
     * TODO — YOUR IMPLEMENTATION
     */
    protected function rules(): array
    {
        return [];
    }

    public function scheduleRound()
    {
        /*
         * TODO — YOUR IMPLEMENTATION
         *
         * 1. Validate the form: $this->validate();
         * 2. Find the candidate.
         * 3. Create the recruitment round.
         * 4. Create Google Calendar event.
         * 5. Save calendar event ID.
         * 6. Send candidate email.
         * 7. Reset the form.
         * 8. Show success message.
         */
    }

    public function render()
    {
        return view('livewire.hr.rounds.schedule');
    }
}
