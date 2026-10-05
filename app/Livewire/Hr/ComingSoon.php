<?php

namespace App\Livewire\Hr;

use Livewire\Component;

/**
 * Temporary placeholder for nav destinations that haven't been built yet
 * in the current phase. Each route using this component gets swapped out
 * for its own real Livewire component ({@see Dashboard} for an example)
 * once that phase is implemented.
 */
class ComingSoon extends Component
{
    public string $title = 'Coming Soon';

    public string $phase = 'a later phase';

    /**
     * Keyed by route name, since this shared placeholder has no route
     * parameters of its own to bind title/phase from.
     */
    protected array $pages = [
        'hr.candidates.index' => ['title' => 'Candidates', 'phase' => 'Phase 3'],
        'hr.rounds.index' => ['title' => 'Recruitment Rounds', 'phase' => 'Phase 5'],
        'hr.gantt' => ['title' => 'Recruitment Gantt', 'phase' => 'Phase 9'],
        'hr.reports' => ['title' => 'Reports', 'phase' => 'a later phase'],
        'hr.settings' => ['title' => 'Settings', 'phase' => 'a later phase'],
    ];

    public function mount(): void
    {
        $page = $this->pages[request()->route()?->getName()] ?? null;

        if ($page) {
            $this->title = $page['title'];
            $this->phase = $page['phase'];
        }
    }

    public function render()
    {
        return view('livewire.hr.coming-soon')
            ->layout('layouts.hr', ['title' => $this->title]);
    }
}
