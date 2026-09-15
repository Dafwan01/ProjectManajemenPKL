<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Illuminate\Support\Facades\Storage;

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
        string $jenisTtd,
        ?string $nipPenandatangan = null
    ): string {
        // Eager load relasi project dan sekolah
        $user->load(['project', 'sekolah']);

        // Tentukan path dan URL berkas sertifikat
        $fileName = 'sertifikat_' . $user->user_id . '_' . time() . '.pdf';
        $relativePath = 'user-sertifikat/' . $fileName;
        $pdfUrl = asset('storage/' . $relativePath);
        $websiteUrl = rtrim(url('/'), '/');

        // Buat barcode / QR Code jika jenis tanda tangan elektronik/otomatis
        $qrCode = null;
        if ($jenisTtd === 'elektronik') {
            $qrOptions = new QROptions([
                'outputInterface'  => QRGdImagePNG::class,
                'outputBase64'     => true,
                'scale'            => 5,
                'imageTransparent' => false,
            ]);
            $qrCode = (new QRCode($qrOptions))->render($pdfUrl);
        }

        $pdf = Pdf::loadView('pdf.sertifikat', compact(
            'user', 
            'nomorSertifikat', 
            'tanggalTerbit',
            'tanggalMulai',
            'tanggalSelesai',
            'namaPenandatangan',
            'jabatanPenandatangan',
            'jenisTtd',
            'nipPenandatangan',
            'qrCode',
            'pdfUrl',
            'websiteUrl'
        ))->setPaper('a4', 'landscape');

        Storage::disk('public')->put($relativePath, $pdf->output());

        return $relativePath;
    }
}