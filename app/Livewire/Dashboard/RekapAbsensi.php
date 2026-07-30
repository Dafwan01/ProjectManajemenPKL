<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole; // Import Enum UserRole
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Import Auth Facade
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class RekapAbsensi extends Component
{
    public string $bulan = '';
    public string $tahun = '';

    public function mount(): void
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    public function render()
    {
        $currentUser = Auth::user();

        $usersPKL = User::query()
            ->where('role', UserRole::PKL->value ?? 'PKL')
            // Filter anak bimbingan jika pengakses adalah Mentor
            ->when($currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->get();

        return view('livewire.dashboard.rekap-absensi', [
            'usersPKL' => $usersPKL,
        ]);
    }
}