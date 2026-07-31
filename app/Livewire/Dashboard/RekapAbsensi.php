<?php
namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class RekapAbsensi extends Component
{
    public string $bulan = '';
    public string $tahun = '';

    // State Modal
    public bool $showModal = false;
    public ?User $selectedUser = null;

    public function mount(): void
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    public function bukaModalRekap($userId): void
    {
        $this->selectedUser = User::where('user_id', $userId)->first();

        if ($this->selectedUser) {
            $this->showModal = true;
        }
    }

    public function tutupModal(): void
    {
        $this->showModal = false;
        $this->selectedUser = null;
    }

    public function render()
    {
        $currentUser = Auth::user();

        $usersPKL = User::query()
            ->where('role', UserRole::PKL->value ?? 'PKL')
            ->when($currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->get();

        return view('livewire.dashboard.rekap-absensi', [
            'usersPKL' => $usersPKL,
        ]);
    }
}