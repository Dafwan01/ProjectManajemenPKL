<?php

namespace App\Livewire\Dashboard\UploadFIle;

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SuratPenerimaanMagang extends Component
{
    #[Layout('layouts.dashboard')]
    public function render()
    {
        $users = User::query()
            ->where('role', UserRole::PKL->value)
            ->latest('tanggal_mulai')
            ->paginate(10);
        return view('livewire.dashboard.upload-file.surat-penerimaan-magang', compact('users'));
    }
}
