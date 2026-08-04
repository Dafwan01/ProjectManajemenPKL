<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\PermohonanIzin;
use App\Enums\UserStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use App\Traits\Toastable;

class IzinSakit extends Component
{
    use Toastable;

    public $nama = '';
    public $sekolah = '';

    // Flag status kelulusan user
    public bool $isLulus = false;

    // State Form
    public $tipePengajuan = 'izin'; // 'izin', 'sakit', atau 'absen'
    public $tanggalMulai = '';
    public $tanggalSelesai = '';
    public $alasan = '';
    public $alamatIzin = ''; // Property baru untuk alamat selama izin
    public $jumlahHari = 1;  // Property baru untuk kalkulasi jumlah hari

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

        // Jika tipe pengajuan 'izin', wajibkan alamatIzin
        if ($this->tipePengajuan === 'izin') {
            $rules['alamatIzin'] = 'required|min:5';
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
        'alamatIzin.required'           => 'Alamat lokasi selama izin wajib diisi.',
        'alamatIzin.min'                => 'Alamat minimal 5 karakter.',
    ];

    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            $this->nama = $user->name ?? $user->nama ?? 'Siswa';
            $this->sekolah = $user->sekolah ?? $user->instansi ?? '-';
        }

        $this->cekUserStatus();

        $today = now()->format('Y-m-d');
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;
        $this->hitungJumlahHari();
    }

    private function cekUserStatus()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $userStatus = $user->status instanceof \UnitEnum ? $user->status->value : $user->status;

        if (strtolower((string) $userStatus) === 'lulus' || $userStatus === UserStatus::LULUS->value) {
            $this->isLulus = true;
            $this->toastWarning( 'Status akun Anda adalah LULUS. Anda tidak dapat membuat pengajuan izin/sakit lagi.');
        }
    }

    /**
     * Hitung durasi hari secara otomatis
     */
    public function hitungJumlahHari()
    {
        if ($this->tipePengajuan === 'absen') {
            $date = Carbon::parse($this->tanggalMulai);
            $this->jumlahHari = $date->isWeekday() ? 1 : 0;
            return;
        }

        if ($this->tanggalMulai && $this->tanggalSelesai) {
            try {
                $start = Carbon::parse($this->tanggalMulai);
                $end = Carbon::parse($this->tanggalSelesai);

                if ($end->greaterThanOrEqualTo($start)) {
                    $this->jumlahHari = $this->countWeekdaysBetween($start, $end);
                } else {
                    $this->jumlahHari = 0;
                }
            } catch (\Exception $e) {
                $this->jumlahHari = 0;
            }
        }
    }

    private function countWeekdaysBetween(Carbon $start, Carbon $end): int
    {
        $count = 0;
        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($date->isWeekday()) {
                $count++;
            }
        }
        return $count;
    }

    public function updatedTipePengajuan($value)
    {
        if ($value === 'absen') {
            $this->tanggalSelesai = $this->tanggalMulai;
        }
        if ($value !== 'izin') {
            $this->alamatIzin = ''; // Reset alamat jika bukan izin
        }
        $this->hitungJumlahHari();
    }

    public function updatedTanggalMulai($value)
    {
        if ($this->tipePengajuan === 'absen') {
            $this->tanggalSelesai = $value;
        }
        $this->hitungJumlahHari();
    }

    public function updatedTanggalSelesai()
    {
        $this->hitungJumlahHari();
    }

    public function kirimPengajuan()
    {
        if ($this->isLulus) {
            $this->toastWarning( 'Gagal Pengajuan! Akun Anda telah berstatus LULUS.');
            return;
        }

        $this->validate();

        $user = Auth::user();

        if (!$user) {
            $this->toastError( 'Anda harus login terlebih dahulu.');
            return;
        }

        $tglAkhir = ($this->tipePengajuan === 'absen') 
            ? $this->tanggalMulai 
            : $this->tanggalSelesai;

        // Simpan Ke Database
        PermohonanIzin::create([
            'user_id'            => $user->id ?? $user->user_id,
            'jenis'              => $this->tipePengajuan,
            'tanggal_awal'       => $this->tanggalMulai,
            'tanggal_akhir'      => $tglAkhir,
            'jumlah_hari'        => $this->jumlahHari,
            'alamat_izin'        => $this->tipePengajuan === 'izin' ? $this->alamatIzin : null,
            'tanggal_permohonan' => now()->format('Y-m-d'),
            'alasan'             => $this->alasan,
            'status'             => 'pending',
        ]);

        // Reset Form Input
        $this->reset(['alasan', 'alamatIzin']);
        $today = now()->format('Y-m-d');
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;
        $this->hitungJumlahHari();

        $this->toastSuccess( 'Pengajuan ' . strtoupper($this->tipePengajuan) . ' berhasil dikirim ke admin!');
    }

    public function render()
    {
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