<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PermohonanIzin;
use App\Enums\UserStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class IzinSakit extends Component
{
    use WithPagination;

    public $nama = '';
    public $sekolah = '';

    public bool $isLulus = false;

    public $tipePengajuan = 'izin';
    public $kategoriAbsen = 'keduanya';

    public $tanggalMulai = '';
    public $tanggalSelesai = '';
    public $alasan = '';
    public $alamatIzin = '';
    public $jumlahHari = 1;

    public bool $sudahAdaPengajuan = false;
    public bool $tanggalAkhirPekan = false; // flag: tanggal yang dipilih jatuh di weekend / rentang tanpa hari kerja

    /**
     * Ambil data user yang sedang login saat komponen pertama kali dimuat.
     */
    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            $this->nama    = $user->name ?? $user->nama ?? '';
            $this->sekolah = $user->sekolah->nama_sekolah ?? $user->sekolah ?? '';
            $this->isLulus = isset($user->status) && $user->status === UserStatus::LULUS->value;
        }

        $this->tanggalMulai = Carbon::today()->format('Y-m-d');
        $this->hitungJumlahHari();
        $this->cekTanggalBentrok();
    }

    public function updatedTipePengajuan($value)
    {
        if (in_array($value, ['absen', 'absen_pulang'])) {
            $this->tanggalSelesai = '';
        }

        if ($value !== 'izin') {
            $this->alamatIzin = '';
        }

        $this->hitungJumlahHari();
        $this->cekTanggalBentrok();
    }

    public function updatedTanggalMulai()
    {
        $this->hitungJumlahHari();
        $this->cekTanggalBentrok();
    }

    public function updatedTanggalSelesai()
    {
        $this->hitungJumlahHari();
    }

    /**
     * Hitung jumlah hari kerja (tidak termasuk Sabtu & Minggu).
     * - Untuk absen/absen_pulang: 1 hari jika tanggalMulai bukan weekend, 0 jika weekend.
     * - Untuk izin/sakit: hitung semua hari kerja (Senin-Jumat) dalam rentang tanggalMulai - tanggalSelesai.
     */
    public function hitungJumlahHari()
    {
        $isTipeAbsen = in_array($this->tipePengajuan, ['absen', 'absen_pulang']);

        if (empty($this->tanggalMulai)) {
            $this->jumlahHari = 0;
            $this->tanggalAkhirPekan = false;
            return;
        }

        try {
            $mulai = Carbon::parse($this->tanggalMulai);

            // Tipe Absen / Absen Pulang -> hanya 1 tanggal
            if ($isTipeAbsen) {
                $isWeekend = $mulai->isWeekend();
                $this->jumlahHari = $isWeekend ? 0 : 1;
                $this->tanggalAkhirPekan = $isWeekend;
                return;
            }

            // Tipe Izin / Sakit -> hitung rentang, exclude weekend
            if (empty($this->tanggalSelesai)) {
                $this->jumlahHari = 0;
                $this->tanggalAkhirPekan = false;
                return;
            }

            $selesai = Carbon::parse($this->tanggalSelesai);

            if ($selesai->lessThan($mulai)) {
                $this->jumlahHari = 0;
                $this->tanggalAkhirPekan = false;
                return;
            }

            $periode = CarbonPeriod::create($mulai, $selesai);

            $hariKerja = collect(iterator_to_array($periode))
                ->filter(fn (Carbon $tanggal) => !$tanggal->isWeekend())
                ->count();

            $this->jumlahHari = $hariKerja;
            $this->tanggalAkhirPekan = $hariKerja === 0; // seluruh rentang jatuh di weekend
        } catch (\Throwable $e) {
            $this->jumlahHari = 0;
            $this->tanggalAkhirPekan = false;
        }
    }

    /**
     * Cek apakah tanggal yang dipilih sudah punya pengajuan aktif (live-check).
     */
    public function cekTanggalBentrok()
    {
        $user = Auth::user();

        if (!$user || empty($this->tanggalMulai)) {
            $this->sudahAdaPengajuan = false;
            return;
        }

        $userId = $user->id ?? $user->user_id;

        $this->sudahAdaPengajuan = PermohonanIzin::where('user_id', $userId)
            ->where(function ($query) {
                $query->whereDate('tanggal_awal', $this->tanggalMulai)
                      ->orWhere(function ($q) {
                          $q->whereDate('tanggal_awal', '<=', $this->tanggalMulai)
                            ->whereNotNull('tanggal_akhir')
                            ->whereDate('tanggal_akhir', '>=', $this->tanggalMulai);
                      });
            })
            ->whereNotIn('status', ['ditolak', 'rejected'])
            ->exists();
    }

    /**
     * Validasi dinamis berdasarkan tipe pengajuan.
     */
    protected function rules(): array
    {
        $isTipeAbsen = in_array($this->tipePengajuan, ['absen', 'absen_pulang']);

        $rules = [
            'tipePengajuan' => 'required|in:izin,sakit,absen,absen_pulang',
            'tanggalMulai'  => 'required|date',
            'alasan'        => 'required|string|min:5|max:500',
        ];

        if (!$isTipeAbsen) {
            $rules['tanggalSelesai'] = 'required|date|after_or_equal:tanggalMulai';
        }

        if ($this->tipePengajuan === 'izin') {
            $rules['alamatIzin'] = 'required|string|max:255';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'tanggalMulai.required'         => 'Tanggal wajib diisi.',
            'tanggalSelesai.required'       => 'Tanggal selesai wajib diisi.',
            'tanggalSelesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'alasan.required'               => 'Alasan wajib diisi.',
            'alasan.min'                    => 'Alasan minimal 5 karakter.',
            'alamatIzin.required'           => 'Alamat selama izin wajib diisi.',
        ];
    }

    public function kirimPengajuan()
    {
        $this->validate();

        $user = Auth::user();

        if (!$user) {
            session()->flash('warning', 'Sesi login tidak ditemukan, silakan login ulang.');
            return;
        }

        $userId = $user->id ?? $user->user_id;
        $isTipeAbsen = in_array($this->tipePengajuan, ['absen', 'absen_pulang']);

        $this->hitungJumlahHari();

        // ✅ Cegah kirim jika jumlah hari kerja = 0 (misal tanggal yang dipilih Sabtu/Minggu)
        if ($this->jumlahHari <= 0) {
            session()->flash('warning', 'Tidak bisa mengirim pengajuan karena tanggal yang dipilih jatuh pada akhir pekan (Sabtu/Minggu) atau tidak ada hari kerja dalam rentang tersebut.');
            return;
        }

        // ✅ Cek apakah user sudah punya pengajuan aktif di tanggal yang sama
        $sudahAda = PermohonanIzin::where('user_id', $userId)
            ->where(function ($query) {
                $query->whereDate('tanggal_awal', $this->tanggalMulai)
                      ->orWhere(function ($q) {
                          $q->whereDate('tanggal_awal', '<=', $this->tanggalMulai)
                            ->whereNotNull('tanggal_akhir')
                            ->whereDate('tanggal_akhir', '>=', $this->tanggalMulai);
                      });
            })
            ->whereNotIn('status', ['ditolak', 'rejected'])
            ->exists();

        if ($sudahAda) {
            session()->flash('warning', 'Kamu sudah mengajukan permohonan untuk tanggal tersebut. Tidak bisa mengirim pengajuan lagi di hari yang sama.');
            $this->sudahAdaPengajuan = true;
            return;
        }

        PermohonanIzin::create([
            'user_id'            => $userId,
            'jenis'              => $this->tipePengajuan === 'absen_pulang' ? 'absen pulang' : $this->tipePengajuan,
            'tanggal_permohonan' => Carbon::now(),
            'tanggal_awal'       => $this->tanggalMulai,
            'tanggal_akhir'      => $isTipeAbsen ? null : $this->tanggalSelesai,
            'jumlah_hari'        => $this->jumlahHari,
            'alasan'             => $this->alasan,
            'alamat_izin'        => $this->tipePengajuan === 'izin' ? $this->alamatIzin : null,
            'status'             => 'pending',
        ]);

        $labelPesan = match ($this->tipePengajuan) {
            'izin'         => 'Izin',
            'sakit'        => 'Sakit',
            'absen'        => 'Absen',
            'absen_pulang' => 'Absen Pulang',
            default        => ucfirst($this->tipePengajuan),
        };

        // Reset form setelah berhasil kirim
        $this->reset(['tanggalSelesai', 'alasan', 'alamatIzin']);
        $this->tanggalMulai = Carbon::today()->format('Y-m-d');
        $this->hitungJumlahHari();
        $this->cekTanggalBentrok();

        $this->resetPage();

        session()->flash('message', "Pengajuan {$labelPesan} berhasil dikirim ke admin!");
    }

    public function render()
    {
        $user = Auth::user();
        $riwayat = collect();

        if ($user) {
            $userId = $user->id ?? $user->user_id;
            $riwayat = PermohonanIzin::where('user_id', $userId)
                ->latest()
                ->paginate(10);
        }

        return view('livewire.user.izin-sakit', [
            'riwayat' => $riwayat,
        ])->layout('layouts.user');
    }
}