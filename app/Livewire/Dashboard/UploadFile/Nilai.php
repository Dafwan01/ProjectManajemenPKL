<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Nilai extends Component
{
    use WithPagination;

    // Properti Pencarian
    public string $search = '';

    // Properti Modal Formulir Input/Edit Nilai
    public bool $showModal = false; 
    public $selectedUserId = null;

    // Properti Modal Pratinjau PDF (Tanpa Membuka Tab Baru)
    public bool $showPdfModal = false;
    public $pdfUserId = null;

    /**
     * Memastikan locale Carbon diatur ke Bahasa Indonesia.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Helper untuk mengecek apakah pengguna yang masuk adalah Mentor secara aman.
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

    // Modal Formulir Nilai
    public function openForm($userId): void
    {
        $this->selectedUserId = $userId;
        $this->showModal = true;
    }

    #[On('close-nilai-modal')]
    public function closeForm(): void
    {
        $this->showModal = false;
        $this->selectedUserId = null;
    }

    // Modal Pratinjau PDF
    public function openPdfModal($userId): void
    {
        $this->pdfUserId = $userId;
        $this->showPdfModal = true;
    }

    public function closePdfModal(): void
    {
        $this->showPdfModal = false;
        $this->pdfUserId = null;
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
            ->with(['nilai', 'nilais', 'sekolah'])
            ->paginate(10);

        return view('livewire.dashboard.upload-file.nilai', compact('users'));
    }
}
