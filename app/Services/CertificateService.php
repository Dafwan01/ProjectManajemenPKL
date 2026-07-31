<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateService
{
    public function generateForUser(User $user, string $nomorSertifikat, string $tanggalTerbit): string
    {
        // Load view PDF sertifikat
        $pdf = Pdf::loadView('pdf.sertifikat', compact('user', 'nomorSertifikat', 'tanggalTerbit'));

        // Buat nama file unik
        $fileName = 'sertifikat_' . $user->user_id . '_' . time() . '.pdf';
        
        // Simpan ke storage/app/public/user-sertifikat/
        $relativePath = 'user-sertifikat/' . $fileName;
        Storage::disk('public')->put($relativePath, $pdf->output());

        // Kembalikan path relatif untuk disimpan ke kolom 'file' di tabel 'files'
        return $relativePath;
    }
}