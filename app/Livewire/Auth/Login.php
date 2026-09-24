<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest', ['title' => 'Log In'])]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * TODO — YOUR IMPLEMENTATION
     * e.g. return ['email' => 'required|email', 'password' => 'required'];
     */
    protected function rules(): array
    {
        return [];
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|exists:admins,email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($this->only('email', 'password'), $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do match our records.'
            ]);
        }
        $user = Auth::user();

        if (! $user->canAccessHrPanel()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'You do not have access to the HR panel.',
            ]);
        }
        request()->session()->regenerate();
        return redirect()->intended(route('hr.dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
