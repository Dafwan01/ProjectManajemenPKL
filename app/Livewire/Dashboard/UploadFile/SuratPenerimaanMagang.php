<?php

namespace App\Livewire\Dashboard\UploadFIle;

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class SuratPenerimaanMagang extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showUploadModal = false;
    public $selectedUserId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openUploadModal($id)
    {
        $this->selectedUserId = $id;
        $this->showUploadModal = true;
    }

    #[On('close-upload-modal')]
    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->selectedUserId = null;
    }

    public function render()
    {
        $users = User::query()
            ->where('role', UserRole::PKL->value)
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.upload-file.surat-penerimaan-magang', compact('users'));
    }
}