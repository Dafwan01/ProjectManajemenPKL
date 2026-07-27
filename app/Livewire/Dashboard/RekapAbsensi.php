<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\User;

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
        return view('livewire.dashboard.rekap-absensi', [
            'usersPKL' => User::where('role', 'PKL')->get(),
        ]);
    }
}