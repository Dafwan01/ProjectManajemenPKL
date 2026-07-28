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

        .signature-section {
            margin-top: 30px;
            width: 100%;
            float: right;
        }

        .signature-box {
            float: right;
            width: 220px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>LAPORAN REKAPITULASI NILAI PESERTA</h3>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 18%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>{{ $selectedUser->nama ?? $selectedUser->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Asal Sekolah</td>
            <td>:</td>
            <td>{{ $selectedUser->asal_sekolah ?? '-' }}</td>
        </tr>
        <tr>
            <td>Divisi / Bidang</td>
            <td>:</td>
            <td>{{ $selectedUser->divisi?->value ?? $selectedUser->divisi ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">NO</th>
                <th style="width: 60%;">ASPEK PENILAIAN</th>
                <th style="width: 32%;">NILAI (0-100)</th>
            </tr>
        </thead>
        <tbody>
            @if($nilaiUser)
                <tr>
                    <td class="text-center">1</td>
                    <td>Kedisiplinan dan Profesionalisme (Integritas Work Ethic)</td>
                    <td class="text-center">{{ $nilaiUser->kedisiplinan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>Kemampuan Teknis dan Implementasi Tugas (Hard Skills)</td>
                    <td class="text-center">{{ $nilaiUser->kemampuan_teknis ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>Kemampuan Logika Pemecahan Masalah (Problem Solving)</td>
                    <td class="text-center">{{ $nilaiUser->problem_solving ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">4</td>
                    <td>Komunikasi dan Kerja Sama Tim (Soft Skills)</td>
                    <td class="text-center">{{ $nilaiUser->komunikasi_kerjasama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">5</td>
                    <td>Kualitas dan Ketepatan Waktu Output Kerja (Deliverables)</td>
                    <td class="text-center">{{ $nilaiUser->kualitas_ketepatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">Rata-Rata Nilai:</td>
                    <td class="text-center" style="font-weight: bold;">{{ $nilaiUser->rata_rata ?? '-' }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="3" class="text-center" style="padding: 15px;">
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

    <div class="signature-section">
        <div class="signature-box">
            <p>Bogor, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y') }}</p>
            <p>Pembimbing / Penilai,</p>
            <br><br><br>
            <p><strong>( _____________________ )</strong></p>
        </div>
    </div>

</body>
</html>