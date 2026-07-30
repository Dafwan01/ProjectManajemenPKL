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

    // Property UI State
    public $showModal = false; 
    public $selectedUserId = null;

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

    public function openForm($userId)
    {
        $this->selectedUserId = $userId;
        $this->showModal = true;
    }

    #[On('close-nilai-modal')]
    public function closeForm()
    {
        $this->showModal = false;
        $this->selectedUserId = null;
    }

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $users = User::query()
            ->where('role', UserRole::PKL->value)
            // Filter hanya anak bimbingan jika yang login adalah Mentor
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->with('nilai')
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.upload-file.nilai', compact('users'));
    }
}