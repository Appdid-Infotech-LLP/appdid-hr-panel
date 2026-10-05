<?php

namespace App\Livewire\Hr;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.hr', ['title' => 'Settings'])]
class Settings extends Component
{
    public function isGoogleCalendarConnected(): bool
    {
        $admin = Auth::user();

        return $admin instanceof Admin && $admin->hasGoogleCalendarConnected();
    }

    public function render()
    {
        return view('livewire.hr.settings');
    }
}
