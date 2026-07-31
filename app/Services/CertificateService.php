<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function generateForUser(User $user, string $nomorSertifikat, string $tanggalTerbit): string
    {
        // Set opsi DomPDF agar bisa baca aset lokal/remote
        $pdf = Pdf::loadView('pdf.sertifikat', [
            'user' => $user,
            'nomorSertifikat' => $nomorSertifikat,
            'tanggalTerbit' => $tanggalTerbit,
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'chroot' => public_path(), // Mengizinkan akses folder public
        ]);

        $fileName = 'sertifikat_' . $user->user_id . '_' . time() . '.pdf';
        $filePath = 'sertifikat/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());

        $user->update([
            'sertifikat' => $filePath,
        ]);

        return $filePath;
    }
}