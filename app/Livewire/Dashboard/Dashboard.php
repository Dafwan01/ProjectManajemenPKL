<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\presensi; 
use App\Models\DetailJadwal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $today = now()->toDateString();
        $namaHariIni = $this->namaHariIndonesia(now()->dayOfWeekIso); // 1 (Senin) - 7 (Minggu)

        $totalPeserta = User::where('role', UserRole::PKL->value)->count();

        // Hadir hari ini: presensi hari ini dengan status_kehadiran = hadir
        $hadirHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'hadir')
            ->count();

        // Terlambat hari ini
        $terlambatHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status_kehadiran', 'terlambat')
            ->count();

        // Izin / Sakit hari ini
        $izinSakitHariIni = Presensi::whereDate('tanggal', $today)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])
            ->count();

        // Belum Absen / Alpa = Total Peserta dikurangi (Hadir + Terlambat + Izin/Sakit)
        $totalSudahAbsen = $hadirHariIni + $terlambatHariIni + $izinSakitHariIni;
        $alpaHariIni = max(0, $totalPeserta - $totalSudahAbsen);

        // WFH & WFO hari ini: dari jadwal user PKL sesuai hari ini
        $userIdsPkl = User::where('role', UserRole::PKL->value)->pluck('user_id');

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