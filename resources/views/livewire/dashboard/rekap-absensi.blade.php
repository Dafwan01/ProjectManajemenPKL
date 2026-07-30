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

    <!-- Header Dashboard -->
    <div class="no-print mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-white uppercase">Rekap Absensi Peserta PKL</h1>
        <p class="text-sm text-slate-400 mt-1">Pilih periode dan klik tombol rekap pada siswa yang bersangkutan.</p>
    </div>

    <!-- Container Utama -->
    <div class="no-print relative overflow-hidden bg-[#0d1322] border border-slate-800/80 rounded-2xl shadow-xl">
        
        <!-- Filter Periode -->
        <div class="p-4 border-b border-slate-800/80 bg-[#0d1322] flex items-center gap-3 flex-wrap">
            <span class="text-sm font-medium text-slate-300">Periode Rekap:</span>
            
            <!-- Dropdown Bulan -->
            <select 
                wire:model.live="bulan"
                class="p-2.5 text-sm text-slate-200 border border-slate-700/80 rounded-xl w-36 bg-[#0b0f19] focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
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
                class="p-2.5 text-sm text-slate-200 border border-slate-700/80 rounded-xl w-28 bg-[#0b0f19] focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            >
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

        <!-- Tabel List Anak PKL -->
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm text-slate-300">
                <thead class="text-xs uppercase bg-[#080c14] text-slate-400 border-b border-slate-800/80">
                    <tr>
                        <th scope="col" class="px-4 py-4 w-[8%] text-center">No</th>
                        <th scope="col" class="px-4 py-4 w-[32%] text-center">Nama Lengkap</th>
                        <th scope="col" class="px-4 py-4 w-[32%] text-center">Asal Sekolah</th>
                        <th scope="col" class="px-4 py-4 w-[28%] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($usersPKL as $index => $user)
                        <tr class="bg-[#0d1322] hover:bg-slate-800/40 transition align-middle">
                            <!-- No -->
                            <td class="px-4 py-4 text-center font-medium text-slate-300">{{ $index + 1 }}</td>

                            <!-- Nama -->
                            <td class="px-4 py-4 font-semibold text-white text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->nama ?? $user->name ?? '-' }}</span>
                            </td>

                            <!-- Asal Sekolah -->
                            <td class="px-4 py-4 text-slate-400 text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->asal_sekolah ?? '-' }}</span>
                            </td>

                            <!-- Tombol Aksi Rekap -->
                            <td class="px-4 py-4 text-center">
                                <a 
                                    href="{{ route('cetak.rekap-absensi', ['userId' => $user->user_id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl transition border
                                    text-rose-400 bg-rose-500/10 border-rose-500/20 hover:bg-rose-500/20"
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
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                                Belum ada data peserta PKL terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>