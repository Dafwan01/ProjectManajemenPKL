<?php

namespace App\Livewire\User;

use Livewire\Component;

class IzinSakit extends Component
{
    public $nama = 'Jonathan';
    public $sekolah = 'IBI Kesatuan';

    // State Form
    public $tipePengajuan = 'izin'; // default 'izin' atau 'sakit'
    public $tanggalMulai = '';
    public $tanggalSelesai = '';
    public $alasan = '';

    protected $rules = [
        'tipePengajuan'  => 'required|in:izin,sakit',
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

        // Data baru pengajuan
        $dataBaru = [
            'id'         => count($riwayatSession) + 1,
            'nama'       => $this->nama,
            'sekolah'    => $this->sekolah,
            'tanggal'    => $rangeTanggal,
            'jam_masuk'  => '-',
            'jam_pulang' => '-',
            'status'     => strtoupper($this->tipePengajuan), // IZIN atau SAKIT
            'logbook'    => '[' . strtoupper($this->tipePengajuan) . '] ' . $this->alasan,
            'latitude'   => null,
            'longitude'  => null,
        ];

        // Masukkan ke urutan paling atas
        array_unshift($riwayatSession, $dataBaru);
        session()->put('riwayat_presensi', $riwayatSession);

        // Reset input form
        $this->reset(['alasan']);
        $this->tanggalMulai = now()->format('Y-m-d');
        $this->tanggalSelesai = now()->format('Y-m-d');

        session()->flash('message', 'Pengajuan ' . strtoupper($this->tipePengajuan) . ' berhasil dikirim!');
    }

    public function render()
    {
        return view('livewire.user.izin-sakit')
            ->layout('layouts.user');
    }
}