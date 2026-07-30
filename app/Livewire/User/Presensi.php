<?php

namespace App\Livewire\User;

use App\Models\log_book;
use App\Models\presensi as PresensiModel;
use App\Models\User;
use App\Enums\UserRole;
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

    // Radius, Status Kerja, & Jam Masuk
    public $isWfa = false;
    public $statusKerja = 'wfo';
    public $jamMasukJadwal = null; // Menyimpan batas jam masuk
    public $maxRadiusMeters = 150;  // Default WFO 150 meter
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
        $this->cekStatusPresensi();
        $this->cekJadwalDanRadius();
    }

    /**
     * Pengecekan Jadwal, Jam Masuk, & Radius berdasarkan Database
     */
    private function cekJadwalDanRadius()
    {
        $userId = $this->currentUserId();

        if (!$userId) {
            return;
        }

        // 1. Dapatkan nama hari ini dalam Bahasa Indonesia
        Carbon::setLocale('id');
        $hariIni = Carbon::now()->translatedFormat('l'); // Hasil: "Senin", "Selasa", dll.

        // 2. Query ke database mencari jadwal user pada hari ini
        $jadwalHariIni = DB::table('detail_jadwals')
            ->join('jadwals', 'detail_jadwals.jadwal_id', '=', 'jadwals.jadwal_id')
            ->where('detail_jadwals.user_id', $userId)
            ->where(DB::raw('LOWER(detail_jadwals.hari)'), strtolower($hariIni))
            ->select('jadwals.status_kerja', 'jadwals.jam_masuk')
            ->first();

        if ($jadwalHariIni) {
            // Simpan jam_masuk dari jadwal (Contoh format dari DB: "08:00:00")
            $this->jamMasukJadwal = $jadwalHariIni->jam_masuk;

            // 3. Tentukan radius berdasarkan status_kerja dari database
            if (strtolower($jadwalHariIni->status_kerja) === 'wfa') {
                $this->isWfa = true;
                $this->statusKerja = 'wfa';
                $this->maxRadiusMeters = 50000000; // Mode WFA (50.000 KM)
            } else {
                $this->isWfa = false;
                $this->statusKerja = 'wfo';
                $this->maxRadiusMeters = 150; // Mode WFO (150 Meter)
            }
        }
    }

    /**
     * Hitung jarak dua titik koordinat (Haversine Formula) dalam satuan meter
     */
    private function hitungJarakMeters($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

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
        $presensiHariIni = $this->getPresensiHariIni();

        // 1. Cek Validasi Logis
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

        // 2. Validasi Form
        $rules = [
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'fotoCaptured' => 'required',
        ];

        if ($this->tipePresensi === 'pulang') {
            $rules['logbook'] = 'required|min:10';
        }

        $this->validate($rules);

        // 3. Validasi Radius Lokasi
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

        // 4. User & File Setup
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

        // 5. Eksekusi Simpan dengan Database Transaction & Error Catching
        DB::beginTransaction();
        try {
            if ($this->tipePresensi === 'masuk') {
                
                if ($presensiHariIni && $presensiHariIni->absen_masuk) {
                    session()->flash('warning', 'Anda sudah melakukan presensi MASUK hari ini!');
                    return;
                }

                $waktuSekarang = now();
                $jamSekarangStr = $waktuSekarang->format('H:i:s');
                
                // Tentukan status_kehadiran (hadir / terlambat)
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

                // Dapatkan Primary Key dari Record Presensi
                $pkName = $presensiHariIni->getKeyName();
                $presensiId = $presensiHariIni->{$pkName};

                // Update jam & foto keluar pada tabel presensi
                $presensiHariIni->update([
                    'absen_keluar' => now()->format('H:i:s'),
                    'foto_keluar'  => $fotoPath,
                ]);

                // Simpan ke tabel log_books
                log_book::create([
                    'presensi_id' => $presensiId,
                    'user_id'     => $user->user_id,
                    'kegiatan'    => $this->logbook,
                ]);

                session()->flash('message', 'Presensi PULANG dan Logbook berhasil dikirim!');
            }

            DB::commit();

            // Reset Form & Refresh State
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