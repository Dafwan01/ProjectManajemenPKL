<?php

namespace App\Livewire\Dashboard;

use App\Models\presensi;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class MonitoringAbsensi extends Component
{
    use WithPagination;

    public string $tanggal = '';
    public bool $showMap = false;
    public $locations = [];

    public function mount()
    {
        $this->tanggal = now()->format('Y-m-d');
    }

    public function updatingTanggal()
    {
        $this->resetPage();
    }

public function openMap()
{
    $dataPresensi = Presensi::with(['logBooks.user'])
        ->whereDate('tanggal', $this->tanggal)
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

    $this->locations = $dataPresensi->map(function ($item) {
        $logBook = $item->logBooks->first();
        $user = $logBook ? $logBook->user : null;

        return [
            'nama' => $user->nama ?? 'Tanpa Nama',
            'sekolah' => $user->asal_sekolah ?? '-',
            'jam_masuk' => $item->absen_masuk ? substr($item->absen_masuk, 0, 5) : '-',
            'jam_keluar' => $item->absen_keluar ? substr($item->absen_keluar, 0, 5) : '-',
            'lat' => (float) $item->latitude,
            'lng' => (float) $item->longitude,
        ];
    })->toArray();

    $this->showMap = true;

    $this->dispatch('init-leaflet-map', locations: $this->locations);
}

    public function closeMap()
    {
        $this->showMap = false;
    }

    public function render()
    {
        $presensis = presensi::with('logBooks.user')
            ->whereHas('logBooks.user', function ($query) {
                $query->where('role', 'PKL');
            })
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