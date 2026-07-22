<?php

namespace App\Livewire\Dashboard;

use App\Models\presensi;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class MonitoringAbsensi extends Component
{
    use WithPagination;

    public string $tanggal = '';

    public function mount()
    {
        $this->tanggal = now()->format('Y-m-d');
    }

    public function updatingTanggal()
    {
        $this->resetPage();
    }

    public function lihatLokasi()
    {
        // TODO: sesuaikan dengan kebutuhan
        // misal: dispatch untuk buka modal peta, atau redirect
    }

    public function render()
    {
        $presensis = presensi::with('logBooks.user')
            ->when($this->tanggal, function ($query) {
                $query->whereDate('tanggal', $this->tanggal);
            })
            ->orderBy('presensi_id', 'desc')
            ->paginate(10);

        return view('livewire.dashboard.monitoring-absensi', [
            'presensis' => $presensis,
        ]);
    }
}