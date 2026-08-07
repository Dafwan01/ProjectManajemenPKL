<?php

namespace App\Livewire\User;

use App\Models\DetailJadwal;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Jadwal extends Component
{
    public $namaHariIni = '';

    private function currentUser()
    {
        return Auth::user() ?? User::where('role', UserRole::PKL)->first();
    }

    public function render()
    {
        $user = $this->currentUser();

        // Urutan hari kerja tetap Senin-Jumat
        $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $namaHariIni = now()->locale('id')->translatedFormat('l');
        $this->namaHariIni = $namaHariIni;

        $jadwalMingguan = collect();

        if ($user) {
            $detailJadwals = DetailJadwal::with('jadwal')
                ->where('user_id', $user->user_id)
                ->get()
                ->keyBy(function ($item) {
                    return strtolower($item->hari);
                });

            foreach ($urutanHari as $hari) {
                $detail = $detailJadwals->get(strtolower($hari));

                $jadwalMingguan->push([
                    'hari'         => $hari,
                    'jam_masuk'    => $detail?->jadwal?->jam_masuk,
                    'jam_keluar'   => $detail?->jadwal?->jam_keluar,
                    'status_kerja' => $detail?->jadwal?->status_kerja?->value ?? $detail?->jadwal?->status_kerja,
                    'is_hari_ini'  => strtolower($hari) === strtolower($namaHariIni),
                ]);
            }
        }

        return view('livewire.user.jadwal', [
            'jadwalMingguan' => $jadwalMingguan,
            'user' => $user,
        ])->layout('layouts.user');
    }
}