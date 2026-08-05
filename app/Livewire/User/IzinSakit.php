<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\PermohonanIzin;
use App\Enums\UserStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IzinSakit extends Component
{
    public $nama = '';
    public $sekolah = '';

    public bool $isLulus = false;

    // State Form: 'izin', 'sakit', 'absen', atau 'absen_pulang'
    public $tipePengajuan = 'izin';
    public $tanggalMulai = '';
    public $tanggalSelesai = '';
    public $alasan = '';
    public $alamatIzin = '';
    public $jumlahHari = 1;

    protected function rules()
    {
        $rules = [
            'tipePengajuan' => 'required|in:izin,sakit,absen,absen_pulang',
            'tanggalMulai'  => 'required|date',
            'alasan'        => 'required|min:10',
        ];

        if (!in_array($this->tipePengajuan, ['absen', 'absen_pulang'])) {
            $rules['tanggalSelesai'] = 'required|date|after_or_equal:tanggalMulai';
        }

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
            session()->flash('warning', 'Status akun Anda adalah LULUS. Anda tidak dapat membuat pengajuan izin/sakit lagi.');
        }
    }

    public function hitungJumlahHari()
    {
        if (in_array($this->tipePengajuan, ['absen', 'absen_pulang'])) {
            if ($this->tanggalMulai) {
                $tanggal = Carbon::parse($this->tanggalMulai);
                $this->jumlahHari = $tanggal->isWeekend() ? 0 : 1;
            } else {
                $this->jumlahHari = 1;
            }
            return;
        }

        if ($this->tanggalMulai && $this->tanggalSelesai) {
            try {
                $start = Carbon::parse($this->tanggalMulai);
                $end = Carbon::parse($this->tanggalSelesai);

                if ($end->greaterThanOrEqualTo($start)) {
                    $hariKerja = 0;
                    $cursor = $start->copy();

                    while ($cursor->lte($end)) {
                        if (! $cursor->isWeekend()) {
                            $hariKerja++;
                        }
                        $cursor->addDay();
                    }

                    $this->jumlahHari = $hariKerja;
                } else {
                    $this->jumlahHari = 0;
                }
            } catch (\Exception $e) {
                $this->jumlahHari = 1;
            }
        }
    }

    public function updatedTipePengajuan($value)
    {
        if (in_array($value, ['absen', 'absen_pulang'])) {
            $this->tanggalSelesai = $this->tanggalMulai;
        }
        if ($value !== 'izin') {
            $this->alamatIzin = '';
        }
        $this->hitungJumlahHari();
    }

    public function updatedTanggalMulai($value)
    {
        if (in_array($this->tipePengajuan, ['absen', 'absen_pulang'])) {
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
            session()->flash('warning', 'Gagal Pengajuan! Akun Anda telah berstatus LULUS.');
            return;
        }

        $this->validate();

        if ($this->jumlahHari <= 0) {
            session()->flash('warning', 'Tanggal yang dipilih jatuh pada akhir pekan (Sabtu/Minggu) dan tidak dapat diajukan.');
            return;
        }

        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'Anda harus login terlebih dahulu.');
            return;
        }

        $isTipeAbsen = in_array($this->tipePengajuan, ['absen', 'absen_pulang']);
        $tglAkhir = $isTipeAbsen ? $this->tanggalMulai : $this->tanggalSelesai;

        // Mapping value form -> value enum database
        // 'absen_pulang' (form) disimpan sebagai 'absen pulang' (enum, ada spasi)
        $jenisTersimpan = $this->tipePengajuan === 'absen_pulang' ? 'absen pulang' : $this->tipePengajuan;

        PermohonanIzin::create([
            'user_id'            => $user->id ?? $user->user_id,
            'jenis'              => $jenisTersimpan,
            'tanggal_awal'       => $this->tanggalMulai,
            'tanggal_akhir'      => $tglAkhir,
            'jumlah_hari'        => $this->jumlahHari,
            'alamat_izin'        => $this->tipePengajuan === 'izin' ? $this->alamatIzin : null,
            'tanggal_permohonan' => now()->format('Y-m-d'),
            'alasan'             => $this->alasan,
            'status'             => 'pending',
        ]);

        $labelPesan = match($this->tipePengajuan) {
            'absen'        => 'Absen',
            'absen_pulang' => 'Absen Pulang',
            default        => ucfirst($this->tipePengajuan),
        };

        // Reset Form Input
        $this->reset(['alasan', 'alamatIzin']);
        $today = now()->format('Y-m-d');
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;
        $this->hitungJumlahHari();

        session()->flash('message', "Pengajuan {$labelPesan} berhasil dikirim ke admin!");
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