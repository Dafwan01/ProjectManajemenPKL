<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\DetailJadwal;
use App\Models\presensi; 
use App\Models\User;
use Illuminate\Support\Carbon;
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

        // Top 5 Sekolah dengan Peserta PKL Aktif Terbanyak.
        // Reuse dari $sekolahAktifData yang sudah difilter status aktif di atas,
        // tinggal diurutkan descending berdasarkan total peserta.
        $topSekolahAktif = $sekolahAktifData
            ->sortByDesc('total')
            ->take(5)
            ->values();

        // 3. Tren Kehadiran 30 Hari Terakhir (Line Chart)
        // Diambil per tanggal: jumlah Hadir, Terlambat, dan Izin/Sakit.
        // Alpa tidak dihitung per hari di sini karena totalPeserta bisa berubah
        // dari waktu ke waktu (peserta baru masuk / lulus), jadi fokus ke 3 status
        // yang datanya memang tercatat langsung di tabel presensi.
        $tanggalMulaiTren = now()->subDays(29)->startOfDay();
        $tanggalAkhirTren = now()->endOfDay();

        $trenMentahPerTanggal = presensi::whereIn('user_id', $userIdsPkl)
            ->whereBetween('tanggal', [$tanggalMulaiTren->toDateString(), $tanggalAkhirTren->toDateString()])
            ->select(
                DB::raw('DATE(tanggal) as tgl'),
                DB::raw("SUM(CASE WHEN status_kehadiran = 'hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status_kehadiran = 'terlambat' THEN 1 ELSE 0 END) as terlambat"),
                DB::raw("SUM(CASE WHEN status_kehadiran IN ('izin', 'sakit') THEN 1 ELSE 0 END) as izin_sakit")
            )
            ->groupBy('tgl')
            ->get()
            ->keyBy('tgl');

        $trenLabels = [];
        $trenHadir = [];
        $trenTerlambat = [];
        $trenIzinSakit = [];

        $cursorTren = $tanggalMulaiTren->copy();
        while ($cursorTren->lte($tanggalAkhirTren)) {
            $tglKey = $cursorTren->toDateString();
            $baris = $trenMentahPerTanggal->get($tglKey);

            $trenLabels[] = $cursorTren->translatedFormat('d M');
            $trenHadir[] = $baris->hadir ?? 0;
            $trenTerlambat[] = $baris->terlambat ?? 0;
            $trenIzinSakit[] = $baris->izin_sakit ?? 0;

            $cursorTren->addDay();
        }

        // 4. Leaderboard Keterlambatan Bulan Ini
        $leaderboardTerlambat = presensi::whereIn('user_id', $userIdsPkl)
            ->where('status_kehadiran', 'terlambat')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->select('user_id', DB::raw('count(*) as total_terlambat'))
            ->groupBy('user_id')
            ->orderByDesc('total_terlambat')
            ->take(5)
            ->with('user:user_id,nama')
            ->get();

        // 5. Rata-rata Jam Masuk Hari Ini
        // Dihitung dari kolom jam_masuk pada tabel presensi (format waktu H:i:s / H:i).
        // Detik dikonversi ke angka lewat TIME_TO_SEC lalu dirata-rata, baru diformat balik ke jam:menit.
        $rataRataJamMasukHariIni = null;

        $avgDetikJamMasuk = presensi::whereIn('user_id', $userIdsPkl)
            ->whereDate('tanggal', $today)
            ->whereIn('status_kehadiran', ['hadir', 'terlambat'])
            ->whereNotNull('absen_masuk')
            ->selectRaw('AVG(TIME_TO_SEC(absen_masuk)) as rata_detik')
            ->value('rata_detik');

        if (!is_null($avgDetikJamMasuk)) {
            $rataRataJamMasukHariIni = Carbon::createFromTime(0, 0, 0)
                ->addSeconds((int) round($avgDetikJamMasuk))
                ->format('H:i');
        }

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
            'topSekolahAktif' => $topSekolahAktif,
            'currentYear' => $currentYear,
            'trenLabels' => $trenLabels,
            'trenHadir' => $trenHadir,
            'trenTerlambat' => $trenTerlambat,
            'trenIzinSakit' => $trenIzinSakit,
            'leaderboardTerlambat' => $leaderboardTerlambat,
            'rataRataJamMasukHariIni' => $rataRataJamMasukHariIni,
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