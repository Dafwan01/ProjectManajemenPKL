<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\presensi;
use Carbon\Carbon;

class Map extends Component
{
    public function render()
    {
        // Ambil data absensi hari ini beserta data user-nya lewat logBooks
        $absensiHariIni = presensi::with('logBooks.user')
            ->whereDate('tanggal', Carbon::today())
            ->get()
            ->flatMap(function ($presensi) {
                return $presensi->logBooks->map(function ($logBook) use ($presensi) {
                    return [
                        'nama' => $logBook->user->nama ?? 'Pengguna',
                        'sekolah' => $logBook->user->asal_sekolah ?? '-',
                        'jam_masuk' => $presensi->absen_masuk ? substr($presensi->absen_masuk, 0, 5) : '-',
                        'jam_keluar' => $presensi->absen_keluar ? substr($presensi->absen_keluar, 0, 5) : '-',
                        'lat' => (float) $presensi->latitude,
                        'lng' => (float) $presensi->longitude,
                    ];
                });
            });

        return view('livewire.components.map', [
            'locations' => $absensiHariIni
        ]);
    }
}