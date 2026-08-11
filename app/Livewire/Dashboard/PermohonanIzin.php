<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\DetailJadwal;
use App\Models\log_book;
use App\Models\PermohonanIzin as PermohonanIzinModel;
use App\Models\presensi;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class PermohonanIzin extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $tanggal = '';

    public bool $showDetailModal = false;
    public $selectedId = null;
    public string $catatanAdmin = '';

    // State untuk modal konfirmasi "presensi sudah ada"
    public bool $showConfirmTimpaModal = false;
    public $pendingSetujuiId = null;
    public array $tanggalBentrok = [];

    public function boot(): void
    {
        Carbon::setLocale('id');
    }

    public function mount(): void
    {
        $this->tanggal = '';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingTanggal(): void
    {
        $this->resetPage();
    }

    public function resetFilterTanggal(): void
    {
        $this->tanggal = '';
    }

    public function openDetail($id): void
    {
        $this->selectedId = $id;
        $permohonan = PermohonanIzinModel::findOrFail($id);
        $this->catatanAdmin = $permohonan->catatan_admin ?? '';
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedId = null;
        $this->catatanAdmin = '';
    }

    /**
     * Entry point saat admin klik "Setujui".
     * - absen        -> proses langsung
     * - absen pulang  -> cek absen masuk dulu, tolak otomatis jika tidak ada
     * - izin/sakit    -> cek bentrok presensi dulu, munculkan modal konfirmasi jika bentrok
     */
    public function setujui($id): void
    {
        $permohonan = PermohonanIzinModel::with('user')->findOrFail($id);
        $jenisStr = strtolower($permohonan->jenis);

        if ($jenisStr === 'absen' || $jenisStr === 'absen pulang') {
            $this->approveAndProcess($permohonan, fn () => $this->prosesAbsen($permohonan));
            return;
        }

        // Tipe: izin / sakit
        $bentrok = $this->cekPresensiBentrok($permohonan);

        if (!empty($bentrok)) {
            $this->pendingSetujuiId = $id;
            $this->tanggalBentrok = $bentrok;
            $this->showConfirmTimpaModal = true;
            return; // Tunggu konfirmasi user (Ya/Tidak) sebelum lanjut
        }

        $this->approveAndProcess($permohonan, fn () => $this->prosesIzinSakit($permohonan, $jenisStr));
    }

    /**
     * Helper umum: update status jadi disetujui, jalankan proses, flash message, tutup modal.
     */
    private function approveAndProcess($permohonan, callable $proses): void
    {
        $permohonan->update([
            'status' => 'disetujui',
            'catatan_admin' => $this->catatanAdmin,
        ]);

        $proses();

        $namaUser = $permohonan->user->nama ?? $permohonan->user->name ?? 'Pengguna';
        session()->flash('message', 'Permohonan ' . strtoupper($permohonan->jenis) . ' dari ' . $namaUser . ' telah disetujui.');

        $this->closeDetail();
    }

    /**
     * Cek apakah tanggal (rentang) permohonan izin/sakit sudah punya data presensi.
     * Mengembalikan array detail tanggal yang bentrok (kosong jika tidak ada bentrok).
     */
   private function cekPresensiBentrok($permohonan): array
{
    $startDate = $permohonan->tanggal_awal ? Carbon::parse($permohonan->tanggal_awal) : Carbon::parse($permohonan->tanggal_permohonan);
    $endDate   = $permohonan->tanggal_akhir ? Carbon::parse($permohonan->tanggal_akhir) : $startDate;

    $period = CarbonPeriod::create($startDate, $endDate);
    $bentrok = [];

    foreach ($period as $date) {
        $tglString = $date->format('Y-m-d');

        $presensi = presensi::where('user_id', $permohonan->user_id)
            ->whereDate('tanggal', $tglString)
            ->first();

        if ($presensi) {
            // ✅ Ambil value dari enum (kalau enum), atau pakai apa adanya kalau sudah string
            $statusValue = $presensi->status_kehadiran instanceof \BackedEnum
                ? $presensi->status_kehadiran->value
                : $presensi->status_kehadiran;

            $bentrok[] = [
                'tanggal'         => $tglString,
                'tanggal_format'  => $date->copy()->translatedFormat('l, d F Y'),
                'status_sekarang' => strtoupper((string) ($statusValue ?? '-')),
            ];
        }
    }

    return $bentrok;
}

    /**
     * Dipanggil saat admin klik "Ya, Timpa Presensi" di modal konfirmasi.
     */
    public function konfirmasiTimpaPresensi(): void
    {
        if (!$this->pendingSetujuiId) {
            $this->batalTimpaPresensi();
            return;
        }

        $permohonan = PermohonanIzinModel::with('user')->findOrFail($this->pendingSetujuiId);
        $jenisStr = strtolower($permohonan->jenis);

        $this->approveAndProcess($permohonan, fn () => $this->prosesIzinSakit($permohonan, $jenisStr));

        $this->resetConfirmTimpaState();
    }

    /**
     * Dipanggil saat admin klik "Tidak" di modal konfirmasi -> batalkan semua, kembali ke list.
     */
    public function batalTimpaPresensi(): void
    {
        $this->resetConfirmTimpaState();
        $this->closeDetail();
    }

    private function resetConfirmTimpaState(): void
    {
        $this->showConfirmTimpaModal = false;
        $this->pendingSetujuiId = null;
        $this->tanggalBentrok = [];
    }

    private function prosesIzinSakit($permohonan, string $statusKehadiran): void
{
    $startDate = $permohonan->tanggal_awal ? Carbon::parse($permohonan->tanggal_awal) : Carbon::parse($permohonan->tanggal_permohonan);
    $endDate   = $permohonan->tanggal_akhir ? Carbon::parse($permohonan->tanggal_akhir) : $startDate;

    $period = CarbonPeriod::create($startDate, $endDate);

    foreach ($period as $date) {
        $tglString = $date->format('Y-m-d');

        $presensi = presensi::where('user_id', $permohonan->user_id)
            ->whereDate('tanggal', $tglString)
            ->first();

        if ($presensi) {
            // ✅ Hapus file foto lama (jika ada) sebelum kolomnya dikosongkan
            $this->hapusFotoPresensi($presensi);

            $presensi->update([
                'status_kehadiran' => $statusKehadiran,
                'absen_masuk'      => null,
                'absen_keluar'     => null,
                'foto_masuk'       => null,
                'foto_keluar'      => null,
            ]);
        } else {
            $presensiNew = presensi::create([
                'user_id'          => $permohonan->user_id,
                'tanggal'          => $tglString,
                'status_kehadiran' => $statusKehadiran,
                'absen_masuk'      => null,
                'absen_keluar'     => null,
                'foto_masuk'       => null,
                'foto_keluar'      => null,
            ]);

            log_book::create([
                'user_id'     => $permohonan->user_id,
                'presensi_id' => $presensiNew->presensi_id ?? $presensiNew->id,
                'kegiatan'    => '(' . strtoupper($permohonan->jenis) . ') ' . $permohonan->alasan,
            ]);
        }
    }
}

private function hapusFotoPresensi($presensi): void
{
    foreach (['foto_masuk', 'foto_keluar'] as $kolom) {
        $path = $presensi->{$kolom};

        if (!empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

    private function prosesAbsen($permohonan): void
    {
        $tglString = $permohonan->tanggal_awal
            ? Carbon::parse($permohonan->tanggal_awal)->format('Y-m-d')
            : Carbon::parse($permohonan->tanggal_permohonan)->format('Y-m-d');

        $tanggalObj = Carbon::parse($tglString);
        $namaHari = $tanggalObj->copy()->locale('id')->translatedFormat('l');

        $detailJadwal = DetailJadwal::with('jadwal')
            ->where('user_id', $permohonan->user_id)
            ->whereRaw('LOWER(hari) = ?', [strtolower($namaHari)])
            ->first();

        if (!$detailJadwal || !$detailJadwal->jadwal) {
            session()->flash('warning', 'Tanggal ' . $tanggalObj->translatedFormat('d F Y') . ' tidak memiliki jadwal magang untuk peserta ini, sehingga jam absen tidak dapat diisi otomatis.');
            return;
        }

        $jadwal = $detailJadwal->jadwal;

        $presensi = presensi::where('user_id', $permohonan->user_id)
            ->whereDate('tanggal', $tglString)
            ->first();

        if (!$presensi) {
            $presensi = presensi::create([
                'user_id' => $permohonan->user_id,
                'tanggal' => $tglString,
            ]);
        }

        $dataUpdate = [];
        $keterangan = [];

        if (empty($presensi->absen_masuk)) {
            $dataUpdate['absen_masuk'] = $jadwal->jam_masuk;
            $keterangan[] = 'Masuk';
        }

        if (empty($presensi->absen_keluar)) {
            $dataUpdate['absen_keluar'] = $jadwal->jam_keluar;
            $keterangan[] = 'Pulang';
        }

        if (!empty($dataUpdate)) {
            $dataUpdate['status_kehadiran'] = 'hadir';
            $presensi->update($dataUpdate);

            log_book::updateOrCreate(
                [
                    'presensi_id' => $presensi->presensi_id ?? $presensi->id,
                    'user_id'     => $permohonan->user_id,
                ],
                [
                    'kegiatan' => '(ABSEN SUSULAN - ' . implode(' & ', $keterangan) . ') ' . $permohonan->alasan,
                ]
            );
        } else {
            session()->flash('warning', 'Tanggal ' . $tanggalObj->translatedFormat('d F Y') . ' sudah memiliki data absen masuk & pulang, sehingga tidak ada data yang diperbarui.');
        }
    }

    /**
     * ✅ Pengecekan baru: jika tanggal belum punya data absen_masuk,
     * permohonan otomatis DITOLAK (bukan disetujui) + flash alert.
     */
    private function handleAbsenPulangApproval($permohonan): void
    {
        $tglString = $permohonan->tanggal_awal
            ? Carbon::parse($permohonan->tanggal_awal)->format('Y-m-d')
            : Carbon::parse($permohonan->tanggal_permohonan)->format('Y-m-d');

        $tanggalObj = Carbon::parse($tglString);
        $namaUser = $permohonan->user->nama ?? $permohonan->user->name ?? 'Pengguna';

        $presensi = presensi::where('user_id', $permohonan->user_id)
            ->whereDate('tanggal', $tglString)
            ->first();

        // Jika absen_masuk belum ada, cek dulu apakah user juga punya pengajuan
        // "Absen" (masuk) yang masih pending untuk tanggal yang sama -> proses &
        // setujui dulu pengajuan itu secara otomatis, supaya presensi.absen_masuk
        // terisi sebelum kita menolak pengajuan Absen Pulang ini.
        if (!$presensi || empty($presensi->absen_masuk)) {
            $pengajuanAbsenMasuk = PermohonanIzinModel::where('user_id', $permohonan->user_id)
                ->whereDate('tanggal_awal', $tglString)
                ->where('jenis', 'absen')
                ->where('status', 'pending')
                ->first();

            if ($pengajuanAbsenMasuk) {
                $pengajuanAbsenMasuk->update([
                    'status'        => 'disetujui',
                    'catatan_admin' => 'Disetujui otomatis bersamaan dengan persetujuan pengajuan Absen Pulang.',
                ]);

                $this->prosesAbsen($pengajuanAbsenMasuk);

                // Ambil ulang data presensi setelah pengajuan absen masuk diproses
                $presensi = presensi::where('user_id', $permohonan->user_id)
                    ->whereDate('tanggal', $tglString)
                    ->first();
            }
        }

        // ❌ Tetap tidak ada presensi/absen_masuk setelah dicoba cascade di atas -> tolak otomatis
        if (!$presensi || empty($presensi->absen_masuk)) {
            $permohonan->update([
                'status'        => 'ditolak',
                'catatan_admin' => $this->catatanAdmin ?: 'Ditolak otomatis: tidak ditemukan data absen masuk pada tanggal ' . $tanggalObj->translatedFormat('d F Y') . '.',
            ]);

            session()->flash('warning', 'Permohonan ABSEN PULANG dari ' . $namaUser . ' otomatis DITOLAK karena belum ada data absen masuk pada tanggal ' . $tanggalObj->translatedFormat('d F Y') . '.');

            $this->closeDetail();
            return;
        }

        // ✅ Ada absen masuk -> lanjut proses normal
        $permohonan->update([
            'status'        => 'disetujui',
            'catatan_admin' => $this->catatanAdmin,
        ]);

        $this->prosesAbsenPulang($permohonan);

        session()->flash('message', 'Permohonan ABSEN PULANG dari ' . $namaUser . ' telah disetujui.');

        $this->closeDetail();
    }

    private function prosesAbsenPulang($permohonan): void
    {
        $tglString = $permohonan->tanggal_awal
            ? Carbon::parse($permohonan->tanggal_awal)->format('Y-m-d')
            : Carbon::parse($permohonan->tanggal_permohonan)->format('Y-m-d');

        $tanggalObj = Carbon::parse($tglString);
        $namaHari = $tanggalObj->copy()->locale('id')->translatedFormat('l');

        $detailJadwal = DetailJadwal::with('jadwal')
            ->where('user_id', $permohonan->user_id)
            ->whereRaw('LOWER(hari) = ?', [strtolower($namaHari)])
            ->first();

        if (!$detailJadwal || !$detailJadwal->jadwal) {
            session()->flash('warning', 'Tanggal ' . $tanggalObj->translatedFormat('d F Y') . ' tidak memiliki jadwal magang untuk peserta ini, sehingga jam absen pulang tidak dapat diisi otomatis.');
            return;
        }

        $jadwal = $detailJadwal->jadwal;

        $presensi = presensi::where('user_id', $permohonan->user_id)
            ->whereDate('tanggal', $tglString)
            ->first();

        // Catatan: pengecekan "tidak ada presensi" & "absen_masuk kosong"
        // sudah ditangani lebih awal di handleAbsenPulangApproval().
        if (!empty($presensi->absen_keluar)) {
            session()->flash('warning', 'Tanggal ' . $tanggalObj->translatedFormat('d F Y') . ' sudah memiliki data absen pulang, sehingga data tidak ditimpa.');
            return;
        }

        $presensi->update([
            'absen_keluar' => $jadwal->jam_keluar,
        ]);

        log_book::updateOrCreate(
            [
                'presensi_id' => $presensi->presensi_id ?? $presensi->id,
                'user_id'     => $permohonan->user_id,
            ],
            [
                'kegiatan' => '(ABSEN SUSULAN - Pulang) ' . $permohonan->alasan,
            ]
        );
    }

    public function tolak($id): void
    {
        $permohonan = PermohonanIzinModel::with('user')->findOrFail($id);

        $permohonan->update([
            'status' => 'ditolak',
            'catatan_admin' => $this->catatanAdmin,
        ]);

        $namaUser = $permohonan->user->nama ?? $permohonan->user->name ?? 'Pengguna';
        session()->flash('message', 'Permohonan ' . strtoupper($permohonan->jenis) . ' dari ' . $namaUser . ' telah ditolak.');

        $this->closeDetail();
    }

    public function render()
    {
        $currentUser = Auth::user();

        $permohonans = PermohonanIzinModel::with('user')
            ->when($currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value, function ($query) use ($currentUser) {
                $query->whereHas('user', function ($q) use ($currentUser) {
                    $q->where('mentor', $currentUser->nama);
                });
            })
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->tanggal, function ($query) {
                $query->where(function ($q) {
                    $q->whereDate('tanggal_awal', '<=', $this->tanggal)
                      ->whereDate('tanggal_akhir', '>=', $this->tanggal)
                      ->orWhereDate('tanggal_permohonan', $this->tanggal);
                });
            })
            ->latest('created_at')
            ->paginate(10);

        $totalPending = PermohonanIzinModel::where('status', 'pending')
            ->when($currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value, function ($query) use ($currentUser) {
                $query->whereHas('user', function ($q) use ($currentUser) {
                    $q->where('mentor', $currentUser->nama);
                });
            })
            ->count();

        return view('livewire.dashboard.permohonan-izin', compact('permohonans', 'totalPending'));
    }
}