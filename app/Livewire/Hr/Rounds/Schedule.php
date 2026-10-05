<?php

namespace App\Livewire\Hr\Rounds;

use App\Enums\RoundType;
use App\Models\Candidate;
use App\Models\CandidateRound;
use App\Services\GoogleCalendarService;
use App\Support\RoundOptions;
use Illuminate\Validation\Rule;
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

    public string $interviewer = '';

    public string $notes = '';

    public function candidates(): array
    {
        return Candidate::all()
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
        $candidate = $this->candidateId ? Candidate::find($this->candidateId) : null;

        return $candidate ? "{$candidate->first_name} {$candidate->last_name}" : null;
    }

    protected function rules(): array
    {
        return [
            'candidateId' => [
                'required',
                'exists:candidates,id',
                function ($attribute, $value, $fail) {
                    if (Candidate::whereKey($value)->whereIn('current_stage', ['Selected', 'Rejected'])->exists()) {
                        $fail('This candidate has already been marked Selected/Rejected and can no longer have rounds scheduled.');
                    }
                },
            ],
            'roundType' => ['required', Rule::enum(RoundType::class)],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $alreadyBooked = CandidateRound::where('candidate_id', $this->candidateId)
                        ->where('schedule_at', "{$this->date} {$value}:00")
                        ->exists();

                    if ($alreadyBooked) {
                        $fail('This candidate already has a round scheduled at this date and time.');
                    }
                },
            ],
            'interviewer' => ['required', Rule::in(array_keys(RoundOptions::interviewers()))],
        ];
    }

    public function scheduleRound()
    {
        $this->validate();

        $candidate = Candidate::findOrFail($this->candidateId);

        [$interviewerType, $interviewerId] = explode(':', $this->interviewer, 2);

        $round = $candidate->rounds()->create([
            'type' => $this->roundType,
            'schedule_at' => "{$this->date} {$this->time}:00",
            'mode' => $this->mode,
            'interviewer_type' => $interviewerType,
            'interviewer_id' => $interviewerId,
            'notes' => $this->notes,
        ]);

        try {
            $event = app(GoogleCalendarService::class)->createEvent($round->load(['candidate', 'interviewer']));

            $round->update([
                'calendar_event_id' => $event->getId(),
                'meeting_link' => GoogleCalendarService::meetLink($event),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Google Calendar sync failed: '.$e->getMessage());

            session()->flash('warning', 'Round scheduled, but the calendar invite could not be created. You can retry from the round\'s edit page.');
        }

        session()->flash('success', 'Round scheduled successfully.');

        return redirect()->route('hr.rounds.index');
    }

    public function render()
    {
        return view('livewire.hr.rounds.schedule');
    }
}
