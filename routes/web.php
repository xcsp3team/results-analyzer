<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', \App\Livewire\Home::class)->name('home');
Route::livewire("/evaluations/optimisation/{slug}", \App\Livewire\EvaluationCop::class)->name('evaluations_cop');
Route::livewire("/evaluations/satisfaction/{slug}", \App\Livewire\Evaluation::class)->name('evaluations');
