<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\DetailJadwal;
use App\Models\presensi; // Menggunakan huruf kecil 'presensi' sesuai file model Anda
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Dashboard extends Component
{
    /**
     * Properti publik untuk menyimpan kata-kata harian agar dapat diakses oleh Livewire & Blade
     */
    public string $kataKataHariIni = '';

    /**
     * Mengambil kata-kata motivasi hari ini (Berganti otomatis setiap pergantian tanggal)
     */
    public function getKataKataHariIni(): string
    {
        $quotes = [
            'Disiplin adalah jembatan antara tujuan dan pencapaian.',
            'Kesuksesan dimulai dari hal-hal kecil yang dilakukan secara konsisten.',
            'Hari ini adalah kesempatan baru untuk menjadi lebih baik dari kemarin.',
            'Semangat bekerja! Lakukan yang terbaik hari ini dan pantang menyerah.',
            'Kerja keras tidak akan pernah mengkhianati hasil.',
            'Tantangan adalah hal yang membuat hidup menarik. Mengatasinya adalah hal yang membuat hidup bermakna.',
            'Fokus pada tujuan, bukan pada hambatan.',
            'Setiap hari adalah kesempatan untuk belajar dan berkembang.',
            'Jangan menunggu motivasi datang, ciptakanlah sendiri.',
            'Proses tidak pernah membohongi hasil; nikmati setiap tahap belajarmu hari ini.',
            'Kesempatan besar sering kali datang dari pekerjaan kecil yang dikerjakan dengan luar biasa.',
            'Keahlian dibentuk dari latihan berulang, bukan sekadar teori.',
            'Jangan takut bertanya, rasa ingin tahu adalah awal dari semua pengetahuan.',
            'Sikap yang baik di tempat kerja adalah kunci utama membuka pintu kesuksesan.',
            'Setiap tantangan hari ini adalah batu pijakan untuk menjadi profesional masa depan.',
            'Konsistensi adalah apa yang mengubah hal biasa menjadi luar biasa.',
            'Bekerja keraslah dalam diam, biarkan kesuksesanmu yang bersuara.',
            'Kesalahan bukan tanda kegagalan, melainkan kesempatan untuk belajar lebih baik.',
            'Jadikan setiap hari sebagai kesempatan untuk menambah keterampilan baru.',
            'Waktu yang kamu investasikan hari ini adalah fondasi kariermu besok.',
            'Kualitas kerja mencerminkan integritas dan dedikasi dirimu.',
            'Kemauan untuk belajar jauh lebih berharga daripada sekadar bakat alami.',
            'Masa depan milik mereka yang mempersiapkan diri sejak hari ini.',
            'Jadilah solusi di mana pun kamu ditempatkan.',
            'Kedisiplinan adalah bentuk rasa hormat pada dirimu sendiri dan masa depanmu.',
            'Jangan menunggu inspirasi, mulailah bekerja dan inspirasi akan mengikuti.',
            'Kerja sama tim yang baik dimulai dari tanggung jawab individu yang tinggi.',
            'Pengalaman adalah guru terbaik, dan tempat kerja adalah wadah untuk menemukannya.',
            'Kecepatan itu penting, tetapi ketelitian jauh lebih utama.',
            'Jangan bandingkan prosesmu dengan orang lain, fokuslah pada pertumbuhan dirimu.',
            'Inovasi dimulai dari keberanian untuk mencoba hal-hal baru.',
            'Kerjakan setiap tugas dengan sepenuh hati seolah itu adalah karya terbaikmu.',
            'Belajar tidak pernah selesai, bahkan setelah kamu mencapai impianmu.',
            'Sikap pantang menyerah adalah pembeda antara yang berhasil dan yang berhenti.',
            'Percayalah pada potensimu, kamu jauh lebih mampu dari yang kamu bayangkan.',
            'Tetaplah rendah hati saat dipuji, dan tetaplah tangguh saat dikritik.',
            'Hasil terbaik lahir dari fokus yang tidak mudah tergoyahkan.',
            'Mulailah hari dengan energi positif, dan tularkan pada lingkungan sekitarmu.',
            'Langkah terpenting dari perjalanan panjang adalah langkah yang kamu ambil hari ini.',  
        ];

        // Hitung indeks berdasarkan tanggal hari ini agar berganti hanya saat ganti hari
        $today = now()->toDateString();
        $index = abs(crc32($today)) % count($quotes);

        return $quotes[$index];
    }

    public function render()
    {
        // Set lokal Carbon ke Bahasa Indonesia untuk format tanggal
        Carbon::setLocale('id');

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

        // 1. Query: Info Sekolah Terbanyak (Keseluruhan)
        $topSekolahTahunIni = User::where('role', UserRole::PKL->value)
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
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

        // Top 5 Sekolah dengan Peserta PKL Aktif Terbanyak
        $topSekolahAktif = $sekolahAktifData
            ->sortByDesc('total')
            ->take(5)
            ->values();

        // 3. Tren Kehadiran 30 Hari Terakhir (Diagram Garis)
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

        // 4. Peringkat Keterlambatan Bulan Ini
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

        // Mengisi properti publik $kataKataHariIni
        $this->kataKataHariIni = $this->getKataKataHariIni();

        return view('livewire.dashboard.dashboard', [
            'totalPeserta'            => $totalPeserta,
            'hadirHariIni'            => $hadirHariIni,
            'terlambatHariIni'        => $terlambatHariIni,
            'izinSakitHariIni'        => $izinSakitHariIni,
            'alpaHariIni'             => $alpaHariIni,
            'wfhHariIni'              => $wfhHariIni,
            'wfoHariIni'              => $wfoHariIni,
            'topSekolahTahunIni'      => $topSekolahTahunIni,
            'chartSekolahLabels'      => $chartSekolahLabels,
            'chartSekolahTotals'      => $chartSekolahTotals,
            'topSekolahAktif'         => $topSekolahAktif,
            'currentYear'             => $currentYear,
            'trenLabels'              => $trenLabels,
            'trenHadir'               => $trenHadir,
            'trenTerlambat'           => $trenTerlambat,
            'trenIzinSakit'           => $trenIzinSakit,
            'leaderboardTerlambat'    => $leaderboardTerlambat,
            'rataRataJamMasukHariIni' => $rataRataJamMasukHariIni,
            'kataKataHariIni'         => $this->kataKataHariIni,
        ]);
    }

    private function namaHariIndonesia($isoDay): string
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
