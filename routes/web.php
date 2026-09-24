<?php

use App\Livewire\Auth\Login;
use App\Livewire\Hr\Candidates\Components\ResumeUpload;
use App\Livewire\Hr\Candidates\Create as CandidatesCreate;
use App\Livewire\Hr\Candidates\Edit as CandidatesEdit;
use App\Livewire\Hr\Candidates\Index as CandidatesIndex;
use App\Livewire\Hr\Candidates\Show as CandidatesShow;
use App\Livewire\Hr\ComingSoon;
use App\Livewire\Hr\Dashboard;
use App\Livewire\Hr\Rounds\Edit as RoundsEdit;
use App\Livewire\Hr\Rounds\ListRounds;
use App\Livewire\Hr\Rounds\Schedule as RoundsSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/hr/dashboard');

Route::get('/login', Login::class)->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::middleware(['auth', 'hr.access'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::prefix('candidates')->name('candidates.')->group(function () {
        Route::get('/', CandidatesIndex::class)->name('index');
        Route::get('/create', CandidatesCreate::class)->name('create');
        Route::get('/upload', ResumeUpload::class)->name('upload');
        Route::get('/{candidateId}', CandidatesShow::class)->name('show');
        Route::get('/{candidateId}/edit', CandidatesEdit::class)->name('edit');
    });

    Route::prefix('rounds')->name('rounds.')->group(function () {
        Route::get('/', ListRounds::class)->name('index');
        Route::get('/schedule', RoundsSchedule::class)->name('schedule');
        Route::get('/{candidateId}/{roundType}/edit', RoundsEdit::class)->name('edit');
    });

    // These point at a shared placeholder until their own phase builds the
    // real Livewire component (see App\Livewire\Hr\ComingSoon).
    Route::get('/pipeline', ComingSoon::class)->name('pipeline');
    Route::get('/gantt', ComingSoon::class)->name('gantt');
    Route::get('/calendar', ComingSoon::class)->name('calendar');
    Route::get('/reports', ComingSoon::class)->name('reports');
    Route::get('/settings', ComingSoon::class)->name('settings');
});
