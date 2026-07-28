<?php

namespace App\Livewire\User;

use App\Models\log_book;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
class Riwayat extends Component
{
    use WithPagination;

    public $filterStatus = 'semua';
    public $tanggalMulai = '';
    public $tanggalSelesai = '';

    // State Edit Logbook
    public $editingId = null;
    public $editingLogbook = '';
    public $isEditModalOpen = false;

    protected $rules = [
        'editingLogbook' => 'required|min:10',
    ];

    protected $messages = [
        'editingLogbook.required' => 'Logbook harian wajib diisi.',
        'editingLogbook.min'      => 'Logbook minimal 10 karakter.',
    ];

    public function mount()
{
    $this->tanggalMulai = now()->startOfMonth()->format('Y-m-d');
    $this->tanggalSelesai = now()->format('Y-m-d');
}

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingTanggalMulai()
    {
        $this->resetPage();
    }

    public function updatingTanggalSelesai()
    {
        $this->resetPage();
    }

    public function resetFilterTanggal()
    {
        $this->tanggalMulai = '';
        $this->tanggalSelesai = '';
        $this->resetPage();
    }


    public function editLogbook($id)
    {
        $logBook = log_book::find($id);

        if ($logBook) {
            $this->editingId = $logBook->log_book_id;
            $this->editingLogbook = $logBook->kegiatan ?? '';
            $this->isEditModalOpen = true;
        }
    }

    public function updateLogbook()
    {
        $this->validate();

        $logBook = log_book::findOrFail($this->editingId);

        if ($logBook->user_id !== auth()->id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengedit logbook ini.');
            $this->closeModal();
            return;
        }

        $logBook->update([
            'kegiatan' => $this->editingLogbook,
        ]);

        $this->closeModal();
        session()->flash('message', 'Logbook berhasil diperbarui!');
    }

    public function closeModal()
    {
        $this->isEditModalOpen = false;
        $this->editingId = null;
        $this->editingLogbook = '';
        $this->resetValidation();
    }

    public function render()
    {
        $userId = auth()->id();

        $logBooks = log_book::with(['presensi', 'user'])
            ->where('user_id', $userId)
            ->when($this->filterStatus !== 'semua', function ($query) {
                $query->whereHas('presensi', function ($q) {
                    $q->where('status_kehadiran', strtolower($this->filterStatus));
                });
            })
            ->when($this->tanggalMulai, function ($query) {
                $query->whereHas('presensi', function ($q) {
                    $q->whereDate('tanggal', '>=', $this->tanggalMulai);
                });
            })
            ->when($this->tanggalSelesai, function ($query) {
                $query->whereHas('presensi', function ($q) {
                    $q->whereDate('tanggal', '<=', $this->tanggalSelesai);
                });
            })
            ->latest('log_book_id')
            ->paginate(10);

        $dataRiwayat = $logBooks->through(function ($logBook) {
            $presensi = $logBook->presensi;

            return [
                'id'         => $logBook->log_book_id,
                'nama'       => $logBook->user->nama ?? '-',
                'sekolah'    => $logBook->user->asal_sekolah ?? '-',
                'tanggal'    => $presensi && $presensi->tanggal ? $presensi->tanggal->translatedFormat('l, d/m/Y') : '-',
                'jam_masuk'  => $presensi && $presensi->absen_masuk ? substr($presensi->absen_masuk, 0, 5) . ' WIB' : '-',
                'jam_pulang' => $presensi && $presensi->absen_keluar ? substr($presensi->absen_keluar, 0, 5) . ' WIB' : '-',
                'status'     => $presensi ? strtoupper($presensi->status_kehadiran?->value ?? '-') : '-',
                'logbook'    => $logBook->kegiatan ?? '-',
                'latitude'   => $presensi->latitude ?? null,
                'longitude'  => $presensi->longitude ?? null,
            ];
        });

        $totalHadir = log_book::where('user_id', $userId)
            ->whereHas('presensi', fn ($q) => $q->where('status_kehadiran', 'hadir'))
            ->count();

        $totalIzinSakit = log_book::where('user_id', $userId)
            ->whereHas('presensi', fn ($q) => $q->whereIn('status_kehadiran', ['izin', 'sakit']))
            ->count();

        return view('livewire.user.riwayat', [
            'dataRiwayat' => $dataRiwayat,
            'totalHadir' => $totalHadir,
            'totalIzinSakit' => $totalIzinSakit,
        ]);
    }
}