<?php

namespace App\Livewire\Hr;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.hr.dashboard');
    }
}
