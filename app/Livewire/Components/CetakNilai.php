<?php

namespace App\Livewire\Components;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller as RoutingController;

class CetakNilai extends RoutingController
{
    public function __invoke(Request $request, $userId = null)
    {
        $userId = $userId ?? $request->route('userId') ?? $request->route('id');
        $selectedUser = User::findOrFail($userId);

        // Ambil 1 record Nilai berdasarkan user_id
        $nilaiUser = Nilai::where('user_id', $selectedUser->user_id ?? $selectedUser->id)->first();

        // Render PDF menggunakan DomPDF
        $pdf = Pdf::loadView('livewire.components.cetak-nilai', [
            'selectedUser' => $selectedUser,
            'nilaiUser'    => $nilaiUser,
        ])->setPaper('a4', 'portrait');

        return response()->stream(
            function () use ($pdf) {
                echo $pdf->output();
            },
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Rekap_Nilai_' . ($selectedUser->nama ?? $selectedUser->name) . '.pdf"',
            ]
        );
    }
}