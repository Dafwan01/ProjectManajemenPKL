<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class RekapAbsensi extends Component
{
    use WithPagination;

    public string $bulan = '';
    public string $tahun = '';
    public string $search = '';
    public string $status = 'aktif'; // Default hanya menampilkan status 'aktif'

    // State Modal
    public bool $showModal = false;
    public ?User $selectedUser = null;

    public function mount(): void
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingBulan(): void
    {
        $this->resetPage();
    }

    public function updatingTahun(): void
    {
        $this->resetPage();
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
            ->when($this->status !== 'semua', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('nama', 'like', '%' . $this->search . '%')
                      ->orWhereHas('sekolah', function ($s) {
                          $s->where('nama_sekolah', 'like', '%' . $this->search . '%');
                      });
                });
            })
            // Sorting custom: Mengurutkan 'aktif' di atas 'lulus', lalu nama A-Z
            ->orderByRaw("CASE WHEN status = 'aktif' THEN 1 WHEN status = 'lulus' THEN 2 ELSE 3 END")
            ->orderBy('nama', 'asc')
            ->paginate(10);

        return view('livewire.dashboard.rekap-absensi', [
            'usersPKL' => $usersPKL,
        ]);
    }
}