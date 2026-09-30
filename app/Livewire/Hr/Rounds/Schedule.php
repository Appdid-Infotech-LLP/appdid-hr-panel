<?php

namespace App\Livewire\Hr\Rounds;

use App\Enums\RoundType;
use App\Models\Candidate;
use App\Models\CandidateRound;
use App\Support\DemoCandidates;
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

    public string $meetingLink = '';

    public string $interviewer = '';

    public string $notes = '';

    public function candidates(): array
    {
        return Candidate::all()
            ->mapWithKeys(fn($candidate) => [
                $candidate['id'] => $candidate['first_name'] . ' ' . $candidate['last_name'] . ' — ' . ($candidate['current_designation'] ?? 'Candidate'),
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

        return $candidate ? $candidate['first_name'] . ' ' . $candidate['last_name'] : null;
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
            'meetingLink' => ['required_if:mode,Virtual', 'url'],
            'interviewer' => ['required', Rule::in(array_keys(RoundOptions::interviewers()))],
        ];
    }

    public function scheduleRound()
    {
        $this->validate();

        $candidate = Candidate::findOrFail($this->candidateId);

        [$interviewerType, $interviewerId] = explode(':', $this->interviewer, 2);

        $candidate->rounds()->create([
            'type' => $this->roundType,
            'schedule_at' => "{$this->date} {$this->time}:00",
            'mode' => $this->mode,
            'meeting_link' => $this->meetingLink,
            'interviewer_type' => $interviewerType,
            'interviewer_id' => $interviewerId,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Round scheduled successfully.');

        return redirect()->route('hr.rounds.index');
    }

    public function render()
    {
        return view('livewire.hr.rounds.schedule');
    }
}
