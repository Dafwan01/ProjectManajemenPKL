<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat {{ $user->nama }}</title>
    <style>
        /* Load Font Kaligrafi secara Lokal */
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
        
        * {
            box-sizing: border-box;
        }

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
           0. NOMOR SERTIFIKAT (DI ATAS DIBERIKAN KEPADA)
           ======================================================== */
        .section-nomor {
            position: absolute;
            top: 322px; /* Disesuaikan agar berada persis di bawah kata MAGANG */
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nomor-sertifikat {
            font-size: 12px;
            color: #1e3a8a;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* ========================================================
           1. NAMA PESERTA
           ======================================================== */
        .section-nama {
            position: absolute;
            top: 360px;
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
            top: 485px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .keterangan {
            font-size: 18px;
            color: #1b263b;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .proyek-container {
            margin-top: 10px;
            font-size: 16px;
            color: #334155;
        }

        .tanggal {
            font-size: 14px;
            color: #475569;
            font-weight: 500;
            margin-top: 15px;
        }

        /* ========================================================
           3. AREA TANDA TANGAN / PENANDATANGAN
           ======================================================== */
        .section-ttd {
            position: absolute;
            bottom: 40px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .jabatan {
            font-size: 16px;
            font-weight: bold;
            color: #0d1b2a;
            margin-bottom: 10px;
        }

        .section-mentor {
            position: absolute;
            top: 708px; 
            right: 198px;
            width: 320px;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #0d1b2a;
            text-decoration: underline;
            margin-top: 5px;
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
            
            <div style="margin-top: 10px; font-size: 18px;">
                dengan proyek akhir berjudul <strong>"{{ $user->project?->nama_project ?? '-' }}"</strong>
                Dengan Mentor: <strong>{{ $user->mentor ?? '-' }}</strong>
            </div>
        </div>

        <div class="tanggal">
            Diterbitkan pada: {{ \Carbon\Carbon::parse($tanggalTerbit ?? now())->isoFormat('D MMMM Y') }}
        </div>
    </div>

    <!-- BLOCK 3: TANDA TANGAN -->
    <div class="section-ttd">
        <!-- 1. Panggil Jabatan Penandatangan -->
        <div class="jabatan">{{ $jabatanPenandatangan }}</div>

        <div class="box-ttd">
            <!-- 2. Pengecekan Jenis TTD Elektronik / Non-Elektronik -->
            @if($jenisTtd === 'elektronik')
                <div class="badge-ttd-elektronik">
                    Ditandatangani secara elektronik oleh<br>
                    <strong>{{ $namaPenandatangan }}</strong>
                </div>
            @endif
            <!-- Jika 'non_elektronik', area ini dibiarkan kosong untuk TTD basah -->
        </div>

        <!-- 3. Panggil Nama Penandatangan -->
        <div class="nama-penandatangan">{{ $namaPenandatangan }}</div>
    </div>

</body>
</html>