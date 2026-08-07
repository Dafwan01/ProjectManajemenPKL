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

    // Tipe Pengajuan: 'izin', 'sakit', atau 'absen'
    public $tipePengajuan = 'izin';

    // Opsi Tambahan untuk Tipe Absen: 'masuk', 'pulang', atau 'keduanya'
    public $kategoriAbsen = 'keduanya';

    public $tanggalMulai = '';
    public $tanggalSelesai = '';
    public $alasan = '';
    public $alamatIzin = '';
    public $jumlahHari = 1;

    protected function rules()
    {
        $rules = [
            'tipePengajuan' => 'required|in:izin,sakit,absen',
            'tanggalMulai'  => 'required|date',
            'alasan'        => 'required|min:10',
        ];

        if ($this->tipePengajuan === 'absen') {
            $rules['kategoriAbsen'] = 'required|in:masuk,pulang,keduanya';
        } else {
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
        'kategoriAbsen.required'        => 'Pilih kategori pengajuan absen.',
        'kategoriAbsen.in'              => 'Kategori pengajuan absen tidak valid.',
        'tanggalMulai.required'         => 'Tanggal wajib diisi.',
        'tanggalSelesai.required'       => 'Tanggal selesai wajib diisi.',
        'tanggalSelesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
        'alasan.required'               => 'Alasan atau keterangan wajib diisi.',
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
            session()->flash('warning', 'Status akun Anda adalah LULUS. Anda tidak dapat membuat pengajuan izin/sakit/absen lagi.');
        }
    }

    public function hitungJumlahHari()
    {
        if ($this->tipePengajuan === 'absen') {
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
        if ($value === 'absen') {
            $this->tanggalSelesai = $this->tanggalMulai;
        }
        if ($value !== 'izin') {
            $this->alamatIzin = '';
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
            session()->flash('warning', 'Pengajuan gagal! Akun Anda telah berstatus LULUS.');
            return;
        }

        $this->validate();

        if ($this->jumlahHari <= 0) {
            session()->flash('warning', 'Tanggal yang dipilih jatuh pada akhir pekan (Sabtu/Minggu) dan tidak dapat diajukan.');
            return;
        }

        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'Anda harus masuk terlebih dahulu.');
            return;
        }

        $isTipeAbsen = $this->tipePengajuan === 'absen';
        $tglAkhir = $isTipeAbsen ? $this->tanggalMulai : $this->tanggalSelesai;

        // Tentukan penanda absen masuk & absen pulang jika tipe pengajuan adalah ABSEN
        $absenMasuk = false;
        $absenPulang = false;

        if ($isTipeAbsen) {
            if (in_array($this->kategoriAbsen, ['masuk', 'keduanya'])) {
                $absenMasuk = true;
            }
            if (in_array($this->kategoriAbsen, ['pulang', 'keduanya'])) {
                $absenPulang = true;
            }
        }

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
            'absen_masuk'        => $absenMasuk,
            'absen_pulang'       => $absenPulang,
        ]);

        // Buat label pesan pengajuan
        $labelPesan = match($this->tipePengajuan) {
            'absen' => match($this->kategoriAbsen) {
                'masuk'   => 'Absen Masuk',
                'pulang'  => 'Absen Pulang',
                default   => 'Absen (Masuk & Pulang)',
            },
            default => ucfirst($this->tipePengajuan),
        };

        // Reset Isian Form
        $this->reset(['alasan', 'alamatIzin']);
        $today = now()->format('Y-m-d');
        $this->tanggalMulai = $today;
        $this->tanggalSelesai = $today;
        $this->kategoriAbsen = 'keduanya';
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
