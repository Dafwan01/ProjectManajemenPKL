<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\presensi;
use Carbon\Carbon;

#[Layout('layouts.auth')] // Gunakan layout polos/tanpa sidebar dashboard
class CetakRekapAbsensi extends Component
{
    public $userId;
    public $bulan;
    public $tahun;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->bulan = request()->query('bulan', now()->format('m'));
        $this->tahun = request()->query('tahun', now()->format('Y'));
    }

    public function render()
    {
        $selectedUser = User::findOrFail($this->userId);

        $presensisUser = presensi::with(['logBooks'])
            ->where('user_id', $selectedUser->user_id)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $namaBulan = Carbon::createFromDate((int)$this->tahun, (int)$this->bulan, 1)
            ->translatedFormat('F Y');

        return view('livewire.components.cetak-rekap-absensi', [
            'selectedUser'  => $selectedUser,
            'presensisUser' => $presensisUser,
            'namaBulan'     => $namaBulan,
        ]);
    }
}