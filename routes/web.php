<?php

use App\Livewire\Dashboard;
use App\Livewire\Login;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('components.header');
// });

// Route::livewire('/home', 'pages::components.header');
Route::get('/', Login::class)->name('header');
Route::get('/dashboard', Dashboard::class)->name('dashboard ');
