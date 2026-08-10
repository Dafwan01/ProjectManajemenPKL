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

        /* Semua posisi pakai satuan % dari tinggi halaman, supaya
           tetap presisi mengikuti garis pada gambar background,
           berapa pun ukuran render-nya. Sesuaikan lagi nilai top
           jika garis di background kamu geser sedikit. */

        /* ========================================================
           0. NOMOR SERTIFIKAT - di bawah kata "MAGANG"
           ======================================================== */
        .section-nomor {
            position: absolute;
            top: 32.4%;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nomor-sertifikat {
            font-size: 13px;
            color: #64748b;
            letter-spacing: 1px;
        }

        /* ========================================================
           1. NAMA PESERTA - ditulis PERSIS DI ATAS garis pertama
           ======================================================== */
        .section-nama {
            position: absolute;
            top: 40%;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nama-peserta {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            color: #1e3a8a;
            line-height: 1.1;
        }

        /* NIM/NIS - Asal Sekolah, tepat DI BAWAH garis pertama */
        .section-nim {
            position: absolute;
            top: 52.5%;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nim-asal {
            font-size: 14px;
            color: #475569;
        }

        /* ========================================================
           2. PARAGRAF PARTISIPASI
           ======================================================== */
        .section-paragraf {
            position: absolute;
            top: 55%;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 0;
        }

        .paragraf {
            font-size: 15px;
            line-height: 1.5;
            color: #1b263b;
        }

        /* ========================================================
           3. PROYEK AKHIR + MENTOR + KOTA/TANGGAL
           ======================================================== */
        .section-proyek {
            position: absolute;
            top: 64%;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .proyek-label {
            font-size: 14px;
            color: #1b263b;
            margin-bottom: 2px;
        }

        .proyek-judul {
            font-size: 15px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 2px;
        }

        .proyek-mentor {
            font-size: 14px;
            color: #1b263b;
            margin-bottom: 2px;
        }

        .kota-tanggal {
            font-size: 13px;
            color: #475569;
            margin-top: 4px;
        }

        /* ========================================================
           4. TANDA TANGAN - nama DI ATAS garis kedua, NIP di bawahnya
           ======================================================== */
        .section-nama-ttd {
            position: absolute;
            top: 88%;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nama-penandatangan {
            font-size: 16px;
            font-weight: bold;
            color: #0d1b2a;
        }

        .section-nip {
            position: absolute;
            top: 91.5%;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .nip {
            font-size: 13px;
            color: #1b263b;
        }

        .badge-ttd-elektronik {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    @php
        $imagePath = public_path('images/template-sertifikat-new.png');
        if (!file_exists($imagePath)) {
            $imagePath = public_path('images/template-sertifikat-new.jpg');
        }
    @endphp

    <img src="{{ $imagePath }}" class="bg-template">

    <!-- BLOCK 0: NOMOR SERTIFIKAT -->
    <div class="section-nomor">
        <div class="nomor-sertifikat">
            No. {{ $nomorSertifikat ?? 'SERT/2026/0001' }}
        </div>
    </div>

    <!-- BLOCK 1: NAMA PESERTA (di atas garis pertama) -->
    <div class="section-nama">
        <div class="nama-peserta">
            {{ ucwords(strtolower($user->nama)) }}
        </div>
    </div>

    <!-- NIM/NIS - Asal Sekolah (di bawah garis pertama) -->
    <div class="section-nim">
        <div class="nim-asal">
            {{ $user->sekolah->nama_sekolah ?? '-' }}
        </div>
    </div>

    <!-- BLOCK 2: PARAGRAF PARTISIPASI -->
    <div class="section-paragraf">
        <div class="paragraf">
            Atas partisipasi dan kinerjanya yang baik selama mengikuti program magang/praktik kerja lapangan <br>
            di <strong>{{ $bidangUnitKerja ?? 'Dinas Komunikasi dan Informatika (Diskominfo) Kota Bogor' }}</strong>, <br>
            yang dilaksanakan pada tanggal
            <strong>{{ \Carbon\Carbon::parse($tanggalMulai ?? now())->isoFormat('D MMMM Y') }}</strong>
            sampai dengan
            <strong>{{ \Carbon\Carbon::parse($tanggalSelesai ?? now())->isoFormat('D MMMM Y') }}</strong>.
        </div>
    </div>

    <!-- BLOCK 3: PROYEK AKHIR, MENTOR, KOTA & TANGGAL TERBIT -->
    <div class="section-proyek">
        <div class="proyek-label">Dengan proyek akhir:</div>
        <div class="proyek-judul">&ldquo;{{ $user->project?->nama_project ?? '-' }}&rdquo;</div>
        <div class="proyek-mentor">Di bawah bimbingan mentor: <strong>{{ $user->mentor ?? '-' }}</strong></div>
        <div class="kota-tanggal">
            {{ $kota ?? 'Bogor' }}, {{ \Carbon\Carbon::parse($tanggalTerbit ?? now())->isoFormat('D MMMM Y') }}
        </div>
    </div>

    <!-- BLOCK 4: TANDA TANGAN -->
    <div class="section-nama-ttd">
        <div class="nama-penandatangan">{{ $namaPenandatangan }}</div>
    </div>

    <div class="section-nip">
        <div class="nip">NIP : {{ $nipPenandatangan ?? '-' }}</div>
        @if($jenisTtd === 'elektronik')
            <div class="badge-ttd-elektronik">Ditandatangani secara elektronik</div>
        @endif
    </div>

</body>
</html>