<?php

namespace App\Livewire\Hr\Rounds;

use App\Enums\RoundStatus;
use App\Jobs\SendRoundScheduledEmail;
use App\Models\Admin;
use App\Models\CandidateRound;
use App\Services\GoogleCalendarService;
use App\Support\RoundOptions;
use App\Support\RoundStatusResolver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
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

    #[Locked]
    public string $initialStatus = '';

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
        $this->status = $this->initialStatus = RoundStatusResolver::effective($round);
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

    public function calendarConnected(): bool
    {
        $admin = Auth::user();

        return $admin instanceof Admin && $admin->hasGoogleCalendarConnected();
    }

    protected function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $alreadyBooked = CandidateRound::where('candidate_id', $this->candidateId)
                        ->whereKeyNot($this->roundId)
                        ->where('schedule_at', "{$this->date} {$value}:00")
                        ->exists();

                    if ($alreadyBooked) {
                        $fail('This candidate already has another round scheduled at this date and time.');
                    }
                },
            ],
            'mode' => ['required', Rule::in(array_keys(RoundOptions::modes()))],
            'interviewer' => ['required', Rule::in(array_keys(RoundOptions::interviewers()))],
            'status' => ['required', Rule::enum(RoundStatus::class)],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Pushes the round to the admin's Google Calendar (creating the event
     * the first time, updating it after) and mirrors the result onto the
     * component. Throws on failure so each caller can word its own message.
     */
    protected function syncCalendarEvent(CandidateRound $round): void
    {
        $round->loadMissing(['candidate', 'interviewer']);

        $service = app(GoogleCalendarService::class);

        $event = $round->calendar_event_id
            ? $service->updateEvent($round->calendar_event_id, $round)
            : $service->createEvent($round);

        $round->update([
            'calendar_event_id' => $event->getId(),
            'meeting_link' => $round->meeting_link ?: GoogleCalendarService::meetLink($event),
        ]);

        $this->calendarEventId = $event->getId();
        $this->meetingLink = (string) $round->meeting_link;
        $this->calendarStatus = 'Synced';
    }

    public function createCalendarEvent(): void
    {
        $round = CandidateRound::findOrFail($this->roundId);

        try {
            $this->syncCalendarEvent($round);

            session()->flash('success', 'Calendar event synced.');
        } catch (\Throwable $e) {
            \Log::error('Google Calendar sync failed: '.$e->getMessage());

            session()->flash('warning', 'Calendar sync failed. You can retry from this page.');
        }
    }

    public function updateRound()
    {
        $this->validate();

        $round = CandidateRound::with(['candidate', 'interviewer'])->findOrFail($this->roundId);

        [$interviewerType, $interviewerId] = explode(':', $this->interviewer, 2);

        $scheduleAt = Carbon::createFromFormat('Y-m-d H:i', "{$this->date} {$this->time}");
        $rescheduled = ! $round->schedule_at->equalTo($scheduleAt) || $round->mode !== $this->mode;

        $attributes = [
            'schedule_at' => $scheduleAt,
            'mode' => $this->mode,
            'interviewer_type' => $interviewerType,
            'interviewer_id' => $interviewerId,
            'notes' => $this->notes,
        ];

        // A round with no explicit status derives it from the candidate's
        // stage (RoundStatusResolver). The form shows that derived value, so
        // only write one when HR actually picked something different —
        // otherwise a plain edit would freeze the status in place.
        if ($this->status !== $this->initialStatus) {
            $attributes['status'] = $this->status;
        }

        $round->update($attributes);
        $round->load('interviewer');

        $warning = null;

        if ($rescheduled) {
            if ($this->calendarConnected()) {
                try {
                    $this->syncCalendarEvent($round);
                } catch (\Throwable $e) {
                    \Log::error('Google Calendar sync failed: '.$e->getMessage());

                    $warning = 'Round updated, but the calendar event could not be synced. You can retry from this page.';
                }
            }

            SendRoundScheduledEmail::dispatch($round);
        }

        session()->flash('success', 'Round updated successfully.');

        if ($warning) {
            session()->flash('warning', $warning);
        }

        return redirect()->route('hr.candidates.show', $this->candidateId);
    }

    public function render()
    {
        return view('livewire.hr.rounds.edit');
    }
}
