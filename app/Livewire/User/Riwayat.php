<?php

namespace App\Livewire\User;

use App\Models\log_book;
use App\Models\presensi as PresensiModel;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\Toastable;

#[Layout('layouts.user')]
class Riwayat extends Component
{
    use Toastable;

    use WithPagination;

    public $filterStatus = 'semua';
    public $tanggalMulai = '';
    public $tanggalSelesai = '';

    // State Edit Logbook (Ubah acuan dari editingId ke editingPresensiId)
    public $editingPresensiId = null;
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
     * Membuka modal edit berdasarkan presensi_id
     */
    public function editLogbook($presensiId)
    {
        $presensi = PresensiModel::with('logBooks')->find($presensiId);

        if ($presensi && $presensi->user_id === auth()->id()) {
            $logBook = $presensi->logBooks->first();

            $this->editingPresensiId = $presensi->presensi_id;
            $this->editingLogbook = $logBook?->kegiatan ?? '';
            $this->isEditModalOpen = true;
        }
    }

    /**
     * Menyimpan atau membuat baru logbook ke database
     */
    public function updateLogbook()
    {
        $this->validate();

        $presensi = PresensiModel::findOrFail($this->editingPresensiId);

        // Pastikan user cuma bisa edit logbook miliknya sendiri
        if ($presensi->user_id !== auth()->id()) {
            $this->toastError( 'Anda tidak memiliki akses untuk mengedit logbook ini.');
            $this->closeModal();
            return;
        }

        // Gunakan updateOrCreate: Buat baru jika belum ada, atau update jika sudah ada
        log_book::updateOrCreate(
            [
                'presensi_id' => $presensi->presensi_id,
                'user_id'     => auth()->id(),
            ],
            [
                'kegiatan'    => $this->editingLogbook,
            ]
        );

        $this->closeModal();
        $this->toastSuccess( 'Logbook berhasil diperbarui!');
    }

    public function closeModal()
    {
        $this->isEditModalOpen = false;
        $this->editingPresensiId = null;
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
                'presensi_id' => $presensi->presensi_id, // Gunakan ID Presensi
                'tanggal'     => $presensi->tanggal ? $presensi->tanggal->translatedFormat('l, d/m/Y') : '-',
                'jam_masuk'   => $presensi->absen_masuk ? substr($presensi->absen_masuk, 0, 5) . ' WIB' : '-',
                'jam_pulang'  => $presensi->absen_keluar ? substr($presensi->absen_keluar, 0, 5) . ' WIB' : '-',
                'status'      => strtoupper($presensi->status_kehadiran?->value ?? '-'),
                'logbook'     => $logBook->kegiatan ?? null,
            ];
        });

        $totalHadir = PresensiModel::where('user_id', $userId)
            ->where('status_kehadiran', 'hadir')
            ->count();

        $totalIzinSakit = PresensiModel::where('user_id', $userId)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])
            ->count();

        return view('livewire.user.riwayat', [
            'dataRiwayat'    => $dataRiwayat,
            'totalHadir'     => $totalHadir,
            'totalIzinSakit' => $totalIzinSakit,
        ]);
    }
}