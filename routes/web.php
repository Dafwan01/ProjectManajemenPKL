<?php

use App\Livewire\Components\Bottomnav;
use App\Models\project;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Dashboard\ManajemenAkun;
use App\Livewire\Dashboard\ManajemenPkl;
use App\Livewire\Dashboard\MonitoringAbsensi;
use App\Livewire\Dashboard\UploadFile\Nilai;
use App\Livewire\Dashboard\UploadFile\Sertifikat;
use App\Livewire\Dashboard\UploadFile\SuratPenerimaanMagang;
use App\Livewire\Login;
use App\Livewire\User\Presensi;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('components.header');
// });

// Route::livewire('/home', 'pages::components.header');
Route::get('/', Login::class)->name('header');
Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/manajemen-akun', ManajemenAkun::class)->name('manajemen-akun');
Route::get('/manajemen-pkl', ManajemenPkl::class)->name('manajemen-pkl');
Route::get('/monitoring-absensi', MonitoringAbsensi::class)->name('monitoring-absensi');
Route::get('/project/{project}/download', function (project $project) {
    abort_unless($project->file_project, 404);
    return Storage::download($project->file_project);
})->name('project.download');
Route::get('/surat-penerimaan-magang', SuratPenerimaanMagang::class)->name('surat-penerimaan-magang');
Route::get('/sertifikat', Sertifikat::class)->name('sertifikat');
Route::get('/nilai', Nilai::class)->name('nilai');
Route::get('/bottomnav', Bottomnav::class)->name('bottomnav');
Route::get('/user/presensi', Presensi::class);