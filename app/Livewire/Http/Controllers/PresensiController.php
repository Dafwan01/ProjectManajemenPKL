<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Ambil data gambar Base64 dari request
        $imageParts = explode(";base64,", $request->foto);
        $imageBase64 = base64_decode($imageParts[1]);

        // 2. Buat nama file unik
        $fileName = 'presensi/' . uniqid() . '.jpg';

        // 3. Simpan file gambar ke storage publik (storage/app/public/presensi)
        Storage::disk('public')->put($fileName, $imageBase64);

        // 4. Ambil array riwayat presensi yang sudah ada di session
        $riwayat = session()->get('riwayat_presensi', []);

        // 5. Masukkan data baru ke array (simpan PATH file, BUKAN string base64)
        $riwayat[] = [
            'id' => count($riwayat) + 1,
            'nama' => $request->nama,
            'foto' => $fileName,
            // Gunakan now() agar mengikuti timezone config/app.php
            'waktu' => now()->format('Y-m-d H:i:s'), 
        ];

        session()->put('riwayat_presensi', $riwayat);

        return redirect()->back()->with('success', 'Presensi berhasil disimpan!');
    }
}