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
    Telah menyelesaikan Program Magang / PKL di <br>
    <strong>Dinas Komunikasi dan Informatika (Diskominfo) Kota Bogor</strong>
    @if(!empty($user->sekolah?->nama_sekolah))
        <br>dari <span class="sekolah">{{ $user->sekolah->nama_sekolah }}</span>
    @elseif(!empty($user->asal_sekolah))
        <br>dari <span class="sekolah">{{ $user->asal_sekolah }}</span>
    @endif
    
    <div style="margin-top: 10px; font-size: 18px;">
        dengan proyek akhir berjudul <strong>"{{ $user->project?->nama_project ?? '-' }}"</strong><br>
        di bawah bimbingan <strong>{{ $user->mentor ?? '-' }}</strong>
    </div>
</div>

        <div class="tanggal">
            Diterbitkan pada: {{ \Carbon\Carbon::parse($tanggalTerbit ?? now())->isoFormat('D MMMM Y') }}
        </div>
    </div>

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

</body>
</html>