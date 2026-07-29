<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\PermohonanIzin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IzinSakit extends Component
{
    public $nama = '';
    public $sekolah = '';

    // State Form
    public $tipePengajuan = 'izin'; // 'izin', 'sakit', atau 'absen'
    public $tanggalMulai = '';
    public $tanggalSelesai = '';
    public $alasan = '';

    // Aturan validasi
    protected function rules()
    {
        $rules = [
            'tipePengajuan' => 'required|in:izin,sakit,absen',
            'tanggalMulai'  => 'required|date',
            'alasan'        => 'required|min:10',
        ];

        // Jika BUKAN 'absen', wajibkan tanggalSelesai
        if ($this->tipePengajuan !== 'absen') {
            $rules['tanggalSelesai'] = 'required|date|after_or_equal:tanggalMulai';
        }

        return $rules;
    }

    protected $messages = [
        'tipePengajuan.required'        => 'Pilih tipe ketidakhadiran.',
        'tipePengajuan.in'              => 'Tipe ketidakhadiran tidak valid.',
        'tanggalMulai.required'         => 'Tanggal wajib diisi.',
        'tanggalSelesai.required'       => 'Tanggal selesai wajib diisi.',
        'tanggalSelesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
        'alasan.required'               => 'Alasan/keterangan wajib diisi.',
        'alasan.min'                    => 'Alasan minimal 10 karakter.',
    ];

    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            $this->nama = $user->name ?? $user->nama ?? 'Siswa';
            $this->sekolah = $user->sekolah ?? $user->instansi ?? '-';
        }

        $today = now()->format('Y-m-d');
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;
    }

    public function updatedTipePengajuan($value)
    {
        if ($value === 'absen') {
            $this->tanggalSelesai = $this->tanggalMulai;
        }
    }

    public function updatedTanggalMulai($value)
    {
        if ($this->tipePengajuan === 'absen') {
            $this->tanggalSelesai = $value;
        }
    }

    public function kirimPengajuan()
    {
        $this->validate();

        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'Anda harus login terlebih dahulu.');
            return;
        }

        // Tentukan tanggal akhir
        $tglAkhir = ($this->tipePengajuan === 'absen') 
            ? $this->tanggalMulai 
            : $this->tanggalSelesai;

        // SIMPAN KE DATABASE TABEL permohonan_izins
        PermohonanIzin::create([
            'user_id'            => $user->id ?? $user->user_id,
            'jenis'              => $this->tipePengajuan,
            'tanggal_awal'       => $this->tanggalMulai,
            'tanggal_akhir'      => $tglAkhir,
            'tanggal_permohonan' => now()->format('Y-m-d'),
            'alasan'             => $this->alasan,
            'status'             => 'pending',
        ]);

        // Reset Form Input
        $this->reset(['alasan']);
        $today = now()->format('Y-m-d');
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;

        session()->flash('message', 'Pengajuan ' . strtoupper($this->tipePengajuan) . ' berhasil dikirim ke admin!');
    }

    public function render()
    {
        // Ambil riwayat pengajuan user langsung dari Database
        $user = Auth::user();
        $riwayat = [];

        if ($user) {
            $userId = $user->id ?? $user->user_id;
            $riwayat = PermohonanIzin::where('user_id', $userId)
                ->latest()
                ->get();
        }

        return view('livewire.user.izin-sakit', [
            'riwayat' => $riwayat,
        ])->layout('layouts.user');
    }
}