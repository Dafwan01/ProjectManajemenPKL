<?php

namespace App\Livewire\User;

use Livewire\Component;

class Presensi extends Component
{
    public $nama = 'Jonathan';
    public $jabatan = 'PKL';
    public $opd = 'Dinas Komunikasi dan Informatika';
    public $bidang = 'Magang Aplikasi Informatika';
    
    // State Form
    public $tipePresensi = 'masuk'; // 'masuk' atau 'pulang'
    public $logbook = '';
    public $latitude = null;
    public $longitude = null;
    public $fotoCaptured = null;

    protected $messages = [
        'latitude.required' => 'Koordinat lokasi belum terdeteksi. Izinkan akses lokasi di browser!',
        'logbook.required'  => 'Logbook harian wajib diisi saat presensi pulang!',
        'logbook.min'       => 'Isi logbook minimal 10 karakter.',
        'fotoCaptured.required' => 'Foto wajib diambil sebelum mengirim presensi!',
    ];

    public function simpanPresensi()
    {
        // Dynamic Validation Rules
        $rules = [
            'latitude'     => 'required',
            'longitude'    => 'required',
            'fotoCaptured' => 'required',
        ];

        if ($this->tipePresensi === 'pulang') {
            $rules['logbook'] = 'required|min:10';
        }

        $this->validate($rules);

        // Ambil data riwayat yang sudah ada di Session (jika ada)
        $riwayatSession = session()->get('riwayat_presensi', []);

        // Buat data presensi baru
        $dataBaru = [
            'id' => count($riwayatSession) + 1,
            'nama' => $this->nama,
            'sekolah' => 'Institut Bisnis dan Informatika Bogor',
            'tanggal' => now()->translatedFormat('l, d/m/Y'),
            'jam_masuk' => $this->tipePresensi === 'masuk' ? now()->format('H:i') . ' WIB' : '08:00 WIB',
            'jam_pulang' => $this->tipePresensi === 'pulang' ? now()->format('H:i') . ' WIB' : '-',
            'status' => 'HADIR',
            'logbook' => $this->tipePresensi === 'pulang' ? $this->logbook : 'Presensi Masuk',
            'foto' => $this->fotoCaptured,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];

        // Masukkan data baru ke urutan paling atas array
        array_unshift($riwayatSession, $dataBaru);

        // Simpan kembali ke Session
        session()->put('riwayat_presensi', $riwayatSession);

        $tipe = $this->tipePresensi;

        // Reset Form
        $this->reset(['logbook', 'fotoCaptured']);
        $this->tipePresensi = 'masuk';

        session()->flash('message', 'Presensi ' . strtoupper($tipe) . ' berhasil dikirim dan tersimpan!');
    }

    public function render()
    {
        return view('livewire.user.presensi')
            ->layout('layouts.user');
    }
}