<?php

use App\Livewire\Hr\ComingSoon;
use App\Livewire\Hr\Dashboard;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/hr/dashboard');

Route::prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // These point at a shared placeholder until their own phase builds the
    // real Livewire component (see App\Livewire\Hr\ComingSoon).
    Route::get('/candidates', ComingSoon::class)->name('candidates.index');
    Route::get('/pipeline', ComingSoon::class)->name('pipeline');
    Route::get('/rounds', ComingSoon::class)->name('rounds.index');
    Route::get('/gantt', ComingSoon::class)->name('gantt');
    Route::get('/calendar', ComingSoon::class)->name('calendar');
    Route::get('/reports', ComingSoon::class)->name('reports');
    Route::get('/settings', ComingSoon::class)->name('settings');
});
