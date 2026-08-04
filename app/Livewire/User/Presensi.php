<?php

namespace App\Livewire\User;

use App\Models\log_book;
use App\Models\presensi as PresensiModel;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Notifications\PresensiTerlambatNotification;
use App\Services\WorldTimeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Carbon\Carbon;

class Presensi extends Component
{
    public $tipePresensi = 'masuk';
    public $logbook = '';
    public $latitude = null;
    public $longitude = null;
    public $fotoCaptured = null;

    public $sudahAbsenMasuk = false;
    public $sudahAbsenKeluar = false;

    public bool $isLulus = false;

    // Flag hari libur (Sabtu/Minggu)
    public bool $isWeekend = false;
    public string $namaHariIni = '';

    public $isWfa = false;
    public $statusKerja = 'wfo';
    public $jamMasukJadwal = null;
    public $maxRadiusMeters = 150;
    public $targetLat = -6.595181;
    public $targetLng = 106.793836;

    public bool $waktuFallbackServer = false;

    protected $messages = [
        'latitude.required'     => 'Koordinat lokasi belum terdeteksi. Izinkan akses lokasi di browser!',
        'longitude.required'    => 'Koordinat lokasi belum terdeteksi.',
        'logbook.required'      => 'Logbook harian wajib diisi saat presensi pulang!',
        'logbook.min'           => 'Isi logbook minimal 10 karakter.',
        'fotoCaptured.required' => 'Foto wajib diambil sebelum mengirim presensi!',
    ];

    public function mount()
    {
        $this->cekUserStatus();
        $this->cekHariLibur();
        $this->cekStatusPresensi();
        $this->cekJadwalDanRadius();
    }

    private function cekUserStatus()
    {
        $user = $this->currentUser();
        if (!$user) {
            return;
        }

        $userStatus = $user->status instanceof \UnitEnum ? $user->status->value : $user->status;

        if (strtolower((string) $userStatus) === 'lulus' || $userStatus === UserStatus::LULUS->value) {
            $this->isLulus = true;
            session()->flash('warning', 'Status akun Anda adalah LULUS. Anda tidak dapat melakukan presensi lagi.');
        }
    }

    /**
     * Cek apakah hari ini Sabtu/Minggu berdasarkan waktu dunia (bukan waktu device/server).
     */
    private function cekHariLibur()
    {
        $waktuSekarang = WorldTimeService::now();
        Carbon::setLocale('id');
        $this->namaHariIni = $waktuSekarang->translatedFormat('l');

        // isWeekend() Carbon: true jika Sabtu (6) atau Minggu (0)
        $this->isWeekend = $waktuSekarang->isWeekend();

        if ($this->isWeekend) {
            session()->flash('warning', 'Hari ini adalah ' . $this->namaHariIni . '. Presensi tidak dapat dilakukan pada akhir pekan (Sabtu/Minggu).');
        }
    }

    private function cekJadwalDanRadius()
    {
        $userId = $this->currentUserId();

        if (!$userId) {
            return;
        }

        Carbon::setLocale('id');
        $hariIni = WorldTimeService::now()->translatedFormat('l');

        $jadwalHariIni = DB::table('detail_jadwals')
            ->join('jadwals', 'detail_jadwals.jadwal_id', '=', 'jadwals.jadwal_id')
            ->where('detail_jadwals.user_id', $userId)
            ->where(DB::raw('LOWER(detail_jadwals.hari)'), strtolower($hariIni))
            ->select('jadwals.status_kerja', 'jadwals.jam_masuk')
            ->first();

        if ($jadwalHariIni) {
            $this->jamMasukJadwal = $jadwalHariIni->jam_masuk;

            if (strtolower($jadwalHariIni->status_kerja) === 'wfh') {
                $this->isWfa = true;
                $this->statusKerja = 'wfh';
                $this->maxRadiusMeters = 50000000;
            } else {
                $this->isWfa = false;
                $this->statusKerja = 'wfo';
                $this->maxRadiusMeters = 150;
            }
        }
    }

