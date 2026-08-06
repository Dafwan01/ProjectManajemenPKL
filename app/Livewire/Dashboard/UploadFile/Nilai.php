<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Nilai extends Component
{
    use WithPagination;

    // Property Search
    public string $search = '';

    // Property Modal Form Input/Edit Nilai
    public bool $showModal = false; 
    public $selectedUserId = null;

    // Property Modal PDF Preview (Tanpa Buka Tab Baru)
    public bool $showPdfModal = false;
    public $pdfUserId = null;

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

    // Modal Form Nilai
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

    // Modal PDF Preview
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
        // Filter hanya anak bimbingan jika yang login adalah Mentor
        ->when($isMentor, function ($query) use ($currentUser) {
            $query->where('mentor', $currentUser->nama);
        })
        ->with(['nilai', 'nilais', 'sekolah'])
        ->latest('tanggal_mulai')
        ->paginate(10);

    return view('livewire.dashboard.upload-file.nilai', compact('users'));
}
}