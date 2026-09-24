<?php

namespace App\Livewire\Hr;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    /**
     * Demo data only — every array below is hardcoded so the dashboard has
     * something to render. Replace each one with a real query once the
     * Candidate / RecruitmentRound models and migrations exist.
     */
    public array $stats = [];

    public array $funnel = [];

    public array $upcomingInterviews = [];

    public array $recentCandidates = [];

    public array $recentActivity = [];

    public function mount(): void
    {
        // TODO: Replace every assignment below with a real database query,
        // e.g. $this->stats['total'] = Candidate::count();
        $this->stats = [
            ['label' => 'Total Candidates', 'value' => 128, 'accent' => 'teal', 'icon' => 'users'],
            ['label' => 'New Candidates', 'value' => 14, 'accent' => 'slate', 'icon' => 'user-plus'],
            ['label' => 'HR Round', 'value' => 22, 'accent' => 'teal', 'icon' => 'clock'],
            ['label' => 'Task Round', 'value' => 18, 'accent' => 'teal', 'icon' => 'clipboard'],
            ['label' => 'Technical Round', 'value' => 16, 'accent' => 'teal', 'icon' => 'code'],
            ['label' => 'Final Round', 'value' => 9, 'accent' => 'yellow', 'icon' => 'flag'],
            ['label' => 'Selected', 'value' => 31, 'accent' => 'green', 'icon' => 'check'],
            ['label' => 'Rejected', 'value' => 18, 'accent' => 'red', 'icon' => 'x'],
        ];

        $this->funnel = [
            ['label' => 'Candidates', 'count' => 128],
            ['label' => 'HR Round', 'count' => 22],
            ['label' => 'Task Round', 'count' => 18],
            ['label' => 'Technical Round', 'count' => 16],
            ['label' => 'Final Round', 'count' => 9],
            ['label' => 'Selected', 'count' => 31],
        ];

        $this->upcomingInterviews = [
            ['candidate' => 'Rahul Sharma', 'round' => 'Technical Round', 'date' => 'Sep 25, 2026', 'time' => '11:00 AM', 'mode' => 'Virtual', 'interviewer' => 'Anita Desai', 'status' => 'Scheduled'],
            ['candidate' => 'Priya Patel', 'round' => 'HR Round', 'date' => 'Sep 25, 2026', 'time' => '02:30 PM', 'mode' => 'In Person', 'interviewer' => 'Karan Mehta', 'status' => 'Scheduled'],
            ['candidate' => 'Amit Kumar', 'round' => 'Task Round', 'date' => 'Sep 26, 2026', 'time' => '10:00 AM', 'mode' => 'Virtual', 'interviewer' => 'Anita Desai', 'status' => 'Pending'],
            ['candidate' => 'Sneha Rao', 'round' => 'Final Round', 'date' => 'Sep 26, 2026', 'time' => '04:00 PM', 'mode' => 'In Person', 'interviewer' => 'Vikram Singh', 'status' => 'Scheduled'],
            ['candidate' => 'Farhan Ali', 'round' => 'Technical Round', 'date' => 'Sep 27, 2026', 'time' => '09:30 AM', 'mode' => 'Virtual', 'interviewer' => 'Karan Mehta', 'status' => 'Rescheduled'],
        ];

        $this->recentCandidates = [
            ['name' => 'Sneha Rao', 'role' => 'Frontend Developer', 'stage' => 'Final Round', 'added' => '2 hours ago'],
            ['name' => 'Farhan Ali', 'role' => 'Backend Developer', 'stage' => 'Technical Round', 'added' => '5 hours ago'],
            ['name' => 'Meera Nair', 'role' => 'UI/UX Designer', 'stage' => 'New', 'added' => '1 day ago'],
            ['name' => 'Vikram Singh', 'role' => 'QA Engineer', 'stage' => 'HR Round', 'added' => '1 day ago'],
            ['name' => 'Ananya Iyer', 'role' => 'Product Manager', 'stage' => 'New', 'added' => '2 days ago'],
        ];

        $this->recentActivity = [
            ['text' => 'Rahul Sharma moved to Technical Round', 'time' => '30 minutes ago'],
            ['text' => 'Priya Patel completed HR Round', 'time' => '2 hours ago'],
            ['text' => 'Amit Kumar was scheduled for Task Round', 'time' => '4 hours ago'],
            ['text' => 'Meera Nair added as a new candidate', 'time' => '1 day ago'],
            ['text' => 'Vikram Singh rejected after Task Round', 'time' => '1 day ago'],
        ];
    }

    /**
     * Demonstrates a Livewire computed property: cached for the duration of
     * the request/component lifecycle instead of recalculated on every
     * render. Used here to size the funnel bars relative to the widest stage.
     */
    #[Computed]
    public function funnelMax(): int
    {
        return collect($this->funnel)->max('count') ?: 1;
    }

    public function render()
    {
        return view('livewire.hr.dashboard');
    }
}