    private function hitungJarakMeters($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                 cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    private function currentUser()
    {
        return Auth::user() ?? User::where('role', UserRole::PKL)->first();
    }

    private function currentUserId()
    {
        return $this->currentUser()?->user_id;
    }

    public function setTipePresensi($tipe)
    {
        $this->tipePresensi = $tipe;
        $this->resetErrorBag();
    }

    private function cekStatusPresensi()
    {
        $presensiHariIni = $this->getPresensiHariIni();

        if ($presensiHariIni) {
            $this->sudahAbsenMasuk = !is_null($presensiHariIni->absen_masuk);
            $this->sudahAbsenKeluar = !is_null($presensiHariIni->absen_keluar);

            if ($this->sudahAbsenMasuk && !$this->sudahAbsenKeluar) {
                $this->tipePresensi = 'pulang';

                $existingLogbook = log_book::where('presensi_id', $presensiHariIni->presensi_id)->first();
                if ($existingLogbook) {
                    $this->logbook = $existingLogbook->kegiatan;
                }
            }
        }
    }

    private function getPresensiHariIni()
    {
        return PresensiModel::where('user_id', $this->currentUserId())
            ->whereDate('tanggal', WorldTimeService::now()->toDateString())
            ->first();
    }

    public function simpanPresensi()
    {
        if ($this->isLulus) {
            session()->flash('warning', 'Gagal Presensi! Akun Anda telah berstatus LULUS.');
            return;
        }

        // Guard: blokir presensi di hari Sabtu/Minggu (dicek ulang di sini agar tidak bisa diakali walau form sempat termuat)
        $waktuSekarang = WorldTimeService::now();
        if ($waktuSekarang->isWeekend()) {
            Carbon::setLocale('id');
            session()->flash('warning', 'Gagal Presensi! Hari ini adalah ' . $waktuSekarang->translatedFormat('l') . '. Presensi tidak dapat dilakukan pada akhir pekan.');
            return;
        }

        $presensiHariIni = $this->getPresensiHariIni();

        if ($this->tipePresensi === 'pulang') {
            if (!$presensiHariIni || !$presensiHariIni->absen_masuk) {
                session()->flash('warning', 'Anda belum melakukan presensi MASUK hari ini!');
                return;
            }
            if ($presensiHariIni->absen_keluar) {
                session()->flash('warning', 'Anda sudah melakukan presensi PULANG hari ini!');
                return;
            }
        }

        $rules = [
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'fotoCaptured' => 'required',
        ];

        if ($this->tipePresensi === 'pulang') {
            $rules['logbook'] = 'required|min:10';
        }

        $this->validate($rules);

        $jarakUser = $this->hitungJarakMeters(
            $this->latitude,
            $this->longitude,
            $this->targetLat,
            $this->targetLng
        );

        if ($jarakUser > $this->maxRadiusMeters) {
            session()->flash('warning', 'Gagal Presensi! Lokasi Anda terlalu jauh dari lokasi kantor (' . round($jarakUser) . ' meter).');
            return;
        }

        $user = $this->currentUser();
        if (!$user) {
            session()->flash('warning', 'User tidak ditemukan.');
            return;
        }

        $waktuDunia = $waktuSekarang;
        $this->waktuFallbackServer = !WorldTimeService::isFromApi();

        $namaUserSlug = Str::slug($user->nama ?? 'user');
        $tanggalHariIni = $waktuDunia->format('Y-m-d');
        $tipe = $this->tipePresensi;

        $namaFile = "{$namaUserSlug}-{$tanggalHariIni}-{$tipe}.jpg";
        $fotoPath = $this->simpanFotoBase64($this->fotoCaptured, $namaFile);

        DB::beginTransaction();
        try {
            if ($this->tipePresensi === 'masuk') {

                if ($presensiHariIni && $presensiHariIni->absen_masuk) {
                    session()->flash('warning', 'Anda sudah melakukan presensi MASUK hari ini!');
                    return;
                }

                $jamSekarangStr = $waktuDunia->format('H:i:s');

                $statusKehadiran = 'hadir';
                if ($this->jamMasukJadwal && $jamSekarangStr > $this->jamMasukJadwal) {
                    $statusKehadiran = 'terlambat';
                }

                $presensiBaru = PresensiModel::create([
                    'user_id'          => $user->user_id,
                    'tanggal'          => $tanggalHariIni,
                    'absen_masuk'      => $jamSekarangStr,
                    'foto_masuk'       => $fotoPath,
                    'status_kehadiran' => $statusKehadiran,
                    'latitude'         => $this->latitude,
                    'longitude'        => $this->longitude,
                ]);

                if ($statusKehadiran === 'terlambat') {
                    $this->notifikasiTerlambat($presensiBaru, $user);
                }

                $pesan = $statusKehadiran === 'terlambat'
                    ? 'Presensi MASUK berhasil dikirim (Terlambat)!'
                    : 'Presensi MASUK berhasil dikirim!';

                if ($this->waktuFallbackServer) {
                    $pesan .= ' (Catatan: waktu dunia tidak dapat diakses, menggunakan waktu server sebagai cadangan.)';
                }

                session()->flash('message', $pesan);

            } else {

                $pkName = $presensiHariIni->getKeyName();
                $presensiId = $presensiHariIni->{$pkName};

                $presensiHariIni->update([
                    'absen_keluar' => $waktuDunia->format('H:i:s'),
                    'foto_keluar'  => $fotoPath,
                ]);

                log_book::updateOrCreate(
                    [
                        'presensi_id' => $presensiId,
                    ],
                    [
                        'user_id'  => $user->user_id,
                        'kegiatan' => $this->logbook,
                    ]
                );

                $pesan = 'Presensi PULANG dan Logbook berhasil dikirim!';
                if ($this->waktuFallbackServer) {
                    $pesan .= ' (Catatan: waktu dunia tidak dapat diakses, menggunakan waktu server sebagai cadangan.)';
                }

                session()->flash('message', $pesan);
            }

            DB::commit();

            $this->reset(['logbook', 'fotoCaptured']);
            $this->cekStatusPresensi();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('warning', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function notifikasiTerlambat(PresensiModel $presensiBaru, User $siswa): void
    {
        $penerima = User::where('role', UserRole::ADMIN)->get();

        $mentor = $this->resolveMentor($siswa);
        if ($mentor) {
            $penerima->push($mentor);
        }

        foreach ($penerima->unique('user_id') as $tujuan) {
            $tujuan->notify(new PresensiTerlambatNotification($presensiBaru));
        }
    }

    private function resolveMentor(User $siswa): ?User
    {
        if (empty($siswa->mentor)) {
            return null;
        }

        // Kemungkinan 1: kolom 'mentor' berisi user_id (FK ke tabel users)
        if (is_numeric($siswa->mentor)) {
            $mentor = User::where('user_id', $siswa->mentor)
                ->where('role', UserRole::MENTOR)
                ->first();
            if ($mentor) {
                return $mentor;
            }
        }

        // Kemungkinan 2: kolom 'mentor' berisi nama, cocokkan ke user ber-role mentor
        return User::where('role', UserRole::MENTOR)
            ->where('nama', $siswa->mentor)
            ->first();
    }

    private function simpanFotoBase64($base64Image, $namaFile)
    {
        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
        $imageData = base64_decode($imageData);

        $path = 'presensi/' . $namaFile;
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }

    public function render()
    {
        return view('livewire.user.presensi')
            ->layout('layouts.user');
    }
}