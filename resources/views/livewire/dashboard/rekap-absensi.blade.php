<div>
    <!-- CSS Print Engine -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #area-cetak-individual, #area-cetak-individual * {
                visibility: visible;
            }

            #area-cetak-individual {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: #ffffff !important;
                color: #000000 !important;
            }

            .no-print {
                display: none !important;
            }

            table.table-cetak {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 11pt !important;
                margin-top: 15px;
            }

            table.table-cetak th, 
            table.table-cetak td {
                border: 1px solid #333 !important;
                padding: 6px 8px !important;
                color: #000 !important;
            }

            table.table-cetak th {
                background-color: #f2f2f2 !important;
                font-weight: bold !important;
                text-transform: uppercase;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        .print-only {
            display: none;
        }

        @media print {
            .print-only {
                display: block;
            }
        }
    </style>

    <!-- Header Dashboard (Sembunyi saat print) -->
    <div class="no-print mb-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rekap Absensi Peserta PKL</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih periode dan klik tombol rekap pada siswa yang bersangkutan.</p>
        </div>
    </div>

    <!-- Container Utama -->
    <div class="no-print relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
        
        <!-- Filter Periode (Sembunyi saat print) -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Periode Rekap:</span>
            
            <!-- Dropdown Bulan -->
            <select 
                wire:model.live="bulan"
                class="p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-36 bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
                <option value="01">Januari</option>
                <option value="02">Februari</option>
                <option value="03">Maret</option>
                <option value="04">April</option>
                <option value="05">Mei</option>
                <option value="06">Juni</option>
                <option value="07">Juli</option>
                <option value="08">Agustus</option>
                <option value="09">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </select>

            <!-- Dropdown Tahun -->
            <select 
                wire:model.live="tahun"
                class="p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-28 bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

        <!-- Tabel List Anak PKL -->
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center" style="width: 5%;">No</th>
                    <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                    <th scope="col" class="px-6 py-3">Asal Sekolah</th>
                    <th scope="col" class="px-6 py-3 text-center" style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usersPKL as $index => $user)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <!-- No -->
                        <td class="px-6 py-4 text-center font-medium">{{ $index + 1 }}</td>

                        <!-- Nama -->
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $user->nama ?? $user->name ?? '-' }}
                        </td>

                        <!-- Asal Sekolah -->
                        <td class="px-6 py-4">
                            {{ $user->asal_sekolah ?? '-' }}
                        </td>

                        <!-- Tombol Aksi Rekap -->
                        <td class="px-6 py-4 text-center">
                     <!-- BENAR ✅ -->
<!-- PASTIKAN SEPERTI INI: -->
<a 
    href="{{ route('cetak.rekap-absensi', ['userId' => $user->user_id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
    target="_blank"
    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-sm"
>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
    </svg>
    <span>Rekap PDF</span>
</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data peserta PKL terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>