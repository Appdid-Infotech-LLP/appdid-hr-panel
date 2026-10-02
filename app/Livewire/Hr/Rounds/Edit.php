<?php

namespace App\Livewire\Hr\Rounds;

use App\Support\DemoCandidates;
use App\Support\RoundOptions;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Edit Round'])]
class Edit extends Component
{
    public int $candidateId;

    public string $candidateName;

    public string $roundType;

    public string $date = '';

    public string $time = '';

    public string $mode = 'Virtual';

    public string $meetingLink = '';

    public string $interviewer = '';

    public string $status = '';

    public string $notes = '';

    // Google Calendar isn't integrated — these are purely visual until a
    // calendar_event_id column exists on candidate_rounds to back them.
    public string $calendarStatus = 'Not Synced';

    public ?string $calendarEventId = null;

    /**
     * TODO — YOUR IMPLEMENTATION
     * Replace DemoCandidates::findRound() with a real
     * RecruitmentRound::where('candidate_id', $candidateId)->where('round_type', $roundType)->firstOrFail()
     * once the model/migrations exist.
     */
    public function mount(int $candidateId, string $roundType): void
    {
        $round = DemoCandidates::findRound($candidateId, $roundType);

        abort_unless($round, 404);

        $this->candidateId = $candidateId;
        $this->candidateName = $round['candidate_name'];
        $this->roundType = $round['type'];
        $this->date = $round['date'];
        $this->time = $round['time'];
        $this->mode = $round['mode'];
        $this->meetingLink = (string) $round['meeting_link'];
        $this->interviewer = $round['interviewer'];
        $this->status = $round['status'];
        $this->notes = (string) $round['notes'];
    }

    public function statuses(): array
    {
        return RoundOptions::statuses();
    }

    public function interviewers(): array
    {
        return RoundOptions::interviewers();
    }

    /**
     * TODO — YOUR IMPLEMENTATION
     */
    protected function rules(): array
    {
        return [];
    }

    public function createCalendarEvent(): void
    {
        /*
         * TODO: Integrate Google Calendar API.
         *
         * 1. Build the event payload from this round's date/time/mode/
         *    meeting link/interviewer (and update it instead of creating a
         *    new one if $this->calendarEventId is already set — this
         *    doubles as the "reschedule" sync path).
         * 2. Call the Google Calendar API to create or update the event.
         * 3. Persist the returned event ID — add a `calendar_event_id`
         *    column to candidate_rounds first, there isn't one yet.
         * 4. Update $this->calendarStatus / $this->calendarEventId so the
         *    UI reflects the synced state.
         */
    }

    public function updateRound()
    {
        /*
         * TODO — YOUR IMPLEMENTATION
         *
         * 1. Validate the form: $this->validate();
         * 2. Update the round record.
         * 3. If the date/time/mode changed, treat it as a reschedule:
         *    update the Google Calendar event and notify the candidate.
         * 4. If the status changed, log a candidate_activities entry.
         * 5. Flash a success message and redirect back to the candidate or rounds list.
         */
    }

    public function render()
    {
        return view('livewire.hr.rounds.edit');
    }
}
