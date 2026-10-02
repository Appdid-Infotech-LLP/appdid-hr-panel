<?php

namespace App\Livewire\Hr\Rounds;

use App\Models\CandidateRound;
use App\Services\GoogleCalendarService;
use App\Support\RoundOptions;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Edit Round'])]
class Edit extends Component
{
    public int $roundId;

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

    public string $calendarStatus = 'Not Synced';

    public ?string $calendarEventId = null;

    /**
     * A round type can have more than one row if it was rescheduled — the
     * latest one is the one that matters (same convention as Show.php).
     */
    public function mount(int $candidateId, string $roundType): void
    {
        $round = CandidateRound::with(['candidate', 'interviewer'])
            ->where('candidate_id', $candidateId)
            ->where('type', $roundType)
            ->latest('schedule_at')
            ->firstOrFail();

        $this->roundId = $round->id;
        $this->candidateId = $round->candidate_id;
        $this->candidateName = trim($round->candidate->first_name.' '.$round->candidate->last_name);
        $this->roundType = $round->type;
        $this->date = $round->schedule_at->format('Y-m-d');
        $this->time = $round->schedule_at->format('H:i');
        $this->mode = $round->mode;
        $this->meetingLink = (string) $round->meeting_link;
        $this->interviewer = "{$round->interviewer_type}:{$round->interviewer_id}";
        $this->status = (string) $round->status;
        $this->notes = (string) $round->notes;
        $this->calendarEventId = $round->calendar_event_id;
        $this->calendarStatus = $round->calendar_event_id ? 'Synced' : 'Not Synced';
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
        $round = CandidateRound::with(['candidate', 'interviewer'])->findOrFail($this->roundId);

        try {
            $service = app(GoogleCalendarService::class);

            $eventId = $this->calendarEventId
                ? tap($this->calendarEventId, fn (string $id) => $service->updateEvent($id, $round))
                : $service->createEvent($round);

            $round->update(['calendar_event_id' => $eventId]);

            $this->calendarEventId = $eventId;
            $this->calendarStatus = 'Synced';

            session()->flash('success', 'Calendar event synced.');
        } catch (\Throwable $e) {
            \Log::error('Google Calendar sync failed: '.$e->getMessage());

            session()->flash('warning', 'Round saved, but calendar sync failed. You can retry from this page.');
        }
    }

    public function updateRound()
    {
        /*
         * TODO — YOUR IMPLEMENTATION
         *
         * 1. Validate the form: $this->validate();
         * 2. Update the round record.
         * 3. If the date/time/mode changed, treat it as a reschedule: call
         *    $this->createCalendarEvent() (above) to push the change to
         *    Google Calendar, and notify the candidate.
         * 4. If the status changed, log a candidate_activities entry.
         * 5. Flash a success message and redirect back to the candidate or rounds list.
         */
    }

    public function render()
    {
        return view('livewire.hr.rounds.edit');
    }
}
