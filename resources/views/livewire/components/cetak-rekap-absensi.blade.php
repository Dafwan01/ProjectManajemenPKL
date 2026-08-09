<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Bulan {{ $namaBulan }}</title>
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
            margin-bottom: 15px;
            border-collapse: collapse;
            font-size: 10pt;
            font-weight: bold;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
        }

        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>LAPORAN PRESENSI BULAN {{ strtoupper($namaBulan) }}</h3>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 12%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>{{ $selectedUser->nama ?? $selectedUser->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>Peserta PKL - {{ $selectedUser->sekolah?->nama_sekolah ?? '-' }}</td>
        </tr>
        <tr>
            <td>Bidang</td>
            <td>:</td>
            <td>{{ $selectedUser->divisi?->bidang?->nama_bidang ?? '-' }}</td>
        </tr>
        <tr>
            <td>Divisi</td>
            <td>:</td>
            <td>{{ $selectedUser->divisi?->nama_divisi ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">Hari</th>
                <th style="width: 13%;">Tanggal</th>
                <th style="width: 12%;">Jam Masuk</th>
                <th style="width: 12%;">Jam Keluar</th>
                <th style="width: 18%;">Total Kerja</th>
                <th style="width: 30%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presensisUser as $idx => $item)
                @php
                    $totalKerja = '-';
                    if ($item->absen_masuk && $item->absen_keluar) {
                        $masuk = \Carbon\Carbon::parse($item->absen_masuk);
                        $keluar = \Carbon\Carbon::parse($item->absen_keluar);
                        $diff = $masuk->diff($keluar);
                        $totalKerja = $diff->h . ' jam ' . $diff->i . ' menit';
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('l') }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('j M Y') }}</td>
                    <td style="text-align: center;">{{ $item->absen_masuk ? substr($item->absen_masuk, 0, 5) : '-' }}</td>
                    <td style="text-align: center;">{{ $item->absen_keluar ? substr($item->absen_keluar, 0, 5) : '-' }}</td>
                    <td style="text-align: center;">{{ $totalKerja }}</td>
                    <td>{{ $item->logBooks->first()?->kegiatan ?? $item->status_kehadiran?->value ?? $item->status_kehadiran ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">
                        Tidak ada data absensi untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>