<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole; // Import Enum UserRole
use App\Models\log_book;
use App\Models\PermohonanIzin as PermohonanIzinModel;
use App\Models\presensi;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth; // Import Auth Facade
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
        // Opsional: Kosongkan tanggal saat mount agar menampilkan semua permohonan secara default
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

        // Tentukan tanggal awal dan akhir
        $startDate = $permohonan->tanggal_awal ? Carbon::parse($permohonan->tanggal_awal) : Carbon::parse($permohonan->tanggal_permohonan);
        $endDate   = $permohonan->tanggal_akhir ? Carbon::parse($permohonan->tanggal_akhir) : $startDate;

        $period = CarbonPeriod::create($startDate, $endDate);

        // Tentukan status kehadiran berdasarkan Enum yang valid
        // Jika tipe pengajuan 'absen', kita set sebagai 'hadir' sesuai instruksi
        $jenisStr = strtolower($permohonan->jenis);
        $statusKehadiran = ($jenisStr === 'absen') ? 'hadir' : $jenisStr;

        foreach ($period as $date) {
            $tglString = $date->format('Y-m-d');

            // Cari presensi user di tanggal tersebut
            $presensi = presensi::where('user_id', $permohonan->user_id)
                ->whereDate('tanggal', $tglString)
                ->first();

            if ($presensi) {
                // Update status kehadiran jika data presensi sudah ada
                $presensi->update([
                    'status_kehadiran' => $statusKehadiran,
                ]);
            } else {
                // Buat record presensi baru dengan status kehadiran 'hadir' (jika pengajuan absen)
                $presensiNew = presensi::create([
                    'user_id'          => $permohonan->user_id,
                    'tanggal'          => $tglString,
                    'status_kehadiran' => $statusKehadiran,
                ]);

                // Catat ke LogBook
                log_book::create([
                    'user_id'     => $permohonan->user_id,
                    'presensi_id' => $presensiNew->presensi_id ?? $presensiNew->id,
                    'kegiatan'    => '(' . strtoupper($permohonan->jenis) . ') ' . $permohonan->alasan,
                ]);
            }
        }

        $namaUser = $permohonan->user->nama ?? $permohonan->user->name ?? 'Pengguna';
        session()->flash('message', 'Permohonan ' . strtoupper($permohonan->jenis) . ' dari ' . $namaUser . ' telah disetujui.');
        
        $this->closeDetail();
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
            // Filter anak bimbingan jika pengakses adalah Mentor
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

        // Counter total pending juga difilter sesuai mentor
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