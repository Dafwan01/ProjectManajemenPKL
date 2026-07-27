<?php

namespace App\Livewire\Dashboard;

use App\Models\presensi;
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
        $this->tanggal = now()->format('Y-m-d');
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
        // Panggil dengan full namespace \App\Models\PermohonanIzin
        $permohonan = \App\Models\PermohonanIzin::findOrFail($id);
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
    $permohonan = \App\Models\PermohonanIzin::findOrFail($id);

    // 1. Update status permohonan izin
    $permohonan->update([
        'status' => 'disetujui',
        'catatan_admin' => $this->catatanAdmin,
    ]);

    $tanggal = $permohonan->tanggal->format('Y-m-d');

    // 2. Cari presensi yang terhubung dengan logbook milik user pada tanggal tersebut
    $presensi = \App\Models\presensi::whereHas('logBooks', function ($query) use ($permohonan) {
        $query->where('user_id', $permohonan->user_id);
    })->whereDate('tanggal', $tanggal)->first();

    if ($presensi) {
        // Jika data presensi sudah ada, update status kehadirannya
        $presensi->update([
            'status_kehadiran' => $permohonan->jenis,
        ]);
    } else {
        // Jika belum ada record presensi pada tanggal tersebut, buat record presensi baru
        $presensi = \App\Models\Presensi::create([
            'tanggal' => $tanggal,
            'status_kehadiran' => $permohonan->jenis,
        ]);

        // Hubungkan presensi baru ini dengan LogBook user
        \App\Models\log_book::create([
            'user_id' => $permohonan->user_id,
            'presensi_id' => $presensi->presensi_id, // Sesuaikan dengan primary key tabel presensis
            'kegiatan' => 'Izin/Sakit: ' . $permohonan->alasan,
        ]);
    }

    session()->flash('message', 'Permohonan ' . $permohonan->jenis . ' dari ' . $permohonan->user->nama . ' telah disetujui.');
    $this->closeDetail();
}

    public function tolak($id)
    {
        $permohonan = \App\Models\PermohonanIzin::findOrFail($id);
        $permohonan->update([
            'status' => 'ditolak',
            'catatan_admin' => $this->catatanAdmin,
        ]);

        session()->flash('message', 'Permohonan ' . $permohonan->jenis . ' dari ' . $permohonan->user->nama . ' telah ditolak.');
        $this->closeDetail();
    }

    public function render()
    {
        $permohonans = \App\Models\PermohonanIzin::with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->tanggal, function ($query) {
                $query->whereDate('tanggal', $this->tanggal);
            })
            ->latest('created_at')
            ->paginate(10);

        $totalPending = \App\Models\PermohonanIzin::where('status', 'pending')->count();

        return view('livewire.dashboard.permohonan-izin', compact('permohonans', 'totalPending'));
    }
}