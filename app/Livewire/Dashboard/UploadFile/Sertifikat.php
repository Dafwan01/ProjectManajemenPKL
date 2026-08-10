<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\file as FileModel;
use App\Models\User;
use App\Notifications\NilaiUpdatedNotification;
use App\Services\CertificateService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Sertifikat extends Component
{
    use WithPagination;

    // Properti Pencarian
    public string $search = '';

    // Properti Modal Formulir Input/Terbitkan Sertifikat
    public bool $showModal = false; 
    public $selectedUserId = null;

    // Properti Modal Pratinjau PDF (Tanpa Membuka Tab Baru)
    public bool $showPdfModal = false;
    public $pdfUserId = null;
    public ?string $previewUrl = null;
    public ?string $previewUserName = null;

    // Field Formulir
    public string $nomorSertifikat = '';
    public string $tanggalTerbit = '';
    public string $namaPenandatangan = '';
    public string $jabatanPenandatangan = '';
    public string $jenisTtd = 'elektronik'; // Default: 'elektronik' atau 'non_elektronik'

    /**
     * Memastikan locale Carbon diatur ke Bahasa Indonesia.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
    }

    public function mount(): void
    {
        $this->tanggalTerbit = date('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Helper untuk mengecek apakah pengguna yang login adalah Mentor secara aman
     */
    private function isMentorUser(): bool
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return false;
        }

        $userRole = $currentUser->role instanceof \UnitEnum 
            ? $currentUser->role->value 
            : $currentUser->role;

        return $userRole === UserRole::MENTOR->value;
    }

    // Modal Formulir Sertifikat
    public function openForm($userId): void
    {
        $this->selectedUserId = $userId;
        $this->nomorSertifikat = 'SERT/' . date('Y') . '/' . str_pad((string) $userId, 4, '0', STR_PAD_LEFT);
        $this->namaPenandatangan = '';
        $this->jabatanPenandatangan = '';
        $this->jenisTtd = 'elektronik';
        $this->showModal = true;
    }

    #[On('close-sertifikat-modal')]
    public function closeForm(): void
    {
        $this->showModal = false;
        $this->selectedUserId = null;
    }

    // Modal Pratinjau PDF
    public function openPdfModal($userId): void
    {
        $user = User::where('user_id', $userId)->first();

        $sertifikatFile = FileModel::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('nama_file', 'Sertifikat')
                      ->orWhere('file', 'like', 'user-sertifikat/%');
            })
            ->latest('file_id')
            ->first();

        if ($sertifikatFile && $sertifikatFile->file) {
            $this->previewUrl = asset('storage/' . $sertifikatFile->file);
            $this->previewUserName = $user?->nama;
            $this->pdfUserId = $userId;
            $this->showPdfModal = true;
        } else {
            session()->flash('error', 'Berkas sertifikat belum tersedia.');
        }
    }

    public function closePdfModal(): void
    {
        $this->showPdfModal = false;
        $this->pdfUserId = null;
        $this->previewUrl = null;
        $this->previewUserName = null;
    }

    /**
     * Eksekusi Buat PDF & Simpan Berkas
     */
    public function generate(): void
    {
        $this->validate([
            'nomorSertifikat'      => 'required|string',
            'tanggalTerbit'        => 'required|date',
            'namaPenandatangan'    => 'required|string|max:255',
            'jabatanPenandatangan' => 'required|string|max:255',
            'jenisTtd'             => 'required|in:elektronik,non_elektronik',
        ], [
            'nomorSertifikat.required'      => 'Nomor sertifikat wajib diisi.',
            'tanggalTerbit.required'        => 'Tanggal terbit wajib diisi.',
            'namaPenandatangan.required'    => 'Nama penandatangan wajib diisi.',
            'jabatanPenandatangan.required' => 'Jabatan penandatangan wajib diisi.',
            'jenisTtd.required'             => 'Pilih jenis tanda tangan.',
        ]);

        try {
            $user = User::where('user_id', $this->selectedUserId)->firstOrFail();
            $certificateService = app(CertificateService::class);

            $relativeFilePath = $certificateService->generateForUser(
                $user,
                $this->nomorSertifikat,
                $this->tanggalTerbit,
 		$user->tanggal_mulai,   // <-- baris baru
    		$user->tanggal_akhir,   // <-- baris baru
                $this->namaPenandatangan,
                $this->jabatanPenandatangan,
                $this->jenisTtd
            );

            FileModel::updateOrCreate(
                [
                    'user_id'   => $user->user_id,
                    'nama_file' => 'Sertifikat',
                ],
                [
                    'file' => $relativeFilePath,
                ]
            );

            // Perbarui Status Peserta Menjadi Lulus
            $user->update([
                'status' => 'Lulus',
            ]);

            // Pemicu Notifikasi ke Peserta Magang
            $currentUser = Auth::user();
            $user->notify(new NilaiUpdatedNotification($currentUser->nama ?? 'Admin/Mentor'));

            session()->flash('message', 'Sertifikat untuk ' . $user->nama . ' berhasil diterbitkan dan status diperbarui menjadi Lulus!');
            $this->closeForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menerbitkan sertifikat: ' . $e->getMessage());
        }
    }

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $users = User::query()
            ->where('role', UserRole::PKL->value)
            // Filter Pencarian
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('sekolah', function ($subQuery) {
                          $subQuery->where('nama_sekolah', 'like', '%' . $this->search . '%');
                      });
                });
            })
            // Filter hanya peserta bimbingan jika yang login adalah Mentor
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            // Pengurutan Prioritas: Status 'aktif' (1) di atas, 'lulus' (2) di bawah, status lain (3)
            ->orderByRaw("CASE 
                WHEN status = 'aktif' THEN 1 
                WHEN status = 'lulus' THEN 2 
                ELSE 3 
            END ASC")
            ->latest('tanggal_mulai')
            ->with(['files', 'sekolah'])
            ->paginate(10);

        return view('livewire.dashboard.upload-file.sertifikat', compact('users'));
    }
}
