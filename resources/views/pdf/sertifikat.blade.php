<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat {{ $user->nama }}</title>
    <style>
        @font-face {
            font-family: 'Great Vibes';
            src: url("{{ public_path('fonts/GreatVibes-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 297mm;
            height: 210mm;
            position: relative;
        }

        .bg-template {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1000;
        }

        /* ========================================================
           0. NOMOR SERTIFIKAT
           Tepat di tengah, di bawah kata "MAGANG" pada background.
           ======================================================== */
        .section-nomor {
            position: absolute;
            top: 300px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nomor-sertifikat {
            font-size: 13px;
            color: #1e3a8a;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* ========================================================
           1. NAMA PESERTA
           Label "Diberikan kepada :" TIDAK ditulis lagi di sini
           karena sudah ada pada gambar template. Garis di bawah
           nama juga sudah ada pada template, jadi tidak dibuat ulang.
           ======================================================== */
        .section-nama {
            position: absolute;
            top: 368px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nama-peserta {
            font-family: 'Great Vibes', cursive;
            font-size: 56px;
            color: #1e3a8a;
            line-height: 1.1;
        }

        /* ========================================================
           2. DETAIL TULISAN DI BAWAH GARIS UTAMA
           ======================================================== */
        .section-keterangan {
            position: absolute;
            top: 460px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .keterangan {
            width: 100%;
            font-size: 16px;
            color: #1b263b;
            line-height: 1.4;
            margin-bottom: 0;
            text-align: center;
        }

        .proyek-container {
            width: 100%;
            margin-top: 4px;
            font-size: 15px;
            line-height: 1.4;
            color: #334155;
            text-align: center;
        }

        .tanggal {
            font-size: 13px;
            color: #475569;
            font-weight: 500;
            margin-top: 12px;
        }

        /* ========================================================
           3. AREA TANDA TANGAN
           Urutan dari atas ke bawah: NAMA -> GARIS -> JABATAN
           (dompdf tidak mendukung flexbox dengan baik, jadi
           dibuat dengan block/absolute biasa)
           ======================================================== */
        .section-ttd {
            position: absolute;
            bottom: 50px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .garis-ttd {
            width: 220px;
            margin: 0 auto 10px;
            border-top: 1.5px solid #0d1b2a;
        }

        .badge-ttd-elektronik {
            font-size: 11px;
            color: #334155;
            line-height: 1.4;
            margin-top: 6px;
        }

        .nama-penandatangan {
            font-size: 22px;
            font-weight: normal;
            color: #0d1b2a;
            margin-top: 20px;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .jabatan {
            font-size: 22px;
            font-weight: bold;
            color: #0d1b2a;
            line-height: 1.2;
        }

    </style>
</head>
<body>

    @php
        $imagePath = public_path('images/template-sertifikat.png');
        if (!file_exists($imagePath)) {
            $imagePath = public_path('images/template-sertifikat.jpg');
        }
    @endphp

    <img src="{{ $imagePath }}" class="bg-template">

    <!-- BLOCK 0: NOMOR SERTIFIKAT -->
    <div class="section-nomor">
        <div class="nomor-sertifikat">
            No. {{ $nomorSertifikat ?? 'SERT/2026/0001' }}
        </div>
    </div>

    <!-- BLOCK 1: NAMA PESERTA -->
    <div class="section-nama">
        <div class="nama-peserta">
            {{ ucwords(strtolower($user->nama)) }}
        </div>
    </div>

    <!-- BLOCK 2: TULISAN & KETERANGAN -->
    <div class="section-keterangan">
        <div class="keterangan">
            Telah menyelesaikan Program Magang / PKL di <br>
            <strong>Dinas Komunikasi dan Informatika (Diskominfo) Kota Bogor</strong>

            <div class="proyek-container">
                dengan proyek akhir berjudul <strong>"{{ $user->project?->nama_project ?? '-' }}"</strong> Dengan Mentor: <strong>{{ $user->mentor ?? '-' }}</strong>
            </div>
        </div>

        <div class="tanggal">
            Diterbitkan pada: {{ \Carbon\Carbon::parse($tanggalTerbit ?? now())->isoFormat('D MMMM Y') }}
        </div>

        @if($jenisTtd === 'elektronik')
            <div class="badge-ttd-elektronik">
                Ditandatangani secara elektronik oleh
            </div>
        @endif
    </div>

    <!-- BLOCK 3: TANDA TANGAN (nama di atas, jabatan di bawah, sejajar) -->
    <div class="section-ttd">
        <div class="nama-penandatangan">{{ $namaPenandatangan }}</div>
        <div class="jabatan">{{ $jabatanPenandatangan ?? '-' }}</div>
    </div>

</body>
</html>