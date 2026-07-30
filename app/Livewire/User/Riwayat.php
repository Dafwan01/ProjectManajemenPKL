<?php

namespace App\Livewire\User;

use App\Models\log_book;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
  use App\Models\presensi as PresensiModel;
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

    /**
     * Membuka modal edit dan menyiapkan data logbook yang dipilih
     */
    public function editLogbook($id)
    {
        $logBook = log_book::find($id);

        if ($logBook) {
            $this->editingId = $logBook->log_book_id;
            $this->editingLogbook = $logBook->kegiatan ?? '';
            $this->isEditModalOpen = true;
        }
    }

    /**
     * Menyimpan perubahan logbook ke database
     */
    public function updateLogbook()
    {
        $this->validate();

        $logBook = log_book::findOrFail($this->editingId);

        // Pastikan user cuma bisa edit logbook miliknya sendiri
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

    $presensis = PresensiModel::with(['logBooks'])
        ->where('user_id', $userId)
        ->when($this->filterStatus !== 'semua', function ($query) {
            $query->where('status_kehadiran', strtolower($this->filterStatus));
        })
        ->when($this->tanggalMulai, function ($query) {
            $query->whereDate('tanggal', '>=', $this->tanggalMulai);
        })
        ->when($this->tanggalSelesai, function ($query) {
            $query->whereDate('tanggal', '<=', $this->tanggalSelesai);
        })
        ->latest('presensi_id')
        ->paginate(10);

    $dataRiwayat = $presensis->through(function ($presensi) {
        $logBook = $presensi->logBooks->first();

        return [
            'id'         => $logBook->log_book_id ?? null,
            'tanggal'    => $presensi->tanggal ? $presensi->tanggal->translatedFormat('l, d/m/Y') : '-',
            'jam_masuk'  => $presensi->absen_masuk ? substr($presensi->absen_masuk, 0, 5) . ' WIB' : '-',
            'jam_pulang' => $presensi->absen_keluar ? substr($presensi->absen_keluar, 0, 5) . ' WIB' : '-',
            'status'     => strtoupper($presensi->status_kehadiran?->value ?? '-'),
            'logbook'    => $logBook->kegiatan ?? null, // null berarti belum diisi (masih presensi masuk)
        ];
    });

    $totalHadir = PresensiModel::where('user_id', $userId)
        ->where('status_kehadiran', 'hadir')
        ->count();

    $totalIzinSakit = PresensiModel::where('user_id', $userId)
        ->whereIn('status_kehadiran', ['izin', 'sakit'])
        ->count();

    return view('livewire.user.riwayat', [
        'dataRiwayat' => $dataRiwayat,
        'totalHadir' => $totalHadir,
        'totalIzinSakit' => $totalIzinSakit,
    ]);
}
}