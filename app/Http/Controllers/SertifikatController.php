<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    /**
     * Membuka / Stream PDF sertifikat milik user yang sedang login
     */
    public function downloadSaya()
    {
        $user = Auth::user();

        // Cek apakah user memiliki file sertifikat di database & storage
        if (!$user->sertifikat || !Storage::disk('public')->exists($user->sertifikat)) {
            return back()->with('error', 'Sertifikat kamu belum diterbitkan oleh mentor/admin.');
        }

        $filePath = storage_path('app/public/' . $user->sertifikat);

        // Tampilkan PDF langsung di browser (inline)
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Sertifikat-' . $user->nama . '.pdf"'
        ]);
    }
}