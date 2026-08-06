<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\DetailJadwal;
use App\Models\presensi; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value;

        $today = now()->toDateString();
        $currentYear = now()->year; 
        $namaHariIni = $this->namaHariIndonesia(now()->dayOfWeekIso); 

        // Query dasar peserta PKL
        $pklQuery = User::where('role', UserRole::PKL->value)
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            });

        $totalPeserta = (clone $pklQuery)->count();
        $userIdsPkl = (clone $pklQuery)->pluck('user_id');

        // Hadir, Terlambat, Izin/Sakit, Alpa
        $hadirHariIni = presensi::whereIn('user_id', $userIdsPkl)
            ->whereDate('tanggal', $today)
            ->where('status_kehadiran', 'hadir')
            ->count();

        $terlambatHariIni = presensi::whereIn('user_id', $userIdsPkl)
            ->whereDate('tanggal', $today)
            ->where('status_kehadiran', 'terlambat')
            ->count();

        $izinSakitHariIni = presensi::whereIn('user_id', $userIdsPkl)
            ->whereDate('tanggal', $today)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])
            ->count();

        $totalSudahAbsen = $hadirHariIni + $terlambatHariIni + $izinSakitHariIni;
        $alpaHariIni = max(0, $totalPeserta - $totalSudahAbsen);

        // WFH & WFO
        $wfhHariIni = DetailJadwal::whereIn('user_id', $userIdsPkl)
            ->where('hari', $namaHariIni)
            ->whereHas('jadwal', function ($query) {
                $query->where('status_kerja', 'WFH');
            })
            ->count();

        $wfoHariIni = DetailJadwal::whereIn('user_id', $userIdsPkl)
            ->where('hari', $namaHariIni)
            ->whereHas('jadwal', function ($query) {
                $query->where('status_kerja', 'WFO');
            })
            ->count();

        // 1. Query: Info Sekolah Terbanyak (Keseluruhan, tanpa filter created_at agar tidak error)
        $topSekolahTahunIni = User::where('role', UserRole::PKL->value)
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            // NOTE: Jika di database ada kolom 'tanggal_mulai', silakan hapus komentar pada kode di bawah:
            // ->whereYear('tanggal_mulai', $currentYear)
            ->whereNotNull('sekolah_id')
            ->select('sekolah_id', DB::raw('count(*) as total'))
            ->groupBy('sekolah_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('sekolah')
            ->get();

        // 2. Query: Data untuk Grafik Sekolah PKL Aktif
        $sekolahAktifData = User::where('role', UserRole::PKL->value)
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->whereIn('status', ['aktif', 'AKTIF']) 
            ->whereNotNull('sekolah_id')
            ->select('sekolah_id', DB::raw('count(*) as total'))
            ->groupBy('sekolah_id')
            ->with('sekolah')
            ->get();

        $chartSekolahLabels = $sekolahAktifData->map(fn($item) => $item->sekolah->nama_sekolah ?? 'Tidak Diketahui')->toArray();
        $chartSekolahTotals = $sekolahAktifData->map(fn($item) => $item->total)->toArray();

        return view('livewire.dashboard.dashboard', [
            'totalPeserta' => $totalPeserta,
            'hadirHariIni' => $hadirHariIni,
            'terlambatHariIni' => $terlambatHariIni,
            'izinSakitHariIni' => $izinSakitHariIni,
            'alpaHariIni' => $alpaHariIni,
            'wfhHariIni' => $wfhHariIni,
            'wfoHariIni' => $wfoHariIni,
            'topSekolahTahunIni' => $topSekolahTahunIni,
            'chartSekolahLabels' => $chartSekolahLabels,
            'chartSekolahTotals' => $chartSekolahTotals,
            'currentYear' => $currentYear,
        ]);
    }

    private function namaHariIndonesia($isoDay)
    {
        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        return $hari[$isoDay] ?? '';
    }
}