<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', \App\Livewire\Home::class)->name('home');
Route::livewire("/evaluations/{slug}", \App\Livewire\Evaluation::class)->name('evaluations');
