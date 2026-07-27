<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class Nilai extends Component
{
    // GANTI NAMA PROPERTY INI (Jangan sama dengan nama function openForm)
    public $showModal = false; 
    public $selectedUserId = null;

    public function openForm($userId)
    {
        $this->selectedUserId = $userId;
        $this->showModal = true; // Diubah
    }

    #[On('close-nilai-modal')]
    public function closeForm()
    {
        $this->showModal = false; // Diubah
        $this->selectedUserId = null;
    }

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $users = User::query()
            ->where('role', UserRole::PKL->value)
             ->with('nilai')
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.upload-file.nilai', compact('users'));
    }
}