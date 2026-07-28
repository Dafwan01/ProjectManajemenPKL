<?php

namespace App\Livewire\User;

use App\Models\log_book;
use App\Models\presensi as PresensiModel;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // <-- Tambahkan import Str
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

    private $targetLat = -6.595181;
    private $targetLng = 106.793836;
    private $maxRadiusMeters = 10000000;

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
    }

    private function currentUser()
    {
        return Auth::user() ?? User::where('role', UserRole::PKL)->first();
    }

    private function currentUserId()
    {
        return $this->currentUser()?->user_id;
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

        // 1. Cek jika sudah selesai semua presensi hari ini
        if ($presensiHariIni && $presensiHariIni->absen_masuk && $presensiHariIni->absen_keluar) {
            session()->flash('warning', 'Anda sudah menyelesaikan presensi masuk dan keluar untuk hari ini!');
            return;
        }

        // 2. Validasi Input Form
        $rules = [
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'fotoCaptured' => 'required',
        ];

        if ($this->tipePresensi === 'pulang') {
            $rules['logbook'] = 'required|min:10';
        }

        $this->validate($rules);

        // 3. Validasi Geofencing
        $distance = $this->calculateDistance($this->latitude, $this->longitude, $this->targetLat, $this->targetLng);

        if ($distance > $this->maxRadiusMeters) {
            $this->addError('latitude', "Gagal presensi! Anda berada {$distance} meter di luar area Balai Kota Bogor.");
            return;
        }

        // 4. Format Nama File: namauser-tanggal-masuk/pulang.jpg
        $user = $this->currentUser();

        if (! $user) {
            session()->flash('warning', 'Tidak ada user yang tersedia untuk presensi.');
            return;
        }

        $namaUserSlug = Str::slug($user->nama ?? 'user'); // Mengubah misal "Ahmad Dani" jadi "ahmad-dani"
        $tanggalHariIni = Carbon::today()->format('Y-m-d');
        $tipe = $this->tipePresensi; // 'masuk' atau 'pulang'

        $namaFile = "{$namaUserSlug}-{$tanggalHariIni}-{$tipe}.jpg";
        $fotoPath = $this->simpanFotoBase64($this->fotoCaptured, $namaFile);

        // 5. Eksekusi Simpan Data
        if ($this->tipePresensi === 'masuk') {
            
            if ($presensiHariIni && $presensiHariIni->absen_masuk) {
                session()->flash('warning', 'Anda sudah melakukan presensi MASUK hari ini!');
                return;
            }

            PresensiModel::create([
                'user_id'          => $user->id,
                'tanggal'          => $tanggalHariIni,
                'absen_masuk'      => now()->format('H:i:s'),
                'foto_masuk'       => $fotoPath,
                'status_kehadiran' => 'hadir',
                'latitude'         => $this->latitude,
                'longitude'        => $this->longitude,
            ]);

            session()->flash('message', 'Presensi MASUK berhasil dikirim!');

        } else {
            
            if (!$presensiHariIni || !$presensiHariIni->absen_masuk) {
                session()->flash('warning', 'Anda belum melakukan presensi MASUK hari ini!');
                return;
            }

            if ($presensiHariIni->absen_keluar) {
                session()->flash('warning', 'Anda sudah melakukan presensi PULANG hari ini!');
                return;
            }

            $presensiHariIni->update([
                'absen_keluar' => now()->format('H:i:s'),
                'foto_keluar'  => $fotoPath,
            ]);

            log_book::create([
                'presensi_id' => $presensiHariIni->presensi_id,
                'user_id'     => $user->id,
                'kegiatan'    => $this->logbook,
            ]);

            session()->flash('message', 'Presensi PULANG dan Logbook berhasil dikirim!');
        }

        // Reset Form & Refresh Status
        $this->reset(['logbook', 'fotoCaptured']);
        $this->cekStatusPresensi();
    }

    private function simpanFotoBase64($base64Image, $namaFile)
    {
        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
        $imageData = base64_decode($imageData);

        $path = 'presensi/' . $namaFile;
        
        // Simpan file (otomatis menimpa jika nama file persis sama)
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
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

        return round($angle * $earthRadius);
    }

    public function render()
    {
        return view('livewire.user.presensi')
            ->layout('layouts.user');
    }
}