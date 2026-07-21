<?php

use App\Livewire\Dashboard;
use App\Livewire\Form\Akun;
use App\Livewire\Login;
use App\Livewire\ManajemenAkun;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('components.header');
// });

// Route::livewire('/home', 'pages::components.header');
Route::get('/', Login::class)->name('header');
Route::get('/dashboard', Dashboard::class)->name('dashboard ');
Route::get('/manajemen-akun', ManajemenAkun::class)->name('manajemen-akun');
Route::get('/akun', Akun::class)->name('akun');
