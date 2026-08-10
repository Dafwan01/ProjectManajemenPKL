<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateService
{
    public function generateForUser(
    User $user, 
    string $nomorSertifikat, 
    string $tanggalTerbit,
    string $tanggalMulai,
    string $tanggalSelesai,
    string $namaPenandatangan,
    string $jabatanPenandatangan,
    string $jenisTtd
): string {
    // Eager load relasi project dan sekolah
    $user->load(['project', 'sekolah']);

    $pdf = Pdf::loadView('pdf.sertifikat', compact(
        'user', 
        'nomorSertifikat', 
        'tanggalTerbit',
        'tanggalMulai',
        'tanggalSelesai',
        'namaPenandatangan',
        'jabatanPenandatangan',
        'jenisTtd'
    ))->setPaper('a4', 'landscape');

    $fileName = 'sertifikat_' . $user->user_id . '_' . time() . '.pdf';
    $relativePath = 'user-sertifikat/' . $fileName;
    Storage::disk('public')->put($relativePath, $pdf->output());

    return $relativePath;
}
}