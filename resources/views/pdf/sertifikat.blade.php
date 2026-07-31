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
           1. NAMA PESERTA
           ======================================================== */
        .section-nama {
            position: absolute;
            top: 375px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nama-peserta {
            font-family: 'Great Vibes', cursive;
            font-size: 58px;
            color: #1e3a8a;
            line-height: 1.1;
        }

        /* ========================================================
           2. DETAIL TULISAN DI BAWAH GARIS UTAMA
           ======================================================== */
        .section-keterangan {
            position: absolute;
            top: 505px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nomor-sertifikat {
            font-size: 18px;
            color: #1e3a8a;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .keterangan {
            font-size: 22px;
            color: #1b263b;
            line-height: 1.4;
            margin-bottom: 12px;
        }

        .sekolah {
            font-weight: bold;
            color: #0d1b2a;
        }

        .tanggal {
            font-size: 16px;
            color: #334155;
            font-weight: 500;
        }

        /* ========================================================
           3. NAMA PEMBIMBING & MENTOR (PRESISI DI TENGAH GARIS)
           ======================================================== */
        .section-pembimbing {
            position: absolute;
            top: 708px; 
            left: 145px;
            width: 320px;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #0d1b2a;
        }

        .section-mentor {
            position: absolute;
            top: 708px; 
            right: 198px; /* Pas persis di tengah-tengah garis & kata Mentor */
            width: 320px;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #0d1b2a;
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

    <!-- BLOCK 1: NAMA PESERTA -->
    <div class="section-nama">
        <div class="nama-peserta">
            {{ ucwords(strtolower($user->nama)) }}
        </div>
    </div>

    <!-- BLOCK 2: TULISAN & KETERANGAN -->
    <div class="section-keterangan">
        <div class="nomor-sertifikat">
            No. {{ $nomorSertifikat ?? 'SERT/2026/0001' }}
        </div>

        <div class="keterangan">
            Telah menyelesaikan Program Magang / PKL
            @if(!empty($user->asal_sekolah))
                dari <span class="sekolah">{{ $user->asal_sekolah }}</span>
            @endif
        </div>

        <div class="tanggal">
            Diterbitkan pada: {{ \Carbon\Carbon::parse($tanggalTerbit ?? now())->isoFormat('D MMMM Y') }}
        </div>
    </div>

    <!-- BLOCK 3: NAMA PEMBIMBING (KIRI) -->
    <div class="section-pembimbing">
        {{ $user->pembimbing->nama ?? $pembimbingNama ?? '' }}
    </div>

    <!-- BLOCK 4: NAMA MENTOR (KANAN) -->
    <div class="section-mentor">
        {{ $user->mentor->nama ?? $mentorNama ?? $user->mentor_nama ?? 'Bambang Sutrisno' }}
    </div>

</body>
</html>