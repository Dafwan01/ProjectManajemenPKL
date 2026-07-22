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
    // Eager load relasi logBooks beserta user-nya
    $dataPresensi = Presensi::with(['logBooks.user'])
        ->whereDate('tanggal', $this->tanggal)
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

    $this->locations = $dataPresensi->map(function ($item) {
        // Ambil logbook pertama dari presensi ini jika ada
        $logBook = $item->logBooks->first();
        $user = $logBook ? $logBook->user : null;

        // Jika presensi punya relasi langsung ke user, pakai ini sebagai cadangan:
        // $user = $user ?? $item->user; 

        return [
            // Ambil nama dari user (atau ganti $user->name jika kolomnya 'name')
            'nama' => $user->nama ?? $user->name ?? 'Tanpa Nama',
            'sekolah' => $user->asal_sekolah ?? '-',
            
            // Format jam absen dari created_at atau atribut jam
            'jam' => $item->absen_masuk
                ? Carbon::parse($item->absen_masuk)->format('H:i') 
                : ($item->created_at ? $item->created_at->format('H:i') : '-'),
                
            'lat' => (float) $item->latitude,
            'lng' => (float) $item->longitude,
        ];
    })->toArray();

    $this->showMap = true;

    // Trigger event ke AlpineJS untuk render marker
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