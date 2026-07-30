<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Sertifikat extends Component
{
    use WithPagination;

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

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $users = User::query()
            ->where('role', UserRole::PKL->value)
            // Filter anak bimbingan jika pengakses adalah Mentor
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.upload-file.sertifikat', compact('users'));
    }
}