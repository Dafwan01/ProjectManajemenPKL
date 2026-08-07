<div class="w-full mx-auto max-w-4xl">

    <!-- Header Judul -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-wide">Jadwal Magang</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar jadwal kerja mingguan Anda selama masa magang (Senin s.d. Jumat).</p>
    </div>

    <!-- Kotak Informasi Hari Ini -->
    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50 text-blue-700 dark:text-blue-300 text-sm rounded-2xl flex items-center gap-3 shadow-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>Hari ini adalah hari <strong>{{ $namaHariIni }}</strong>. Baris jadwal hari ini ditandai dengan sorotan warna biru pada tabel di bawah.</span>
    </div>

    <!-- Kartu Tabel Jadwal -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Hari</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Jam Masuk</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Jam Pulang</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Status Kerja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60">
                    @forelse($jadwalMingguan as $jadwal)
                        <tr class="transition {{ $jadwal['is_hari_ini'] ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-white dark:bg-gray-900 hover:bg-gray-50/80 dark:hover:bg-gray-800/40' }}">
                            <!-- Kolom Hari -->
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    {{ $jadwal['hari'] }}
                                    @if($jadwal['is_hari_ini'])
                                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-600 text-white uppercase tracking-wide">
                                            Hari Ini
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Jam Masuk -->
                            <td class="px-6 py-4 text-center">
                                @if($jadwal['jam_masuk'])
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        {{ substr($jadwal['jam_masuk'], 0, 5) }} WIB
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-600">-</span>
                                @endif
                            </td>

                            <!-- Jam Pulang -->
                            <td class="px-6 py-4 text-center">
                                @if($jadwal['jam_keluar'])
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                        {{ substr($jadwal['jam_keluar'], 0, 5) }} WIB
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-600">-</span>
                                @endif
                            </td>

                            <!-- Status Kerja (WFO / WFH) -->
                            <td class="px-6 py-4 text-center">
                                @if($jadwal['status_kerja'])
                                    @php
                                        $isWfh = strtolower($jadwal['status_kerja']) === 'wfh';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full uppercase tracking-wide border
                                        {{ $isWfh 
                                            ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20' 
                                            : 'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-500/20' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isWfh ? 'bg-blue-500 dark:bg-blue-400' : 'bg-purple-500 dark:bg-purple-400' }}"></span>
                                        {{ strtoupper($jadwal['status_kerja']) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
                                Belum ada jadwal kerja yang ditetapkan untuk Anda. Silakan hubungi Pembimbing atau Administrator.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Keterangan Status Kerja -->
    <div class="mt-4 flex flex-wrap items-center gap-6 text-xs text-gray-500 dark:text-gray-400">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
            <span><strong>WFO (Work From Office):</strong> Wajib melakukan presensi di lokasi kantor sesuai radius yang ditentukan.</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
            <span><strong>WFH (Work From Home):</strong> Dapat melakukan presensi dari mana saja tanpa batasan lokasi.</span>
        </div>
    </div>

</div>
