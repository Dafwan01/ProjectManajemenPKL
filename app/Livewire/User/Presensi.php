<?php

namespace App\Livewire\User;

use Livewire\Component;

class Presensi extends Component
{
    public $nama = 'Jonathan';
    public $jabatan = 'PKL';
    public $opd = 'Dinas Komunikasi dan Informatika';
    public $bidang = 'Magang Aplikasi Informatika';
    
    public $status = 'Hadir';
    public $logbook = '';
    public $fotoCaptured = null; // Menampung base64 foto baru
    public $selectedFoto = null;  // Menampung foto yang dipilih untuk preview modal

    public $riwayat = [
        [
            'nama' => 'Jonathan',
            'sekolah' => 'Institut Bisnis dan Informatika Bogor',
            'tanggal' => 'Jumat, 24/07/2026',
            'status' => 'HADIR',
            'logbook' => 'Pengembangan antarmuka komponen UI Presensi dengan Livewire & Tailwind CSS.',
            'foto' => null // Placeholder foto default/sampel
        ]
    ];

    protected $messages = [
        'logbook.required' => 'Logbook harian wajib diisi sebelum mengirim presensi!',
    ];

    public function simpanPresensi()
    {
        $this->validate([
            'logbook' => 'required|min:5',
        ]);

        // Tambahkan presensi baru ke riwayat beserta fotonya
        array_unshift($this->riwayat, [
            'nama' => $this->nama,
            'sekolah' => 'Institut Bisnis dan Informatika Bogor',
            'tanggal' => date('l, d/m/Y'),
            'status' => strtoupper($this->status),
            'logbook' => $this->logbook,
            'foto' => $this->fotoCaptured
        ]);

        // Reset semua field form ke posisi Standby
        $this->logbook = '';
        $this->status = 'Hadir';
        $this->fotoCaptured = null;

        session()->flash('message', 'Presensi berhasil dikirim!');
    }

    // Method untuk memilih foto mana yang ditampilkan di modal
    public function lihatFoto($foto)
    {
        $this->selectedFoto = $foto;
    }

    public function render()
    {
        return view('livewire.user.presensi')
            ->layout('layouts.user');
    }
}