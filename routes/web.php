<?php

use App\Livewire\Header;
use App\Livewire\Login;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('components.header');
// });

// Route::livewire('/home', 'pages::components.header');
Route::get('/', Login::class)->name('header');
Route::get('/dashboard', Header::class)->name('header');
