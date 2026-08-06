<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\file as FileModel;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Sertifikat extends Component
{
    use WithPagination;

    // Property Search
    public string $search = '';

    // Property Modal Form Input/Generate Sertifikat
    public bool $showModal = false; 
    public $selectedUserId = null;

    // Property Modal PDF Preview (Tanpa Buka Tab Baru)
    public bool $showPdfModal = false;
    public $pdfUserId = null;
    public ?string $previewUrl = null;
    public ?string $previewUserName = null;

    // Form Fields
    public string $nomorSertifikat = '';
    public string $tanggalTerbit = '';
    public string $namaPenandatangan = '';
    public string $jabatanPenandatangan = '';
    public string $jenisTtd = 'elektronik'; // Default: 'elektronik' atau 'non_elektronik'

    public function mount(): void
    {
        $this->tanggalTerbit = date('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Helper untuk mengecek apakah user yang login adalah Mentor secara aman
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

    // Modal Form Sertifikat
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

    // Modal PDF Preview
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
            session()->flash('error', 'File sertifikat belum tersedia.');
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
     * Eksekusi Generate PDF & Simpan File
     */
    public function generate(): void
    {
        $this->validate([
            'nomorSertifikat'      => 'required|string',
            'tanggalTerbit'        => 'required|date',
            'namaPenandatangan'    => 'required|string|max:255',
            'jabatanPenandatangan' => 'required|string|max:255',
            'jenisTtd'             => 'required|in:elektronik,non_elektronik',
        ]);

        try {
            $user = User::where('user_id', $this->selectedUserId)->firstOrFail();
            $certificateService = app(CertificateService::class);

            $relativeFilePath = $certificateService->generateForUser(
                $user,
                $this->nomorSertifikat,
                $this->tanggalTerbit,
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

            session()->flash('message', 'Sertifikat untuk ' . $user->nama . ' berhasil diterbitkan!');
            $this->closeForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat sertifikat: ' . $e->getMessage());
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
        // Filter hanya anak bimbingan jika yang login adalah Mentor
        ->when($isMentor, function ($query) use ($currentUser) {
            $query->where('mentor', $currentUser->nama);
        })
        ->with(['files', 'sekolah'])
        ->latest('tanggal_mulai')
        ->paginate(10);

    return view('livewire.dashboard.upload-file.sertifikat', compact('users'));
}
}