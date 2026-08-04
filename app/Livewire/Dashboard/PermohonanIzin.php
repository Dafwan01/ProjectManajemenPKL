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

        // 1. Update status permohonan izin
        $permohonan->update([
            'status' => 'disetujui',
            'catatan_admin' => $this->catatanAdmin,
        ]);

        $jenisStr = strtolower($permohonan->jenis);

        if ($jenisStr === 'absen') {
            $this->prosesAbsenSusulan($permohonan);
        } else {
            $this->prosesIzinSakit($permohonan, $jenisStr);
        }

        $namaUser = $permohonan->user->nama ?? $permohonan->user->name ?? 'Pengguna';
        session()->flash('message', 'Permohonan ' . strtoupper($permohonan->jenis) . ' dari ' . $namaUser . ' telah disetujui.');

        $this->closeDetail();
    }

    /**
     * Logic lama: untuk pengajuan izin/sakit, tandai status_kehadiran
     * di presensi sepanjang rentang tanggal.
     */
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
     * Logic baru: untuk pengajuan absen susulan (lupa absen masuk/pulang),
     * isi jam absen_masuk / absen_keluar di presensi sesuai jam default
     * dari jadwal magang user pada hari tersebut.
     */
    private function prosesAbsenSusulan($permohonan)
    {
        $startDate = $permohonan->tanggal_awal ? Carbon::parse($permohonan->tanggal_awal) : Carbon::parse($permohonan->tanggal_permohonan);
        $endDate   = $permohonan->tanggal_akhir ? Carbon::parse($permohonan->tanggal_akhir) : $startDate;

        $period = CarbonPeriod::create($startDate, $endDate);
        $tanggalTanpaJadwal = [];

        foreach ($period as $date) {
            $tglString = $date->format('Y-m-d');
            $namaHari  = $date->copy()->locale('id')->translatedFormat('l'); // Senin, Selasa, dst

            $detailJadwal = DetailJadwal::with('jadwal')
                ->where('user_id', $permohonan->user_id)
                ->whereRaw('LOWER(hari) = ?', [strtolower($namaHari)])
                ->first();

            if (!$detailJadwal || !$detailJadwal->jadwal) {
                $tanggalTanpaJadwal[] = $tglString;
                continue;
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

            // Hanya isi jika benar-benar dicentang saat pengajuan, dan belum ada datanya
            if ($permohonan->absen_masuk && !$presensi->absen_masuk) {
                $dataUpdate['absen_masuk'] = $jadwal->jam_masuk;
            }

            if ($permohonan->absen_pulang && !$presensi->absen_keluar) {
                $dataUpdate['absen_keluar'] = $jadwal->jam_keluar;
            }

            if (!empty($dataUpdate)) {
                $dataUpdate['status_kehadiran'] = 'hadir';
                $presensi->update($dataUpdate);

                $keterangan = [];
                if (isset($dataUpdate['absen_masuk'])) $keterangan[] = 'Masuk';
                if (isset($dataUpdate['absen_keluar'])) $keterangan[] = 'Pulang';

                log_book::updateOrCreate(
                    [
                        'presensi_id' => $presensi->presensi_id ?? $presensi->id,
                        'user_id'     => $permohonan->user_id,
                    ],
                    [
                        'kegiatan' => '(ABSEN SUSULAN - ' . implode(' & ', $keterangan) . ') ' . $permohonan->alasan,
                    ]
                );
            }
        }

        if (!empty($tanggalTanpaJadwal)) {
            session()->flash('warning', 'Sebagian tanggal (' . implode(', ', $tanggalTanpaJadwal) . ') tidak memiliki jadwal magang untuk user ini, sehingga jam absen tidak bisa diisi otomatis.');
        }
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