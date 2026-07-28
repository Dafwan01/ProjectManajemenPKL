<?php

namespace App\Livewire\User;

use Livewire\Component;

class IzinSakit extends Component
{
    public $nama = 'Jonathan';
    public $sekolah = 'IBI Kesatuan';

    // State Form
    public $tipePengajuan = 'izin'; // default 'izin', 'sakit', atau 'absen'
    public $tanggalMulai = '';
    public $tanggalSelesai = '';
    public $alasan = '';
    public $riwayat = [];

    protected $rules = [
        'tipePengajuan'  => 'required|in:izin,sakit,absen',
        'tanggalMulai'   => 'required|date',
        'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
        'alasan'         => 'required|min:10',
    ];

    protected $messages = [
        'tipePengajuan.required'        => 'Pilih tipe ketidakhadiran.',
        'tipePengajuan.in'              => 'Tipe ketidakhadiran tidak valid.',
        'tanggalMulai.required'         => 'Tanggal mulai wajib diisi.',
        'tanggalSelesai.required'       => 'Tanggal selesai wajib diisi.',
        'tanggalSelesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
        'alasan.required'               => 'Alasan/keterangan wajib diisi.',
        'alasan.min'                    => 'Alasan minimal 10 karakter.',
    ];

    public function mount()
    {
        $this->tanggalMulai = now()->format('Y-m-d');
        $this->tanggalSelesai = now()->format('Y-m-d');
        $this->riwayat = session()->get('riwayat_presensi', []);
    }

    public function kirimPengajuan()
    {
        $this->validate();

        // Ambil data riwayat di Session
        $riwayatSession = session()->get('riwayat_presensi', []);

        // Format tanggal tampilan
        $tglMulaiFormatted = \Carbon\Carbon::parse($this->tanggalMulai)->translatedFormat('d/m/Y');
        $tglSelesaiFormatted = \Carbon\Carbon::parse($this->tanggalSelesai)->translatedFormat('d/m/Y');
        
        $rangeTanggal = ($tglMulaiFormatted === $tglSelesaiFormatted) 
            ? \Carbon\Carbon::parse($this->tanggalMulai)->translatedFormat('l, d/m/Y')
            : $tglMulaiFormatted . ' - ' . $tglSelesaiFormatted;

        $statusLabel = $this->tipePengajuan === 'absen'
            ? 'ABSEN'
            : strtoupper($this->tipePengajuan);

        $dataBaru = [
            'id'               => count($riwayatSession) + 1,
            'nama'             => $this->nama,
            'sekolah'          => $this->sekolah,
            'tanggal'          => $rangeTanggal,
            'jam_masuk'        => '-',
            'jam_pulang'       => '-',
            'status'           => $statusLabel,
            'status_pengajuan' => 'pending',
            'logbook'          => '[' . $statusLabel . '] ' . $this->alasan,
            'latitude'         => null,
            'longitude'        => null,
        ];

        // Masukkan ke urutan paling atas
        array_unshift($riwayatSession, $dataBaru);
        session()->put('riwayat_presensi', $riwayatSession);
        $this->riwayat = $riwayatSession;

        // Reset input form
        $this->reset(['alasan']);
        $this->tanggalMulai = now()->format('Y-m-d');
        $this->tanggalSelesai = now()->format('Y-m-d');

        session()->flash('message', 'Pengajuan ' . ($this->tipePengajuan === 'absen' ? 'ABSEN' : strtoupper($this->tipePengajuan)) . ' berhasil dikirim!');
    }

    public function render()
    {
        return view('livewire.user.izin-sakit', [
            'riwayat' => $this->riwayat,
        ])
            ->layout('layouts.user');
    }
}