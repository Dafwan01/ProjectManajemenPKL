<div>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 16pt; }
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 3px 0; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #333; padding: 6px 8px; }
        .data-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 10pt; }
        @media print { .no-print { display: none !important; } }
    </style>

    <!-- Tombol Operasi -->
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Cetak / Save PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6b7280; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 8px;">
            Tutup Tab
        </button>
    </div>

    <!-- Header Laporan -->
    <div class="header">
        <h2>Laporan Rekapitulasi Absensi Peserta PKL</h2>
        <p>Periode: {{ $namaBulan }}</p>
    </div>

    <!-- Informasi Siswa -->
    <table class="info-table">
        <tr>
            <td style="width: 15%;"><strong>Nama</strong></td>
            <td style="width: 2%;">:</td>
            <td>{{ $selectedUser->nama ?? $selectedUser->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Asal Sekolah</strong></td>
            <td>:</td>
            <td>{{ $selectedUser->asal_sekolah ?? '-' }}</td>
        </tr>
    </table>

    <!-- Tabel Rekap Presensi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%; text-align: center;">Kehadiran</th>
                <th style="width: 12%; text-align: center;">Jam Masuk</th>
                <th style="width: 12%; text-align: center;">Jam Keluar</th>
                <th style="width: 41%;">Logbook Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presensisUser as $idx => $item)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d/m/Y') }}</td>
                    <td style="text-align: center; text-transform: capitalize;">
                        {{ $item->status_kehadiran?->value ?? $item->status_kehadiran ?? '-' }}
                    </td>
                    <td style="text-align: center;">{{ $item->absen_masuk ? substr($item->absen_masuk, 0, 5) : '-' }}</td>
                    <td style="text-align: center;">{{ $item->absen_keluar ? substr($item->absen_keluar, 0, 5) : '-' }}</td>
                    <td>{{ $item->logBooks->first()?->kegiatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 15px;">
                        Tidak ada data absensi untuk peserta ini pada periode {{ $namaBulan }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</div>