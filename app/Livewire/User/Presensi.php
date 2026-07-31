<?php

namespace App\Livewire\User;

use App\Models\log_book;
use App\Models\presensi as PresensiModel;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
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

    // Flag status presensi hari ini
    public $sudahAbsenMasuk = false;
    public $sudahAbsenKeluar = false;

    // Flag status kelulusan user
    public bool $isLulus = false;

    // Radius, Status Kerja, & Jam Masuk
    public $isWfa = false;
    public $statusKerja = 'wfo';
    public $jamMasukJadwal = null;
    public $maxRadiusMeters = 150;
    public $targetLat = -6.595181;
    public $targetLng = 106.793836;

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

    private function cekJadwalDanRadius()
    {
        $userId = $this->currentUserId();

        if (!$userId) {
            return;
        }

        Carbon::setLocale('id');
        $hariIni = Carbon::now()->translatedFormat('l');

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

                // Ambil logbook yang mungkin sudah pernah diisi via Halaman Riwayat
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
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->first();
    }

    public function simpanPresensi()
    {
        if ($this->isLulus) {
            session()->flash('warning', 'Gagal Presensi! Akun Anda telah berstatus LULUS.');
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

        $namaUserSlug = Str::slug($user->nama ?? 'user');
        $tanggalHariIni = Carbon::today()->format('Y-m-d');
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

                $waktuSekarang = now();
                $jamSekarangStr = $waktuSekarang->format('H:i:s');
                
                $statusKehadiran = 'hadir';
                if ($this->jamMasukJadwal && $jamSekarangStr > $this->jamMasukJadwal) {
                    $statusKehadiran = 'terlambat';
                }

                PresensiModel::create([
                    'user_id'          => $user->user_id,
                    'tanggal'          => $tanggalHariIni,
                    'absen_masuk'      => $jamSekarangStr,
                    'foto_masuk'       => $fotoPath,
                    'status_kehadiran' => $statusKehadiran,
                    'latitude'         => $this->latitude,
                    'longitude'        => $this->longitude,
                ]);

                $pesan = $statusKehadiran === 'terlambat' 
                    ? 'Presensi MASUK berhasil dikirim (Terlambat)!' 
                    : 'Presensi MASUK berhasil dikirim!';

                session()->flash('message', $pesan);

            } else {

                $pkName = $presensiHariIni->getKeyName();
                $presensiId = $presensiHariIni->{$pkName};

                $presensiHariIni->update([
                    'absen_keluar' => now()->format('H:i:s'),
                    'foto_keluar'  => $fotoPath,
                ]);

                // Menggunakan updateOrCreate agar tidak menciptakan duplikasi logbook
                log_book::updateOrCreate(
                    [
                        'presensi_id' => $presensiId,
                    ],
                    [
                        'user_id'  => $user->user_id,
                        'kegiatan' => $this->logbook,
                    ]
                );

                session()->flash('message', 'Presensi PULANG dan Logbook berhasil dikirim!');
            }

            DB::commit();

            $this->reset(['logbook', 'fotoCaptured']);
            $this->cekStatusPresensi();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('warning', 'Gagal menyimpan: ' . $e->getMessage());
        }
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