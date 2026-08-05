<?php

namespace App\Livewire\Components;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Nilai;
use App\Models\Divisi;
use App\Models\Bidang;
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

        // Ambil nama Divisi & Bidang milik user.
        // Sengaja query langsung by ID (bukan lewat relasi Eloquent $user->divisi->bidang)
        // supaya tidak tergantung pada guessing foreign key yang pernah bermasalah
        // di model Divisi/Bidang.
        $namaDivisi = null;
        $namaBidang = null;

        if (!empty($selectedUser->divisi_id)) {
            $divisi = Divisi::find($selectedUser->divisi_id);
            $namaDivisi = $divisi?->nama_divisi;

            if ($divisi && !empty($divisi->bidang_id)) {
                $bidang = Bidang::find($divisi->bidang_id);
                $namaBidang = $bidang?->nama_bidang;
            }
        }

        // Tentukan predikat untuk setiap aspek penilaian & rata-rata
        $predikat = null;
        $predikatPerAspek = [];

        if ($nilaiUser) {
            $aspek = [
                'kedisiplinan',
                'kemampuan_teknis',
                'problem_solving',
                'komunikasi_kerjasama',
                'kualitas_ketepatan',
            ];

            foreach ($aspek as $key) {
                $nilai = $nilaiUser->{$key} ?? null;
                $predikatPerAspek[$key] = $this->tentukanPredikat($nilai);
            }

            $predikat = $this->tentukanPredikat($nilaiUser->rata_rata ?? null);
        }

        // Render PDF menggunakan DomPDF
        $pdf = Pdf::loadView('livewire.components.cetak-nilai', [
            'selectedUser'      => $selectedUser,
            'nilaiUser'         => $nilaiUser,
            'predikat'          => $predikat,
            'predikatPerAspek'  => $predikatPerAspek,
            'namaDivisi'        => $namaDivisi,
            'namaBidang'        => $namaBidang,
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

    /**
     * Menentukan predikat berdasarkan rentang nilai.
     */
   private function tentukanPredikat($nilai): ?string
{
    if ($nilai === null || $nilai === '') {
        return null;
    }

    $nilai = (float) $nilai;

    return match (true) {
        $nilai >= 95 => 'Sangat Baik',
        $nilai >= 86 => 'Sangat Baik',
        $nilai >= 80 => 'Baik Sekali',
        $nilai >= 75 => 'Baik',
        $nilai >= 70 => 'Baik',
        $nilai >= 65 => 'Cukup Baik',
        $nilai >= 60 => 'Cukup',
        $nilai >= 40 => 'Kurang',
        default      => 'Sangat Kurang',
    };
}
}