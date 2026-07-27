<?php

namespace App\Livewire\User;

use App\Models\presensi as PresensiModel;
use App\Models\LogBook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class Presensi extends Component
{
    // State Form
    public $tipePresensi = 'masuk'; // 'masuk' atau 'pulang'
    public $logbook = '';
    public $latitude = null;
    public $longitude = null;
    public $fotoCaptured = null;

    // Koordinat Pusat Balai Kota Bogor
    private $targetLat = -6.595181;
    private $targetLng = 106.793836;
    private $maxRadiusMeters = 150;

    protected $messages = [
        'latitude.required' => 'Koordinat lokasi belum terdeteksi. Izinkan akses lokasi di browser!',
        'logbook.required'  => 'Logbook harian wajib diisi saat presensi pulang!',
        'logbook.min'       => 'Isi logbook minimal 10 karakter.',
        'fotoCaptured.required' => 'Foto wajib diambil sebelum mengirim presensi!',
    ];

    public function mount()
    {
        // Cek apakah user sudah presensi masuk hari ini,
        // supaya tab default disesuaikan otomatis
        $presensiHariIni = $this->getPresensiHariIni();

        if ($presensiHariIni && $presensiHariIni->absen_masuk && !$presensiHariIni->absen_keluar) {
            $this->tipePresensi = 'pulang';
        }
    }

    private function getPresensiHariIni()
    {
        return PresensiModel::where('user_id', Auth::id())
            ->whereDate('tanggal', now()->toDateString())
            ->first();
    }

    public function simpanPresensi()
    {
        $rules = [
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'fotoCaptured' => 'required',
        ];

        if ($this->tipePresensi === 'pulang') {
            $rules['logbook'] = 'required|min:10';
        }

        $this->validate($rules);

        // Validasi Geofencing Balai Kota Bogor (backend, tidak bisa dibypass dari frontend)
        $distance = $this->calculateDistance($this->latitude, $this->longitude, $this->targetLat, $this->targetLng);

        if ($distance > $this->maxRadiusMeters) {
            $this->addError('latitude', "Gagal presensi! Anda berada {$distance} meter di luar area Balai Kota Bogor (Maksimal {$this->maxRadiusMeters} meter).");
            return;
        }

        $userId = Auth::id();
        $presensiHariIni = $this->getPresensiHariIni();

        // Simpan foto dari base64 ke storage
        $namaFile = 'presensi_' . $userId . '_' . now()->format('Ymd_His') . '.jpg';
        $fotoPath = $this->simpanFotoBase64($this->fotoCaptured, $namaFile);

        if ($this->tipePresensi === 'masuk') {
            if ($presensiHariIni) {
                $this->addError('fotoCaptured', 'Anda sudah melakukan presensi masuk hari ini.');
                return;
            }

            PresensiModel::create([
                'user_id' => $userId, // pastikan kolom ini ada; kalau tidak, sesuaikan skema
                'tanggal' => now()->toDateString(),
                'absen_masuk' => now()->format('H:i:s'),
                'foto_masuk' => $fotoPath,
                'status_kehadiran' => 'hadir',
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]);
        } else {
            if (!$presensiHariIni) {
                $this->addError('fotoCaptured', 'Anda belum melakukan presensi masuk hari ini.');
                return;
            }

            $presensiHariIni->update([
                'absen_keluar' => now()->format('H:i:s'),
                'foto_keluar' => $fotoPath,
            ]);

            LogBook::create([
                'presensi_id' => $presensiHariIni->presensi_id,
                'user_id' => $userId,
                'kegiatan' => $this->logbook,
            ]);
        }

        $tipe = $this->tipePresensi;

        $this->reset(['logbook', 'fotoCaptured']);
        $this->tipePresensi = 'masuk';

        session()->flash('message', 'Presensi ' . strtoupper($tipe) . ' berhasil dikirim dan tersimpan!');
    }

    private function simpanFotoBase64($base64Image, $namaFile)
    {
        // Hilangkan prefix "data:image/jpeg;base64,"
        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
        $imageData = base64_decode($imageData);

        $path = 'presensi/' . $namaFile;
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