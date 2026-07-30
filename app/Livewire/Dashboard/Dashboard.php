<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\DetailJadwal;
use App\Models\presensi; 
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Import Auth Facade
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
        $namaHariIni = $this->namaHariIndonesia(now()->dayOfWeekIso); // 1 (Senin) - 7 (Minggu)

        // Query dasar peserta PKL (Saring berdasarkan nama mentor jika yang login adalah Mentor)
        $pklQuery = User::where('role', UserRole::PKL->value)
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            });

        $totalPeserta = (clone $pklQuery)->count();
        $userIdsPkl = (clone $pklQuery)->pluck('user_id');

        // Hadir hari ini: presensi hari ini dengan status_kehadiran = hadir (khusus user PKL yang relevan)
        $hadirHariIni = presensi::whereIn('user_id', $userIdsPkl)
            ->whereDate('tanggal', $today)
            ->where('status_kehadiran', 'hadir')
            ->count();

        // Terlambat hari ini
        $terlambatHariIni = presensi::whereIn('user_id', $userIdsPkl)
            ->whereDate('tanggal', $today)
            ->where('status_kehadiran', 'terlambat')
            ->count();

        // Izin / Sakit hari ini
        $izinSakitHariIni = presensi::whereIn('user_id', $userIdsPkl)
            ->whereDate('tanggal', $today)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])
            ->count();

        // Belum Absen / Alpa = Total Peserta dikurangi (Hadir + Terlambat + Izin/Sakit)
        $totalSudahAbsen = $hadirHariIni + $terlambatHariIni + $izinSakitHariIni;
        $alpaHariIni = max(0, $totalPeserta - $totalSudahAbsen);

        // WFH & WFO hari ini: dari jadwal user PKL yang relevan
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

        return view('livewire.dashboard.dashboard', [
            'totalPeserta' => $totalPeserta,
            'hadirHariIni' => $hadirHariIni,
            'terlambatHariIni' => $terlambatHariIni,
            'izinSakitHariIni' => $izinSakitHariIni,
            'alpaHariIni' => $alpaHariIni,
            'wfhHariIni' => $wfhHariIni,
            'wfoHariIni' => $wfoHariIni,
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