<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Nilai Peserta</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h3 {
            margin: 0;
            text-transform: uppercase;
            font-size: 13pt;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            font-size: 10pt;
            font-weight: bold;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9.5pt;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .catatan-box {
            margin-top: 15px;
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 9pt;
        }

        .keterangan-box {
            margin-top: 10px;
            font-size: 8.5pt;
            border: 1px solid #000;
            padding: 8px 10px;
        }

        .keterangan-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .keterangan-box td {
            padding: 2px 8px 2px 0;
        }

        .date-section {
            margin-top: 25px;
            width: 100%;
            float: right;
        }

        .date-box {
            float: right;
            width: 220px;
            text-align: center;
            font-size: 9.5pt;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>LAPORAN REKAPITULASI NILAI PESERTA</h3>
    </div>

    <p style="text-align: center; font-size: 8.5pt; font-style: italic; margin: -12px 0 20px 0;">
       Dokumen ini merupakan lampiran resmi yang tidak dapat dipisahkan dari Sertifikat Magang, dan menjadi bagian sah dari keseluruhan dokumen penilaian peserta didik/mahasiswa selama masa Praktik Kerja Lapangan (PKL) yang bersangkutan.
    </p>

    @php
        $namaSekolah = $selectedUser->sekolah?->nama_sekolah
            ?? \App\Models\Sekolah::find($selectedUser->sekolah_id)?->nama_sekolah
            ?? '-';

        $divisiBidangText = collect([$namaDivisi, $namaBidang])
            ->filter()
            ->implode(' / ') ?: '-';
    @endphp

    <table class="info-table">
        <tr>
            <td style="width: 18%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>{{ $selectedUser->nama ?? $selectedUser->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Asal Sekolah</td>
            <td>:</td>
            <td>{{ $namaSekolah }}</td>
        </tr>
        <tr>
            <td>Divisi / Bidang</td>
            <td>:</td>
            <td>{{ $divisiBidangText }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">NO</th>
                <th style="width: 48%;">ASPEK PENILAIAN</th>
                <th style="width: 18%;">NILAI (0-100)</th>
                <th style="width: 28%;">PREDIKAT</th>
            </tr>
        </thead>
        <tbody>
            @if($nilaiUser)
                <tr>
                    <td class="text-center">1</td>
                    <td>Kedisiplinan dan Profesionalisme (Integritas Work Ethic)</td>
                    <td class="text-center">{{ $nilaiUser->kedisiplinan ?? '-' }}</td>
                    <td class="text-center">{{ $predikatPerAspek['kedisiplinan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>Kemampuan Teknis dan Implementasi Tugas (Hard Skills)</td>
                    <td class="text-center">{{ $nilaiUser->kemampuan_teknis ?? '-' }}</td>
                    <td class="text-center">{{ $predikatPerAspek['kemampuan_teknis'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>Kemampuan Logika Pemecahan Masalah (Problem Solving)</td>
                    <td class="text-center">{{ $nilaiUser->problem_solving ?? '-' }}</td>
                    <td class="text-center">{{ $predikatPerAspek['problem_solving'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">4</td>
                    <td>Komunikasi dan Kerja Sama Tim (Soft Skills)</td>
                    <td class="text-center">{{ $nilaiUser->komunikasi_kerjasama ?? '-' }}</td>
                    <td class="text-center">{{ $predikatPerAspek['komunikasi_kerjasama'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">5</td>
                    <td>Kualitas dan Ketepatan Waktu Output Kerja (Deliverables)</td>
                    <td class="text-center">{{ $nilaiUser->kualitas_ketepatan ?? '-' }}</td>
                    <td class="text-center">{{ $predikatPerAspek['kualitas_ketepatan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">Rata-Rata Nilai:</td>
                    <td class="text-center" style="font-weight: bold;">{{ $nilaiUser->rata_rata ?? '-' }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $predikat ?? '-' }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" class="text-center" style="padding: 15px;">
                        Belum ada data nilai untuk peserta ini.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>


    @if($nilaiUser && !empty($nilaiUser->catatan))
        <div class="catatan-box">
            <strong>Catatan Evaluasi:</strong><br>
            <p style="margin: 4px 0 0 0;">{{ $nilaiUser->catatan }}</p>
        </div>
    @endif

    <div class="date-section">
        <div class="date-box">
            <p style="margin: 0;">Bogor, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y') }}</p>
        </div>
    </div>

</body>
</html>