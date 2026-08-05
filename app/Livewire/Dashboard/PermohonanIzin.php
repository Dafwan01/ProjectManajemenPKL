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

    public function mount()
    {
        $this->tanggal = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingTanggal()
    {
        $this->resetPage();
    }

    public function resetFilterTanggal()
    {
        $this->tanggal = '';
    }

    public function openDetail($id)
    {
        $this->selectedId = $id;
        $permohonan = PermohonanIzinModel::findOrFail($id);
        $this->catatanAdmin = $permohonan->catatan_admin ?? '';
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedId = null;
        $this->catatanAdmin = '';
    }

  public function setujui($id)
{
    $permohonan = PermohonanIzinModel::with('user')->findOrFail($id);

    $permohonan->update([
        'status' => 'disetujui',
        'catatan_admin' => $this->catatanAdmin,
    ]);

    $jenisStr = strtolower($permohonan->jenis);

    if ($jenisStr === 'absen') {
        $this->prosesAbsen($permohonan);
    } elseif ($jenisStr === 'absen pulang') {
        $this->prosesAbsenPulang($permohonan);
    } else {
        $this->prosesIzinSakit($permohonan, $jenisStr);
    }

    $namaUser = $permohonan->user->nama ?? $permohonan->user->name ?? 'Pengguna';
    session()->flash('message', 'Permohonan ' . strtoupper($permohonan->jenis) . ' dari ' . $namaUser . ' telah disetujui.');

    $this->closeDetail();
}

private function prosesIzinSakit($permohonan, string $statusKehadiran)
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
            $presensi->update([
                'status_kehadiran' => $statusKehadiran,
            ]);
        } else {
            $presensiNew = presensi::create([
                'user_id'          => $permohonan->user_id,
                'tanggal'          => $tglString,
                'status_kehadiran' => $statusKehadiran,
            ]);

            log_book::create([
                'user_id'     => $permohonan->user_id,
                'presensi_id' => $presensiNew->presensi_id ?? $presensiNew->id,
                'kegiatan'    => '(' . strtoupper($permohonan->jenis) . ') ' . $permohonan->alasan,
            ]);
        }
    }
}

/**
 * Proses persetujuan pengajuan ABSEN (jenis = 'absen').
 * Buat/lengkapi presensi pada tanggal yang diajukan, isi JAM MASUK & JAM KELUAR
 * sekaligus sesuai jadwal magang user hari itu.
 */
private function prosesAbsen($permohonan)
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
        session()->flash('warning', 'Tanggal ' . $tanggalObj->translatedFormat('d F Y') . ' tidak memiliki jadwal magang untuk user ini, sehingga jam absen tidak bisa diisi otomatis.');
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

    // Isi jam masuk, hanya jika masih kosong
    if (empty($presensi->absen_masuk)) {
        $dataUpdate['absen_masuk'] = $jadwal->jam_masuk;
        $keterangan[] = 'Masuk';
    }

    // Isi jam keluar, hanya jika masih kosong
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
        session()->flash('warning', 'Tanggal ' . $tanggalObj->translatedFormat('d F Y') . ' sudah memiliki data absen masuk & pulang, sehingga tidak ada yang perlu ditimpa.');
    }
}

/**
 * Proses persetujuan pengajuan ABSEN PULANG (jenis = 'absen pulang').
 * Cek dulu apakah ada presensi di tanggal yang diajukan dan apakah jam keluarnya masih kosong,
 * baru isi sesuai jadwal magang user hari itu.
 */
private function prosesAbsenPulang($permohonan)
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
        session()->flash('warning', 'Tanggal ' . $tanggalObj->translatedFormat('d F Y') . ' tidak memiliki jadwal magang untuk user ini, sehingga jam absen pulang tidak bisa diisi otomatis.');
        return;
    }

    $jadwal = $detailJadwal->jadwal;

    // Cek dulu apakah presensi di tanggal itu SUDAH ADA
    $presensi = presensi::where('user_id', $permohonan->user_id)
        ->whereDate('tanggal', $tglString)
        ->first();

    if (!$presensi) {
        session()->flash('warning', 'Tidak ditemukan data presensi pada tanggal ' . $tanggalObj->translatedFormat('d F Y') . '. Absen pulang tidak dapat diisi karena belum ada data kehadiran di hari tersebut.');
        return;
    }

    // Cek apakah jam keluar memang masih kosong
    if (!empty($presensi->absen_keluar)) {
        session()->flash('warning', 'Tanggal ' . $tanggalObj->translatedFormat('d F Y') . ' sudah memiliki data absen pulang, sehingga tidak ditimpa.');
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
    public function tolak($id)
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
                    $q->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
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