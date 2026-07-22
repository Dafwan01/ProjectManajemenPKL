<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\presensi;
use Carbon\Carbon;

class Map extends Component
{
    public function render()
    {
        // Ambil data absensi hari ini beserta data user-nya
        $absensiHariIni = presensi::with('user')
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->user->name ?? 'Pengguna',
                    'sekolah' => $item->user->asal_sekolah ?? '-',
                    'jam' => $item->jam_masuk ?? $item->created_at->format('H:i'),
                    'lat' => (float) $item->latitude,
                    'lng' => (float) $item->longitude,
                ];
            });

        return view('livewire.components.map', [
            'locations' => $absensiHariIni
        ]);
    }
}