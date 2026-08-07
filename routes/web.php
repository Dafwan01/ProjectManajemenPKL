<?php
use App\Http\Controllers\ForumController;
use App\Http\Controllers\SertifikatController;
use App\Livewire\Components\CetakNilai;
use App\Livewire\Components\CetakRekapAbsensi;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Dashboard\Log;
use App\Livewire\Dashboard\ManajemenAkun;
use App\Livewire\Dashboard\ManajemenPkl;
use App\Livewire\Dashboard\MonitoringAbsensi;
use App\Livewire\Dashboard\PermohonanIzin;
use App\Livewire\Dashboard\Profile as DashboardProfile;
use App\Livewire\Dashboard\RekapAbsensi;
use App\Livewire\Dashboard\UploadFile\Nilai;
use App\Livewire\Dashboard\UploadFile\Sertifikat;
use App\Livewire\Dashboard\UploadFile\SuratPenerimaanMagang;
use App\Livewire\Forum;
use App\Livewire\ForumDetail;
use App\Livewire\Login;
use App\Livewire\User\Dokumen;
use App\Livewire\User\IzinSakit;
use App\Livewire\User\Jadwal;
use App\Livewire\User\Presensi;
use App\Livewire\User\Profile;
use App\Livewire\User\Project as UserProject;
use App\Livewire\User\Riwayat;
use App\Models\project as ProjectModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// ==========================================
// 1. PUBLIC / GUEST ROUTES (Tamu)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('header');
    Route::get('/login', Login::class)->name('login');
});

// ==========================================
// 2. LOGOUT & UTILITY ROUTES (Auth Umum)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

    Route::get('/project/{id}/download', function ($id) {
        $project = ProjectModel::findOrFail($id);

        abort_unless($project->file_project, 404);

        if (!Storage::disk('public')->exists($project->file_project)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        return Storage::disk('public')->download($project->file_project);
    })->name('project.download');

    Route::get('/sertifikat/saya', [SertifikatController::class, 'downloadSaya'])->name('sertifikat.saya');
    
});

// ==========================================
// 3. DASHBOARD ROUTES (Khusus Admin & Mentor)
// ==========================================
Route::middleware(['auth', 'role:admin,mentor'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/manajemen-akun', ManajemenAkun::class)->name('manajemen-akun');
    Route::get('/manajemen-pkl', ManajemenPkl::class)->name('manajemen-pkl');
    Route::get('/monitoring-absensi', MonitoringAbsensi::class)->name('monitoring-absensi');
    Route::get('/rekap-absensi', RekapAbsensi::class)->name('rekap-absensi');
    Route::get('/permohonan-izin', PermohonanIzin::class)->name('permohonan-izin');
    Route::get('/log', Log::class)->name('log');
    Route::get('/profile', DashboardProfile::class)->name('profile');

    // Upload & Document Routes
    Route::get('/surat-penerimaan-magang', SuratPenerimaanMagang::class)->name('surat-penerimaan-magang');
    Route::get('/sertifikat', Sertifikat::class)->name('sertifikat');
    Route::get('/nilai', Nilai::class)->name('nilai');

    // Cetak Routes
    Route::get('/cetak-rekap-absensi/{userId}', CetakRekapAbsensi::class)->name('cetak.rekap-absensi');
    Route::get('/cetak-nilai/{userId}', CetakNilai::class)->name('cetak.nilai');
});

// ==========================================
// 4. USER ROUTES (Khusus Siswa/Mahasiswa PKL)
// ==========================================
Route::middleware(['auth', 'role:PKL'])->group(function () {
    Route::get('/user/presensi', Presensi::class)->name('user.presensi');
    Route::get('/user/riwayat', Riwayat::class)->name('user.riwayat');
    Route::get('/user/izin-sakit', IzinSakit::class)->name('user.izin-sakit');
    Route::get('/user/profile', Profile::class)->name('user.profile');
    Route::get('/user/dokumen', Dokumen::class)->name('user.dokumen');
    Route::get('/user/project', UserProject::class)->name('user.project');
      Route::get('/user/jadwal', Jadwal::class)->name('jadwal');
 
});

Route::middleware(['auth'])->group(function () {
    Route::get('/forum', Forum::class)->name('forum');
    Route::get('/forum/{forum}', ForumDetail::class)->name('forum.show');
});