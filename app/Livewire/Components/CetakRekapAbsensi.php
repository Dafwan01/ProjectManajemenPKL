<?php

namespace App\Livewire\Components;

use App\Http\Controllers\Controller; // Menggunakan Base Controller Laravel
use App\Models\User;
use App\Models\presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller as RoutingController;

class CetakRekapAbsensi extends RoutingController
{
    public function __invoke(Request $request, $userId = null)
    {
        $userId = $userId ?? $request->route('userId') ?? $request->route('id');
        $bulan  = $request->query('bulan', now()->format('m'));
        $tahun  = $request->query('tahun', now()->format('Y'));

        $selectedUser = User::findOrFail($userId);

        $presensisUser = presensi::with(['logBooks'])
            ->where('user_id', $selectedUser->user_id ?? $selectedUser->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $namaBulan = Carbon::createFromDate((int)$tahun, (int)$bulan, 1)
            ->translatedFormat('F Y');

        // Render PDF menggunakan DomPDF
        $pdf = Pdf::loadView('livewire.components.cetak-rekap-absensi', [
            'selectedUser'  => $selectedUser,
            'presensisUser' => $presensisUser,
            'namaBulan'     => $namaBulan,
        ])->setPaper('a4', 'portrait');

        // Stream langsung agar Chrome PDF Viewer otomatis aktif
        return response()->stream(
            function () use ($pdf) {
                echo $pdf->output();
            },
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Rekap_Absensi_' . ($selectedUser->nama ?? $selectedUser->name) . '.pdf"',
            ]
        );
    }
}