<?php

namespace App\Livewire\Hr;

use App\Models\CandidateRound;
use App\Services\GoogleCalendarService;
use App\Support\RoundOptions;
use App\Support\RoundStatusResolver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Calendar'])]
class CalendarPage extends Component
{
    #[Url]
    public string $month = '';

    #[Url]
    public string $typeFilter = '';

    #[Url]
    public string $modeFilter = '';

    public bool $showDay = false;

    #[Locked]
    public string $selectedDate = '';

    public function mount(): void
    {
        if (! $this->month || ! Carbon::createFromFormat('Y-m', $this->month)) {
            $this->month = now()->format('Y-m');
        }
    }

    public function previousMonth(): void
    {
        $this->month = $this->monthStart()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->monthStart()->addMonth()->format('Y-m');
    }

    public function goToToday(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function types(): array
    {
        return RoundOptions::types();
    }

    public function modes(): array
    {
        return RoundOptions::modes();
    }

    public function clearFilters(): void
    {
        $this->reset(['typeFilter', 'modeFilter']);
    }

    public function hasActiveFilters(): bool
    {
        return $this->typeFilter !== '' || $this->modeFilter !== '';
    }

    public function viewDay(string $date): void
    {
        $this->selectedDate = $date;
        $this->showDay = true;
    }

    public function close(): void
    {
        $this->reset(['showDay', 'selectedDate']);
    }

    public function createCalendarEvent(int $roundId): void
    {
        $round = CandidateRound::with(['candidate', 'interviewer'])->findOrFail($roundId);

        try {
            $service = app(GoogleCalendarService::class);

            $event = $round->calendar_event_id
                ? $service->updateEvent($round->calendar_event_id, $round)
                : $service->createEvent($round);

            $round->update([
                'calendar_event_id' => $event->getId(),
                'meeting_link' => $round->meeting_link ?: GoogleCalendarService::meetLink($event),
            ]);

            session()->flash('success', 'Calendar event synced.');
        } catch (\Throwable $e) {
            \Log::error('Google Calendar sync failed: '.$e->getMessage());

            session()->flash('warning', 'Calendar sync failed. You can retry from here.');
        }
    }

    protected function monthStart(): Carbon
    {
        return Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
    }

    /**
     * Rounds for the visible month, grouped by day (Y-m-d) => list of
     * display-ready round arrays, ordered by time within each day.
     */
    protected function roundsByDay(): Collection
    {
        $monthStart = $this->monthStart();

        [$statusSql, $statusBindings] = RoundStatusResolver::sqlExpression();

        $query = CandidateRound::query()
            ->join('candidates', 'candidate_rounds.candidate_id', '=', 'candidates.id')
            ->select('candidate_rounds.*')
            ->selectRaw("{$statusSql} as computed_status", $statusBindings)
            ->with(['candidate', 'interviewer'])
            ->whereBetween('candidate_rounds.schedule_at', [
                $monthStart->copy()->startOfDay(),
                $monthStart->copy()->endOfMonth()->endOfDay(),
            ]);

        if ($this->typeFilter !== '') {
            $query->where('candidate_rounds.type', $this->typeFilter);
        }

        if ($this->modeFilter !== '') {
            $query->where('candidate_rounds.mode', $this->modeFilter);
        }

        return $query->orderBy('candidate_rounds.schedule_at')
            ->get()
            ->groupBy(fn (CandidateRound $round): string => $round->schedule_at->toDateString())
            ->map(fn (Collection $rounds): Collection => $rounds->map(fn (CandidateRound $round): array => [
                'id' => $round->id,
                'candidate_id' => $round->candidate_id,
                'candidate_name' => trim($round->candidate->first_name.' '.$round->candidate->last_name),
                'type' => $round->type,
                'time' => $round->schedule_at->format('h:i A'),
                'mode' => $round->mode,
                'meeting_link' => $round->meeting_link,
                'interviewer' => $round->interviewer?->name ?? '—',
                'status' => $round->computed_status,
                'calendar_event_id' => $round->calendar_event_id,
            ]));
    }

    /**
     * A 6-row x 7-col grid of days covering the full weeks the month spans,
     * each cell carrying its date, whether it's in the visible month, and
     * the day's rounds.
     *
     * @return list<array>
     */
    protected function weeks(Collection $roundsByDay): array
    {
        $monthStart = $this->monthStart();
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $monthStart->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        $cursor = $gridStart->copy();

        while ($cursor->lte($gridEnd)) {
            $dateKey = $cursor->toDateString();

            $days[] = [
                'date' => $dateKey,
                'day' => $cursor->day,
                'inMonth' => $cursor->month === $monthStart->month,
                'isToday' => $cursor->isToday(),
                'rounds' => $roundsByDay->get($dateKey, collect())->all(),
            ];

            $cursor->addDay();
        }

        return array_chunk($days, 7);
    }

    public function render()
    {
        $roundsByDay = $this->roundsByDay();

        return view('livewire.hr.calendar-page', [
            'monthLabel' => $this->monthStart()->format('F Y'),
            'weeks' => $this->weeks($roundsByDay),
            'selectedDayRounds' => $this->showDay ? $roundsByDay->get($this->selectedDate, collect())->all() : [],
            'selectedDateLabel' => $this->selectedDate ? Carbon::parse($this->selectedDate)->format('l, F j, Y') : '',
        ]);
    }
}
