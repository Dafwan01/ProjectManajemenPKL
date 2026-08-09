<div class="w-full mx-auto max-w-4xl space-y-6">

    <!-- Header Judul Ringkas -->
    <div class="pb-3 border-b border-slate-200 dark:border-slate-800">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-wide">JADWAL MAGANG</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar jadwal kerja mingguan Anda selama masa magang (Senin s.d. Jumat).</p>
    </div>

    <!-- Kotak Informasi Hari Ini -->
    <div class="p-3 sm:p-4 bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800/80 text-blue-700 dark:text-blue-300 text-xs rounded-2xl flex items-center gap-3 shadow-sm">
        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="leading-relaxed">Hari ini adalah hari <strong class="font-bold underline decoration-blue-400 decoration-2 underline-offset-2">{{ $namaHariIni }}</strong>. Baris jadwal hari ini ditandai dengan sorotan warna biru.</span>
    </div>

    <!-- 1. MOBILE & TABLET VIEW: Card Grid (Tampil di Layar < lg) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:hidden">
        @forelse($jadwalMingguan as $jadwal)
            @php
                $isToday = $jadwal['is_hari_ini'] ?? false;
                $isWfh = strtolower($jadwal['status_kerja'] ?? '') === 'wfh';
            @endphp
            <div class="p-4 rounded-2xl border transition shadow-sm flex flex-col justify-between gap-3 {{ $isToday ? 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-300 dark:border-blue-800/80 ring-1 ring-blue-400/30' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800' }}">
                
                <!-- Header Card: Hari & Badge Status Hari Ini -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800/60">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $jadwal['hari'] }}</span>
                        @if($isToday)
                            <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-md bg-blue-600 text-white uppercase tracking-wider">
                                Hari Ini
                            </span>
                        @endif
                    </div>

                    <!-- Status Kerja (WFO/WFH) Badge -->
                    @if($jadwal['status_kerja'])
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-bold rounded-lg uppercase tracking-wide border {{ $isWfh ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800/60' : 'bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isWfh ? 'bg-blue-500' : 'bg-purple-500' }}"></span>
                            {{ strtoupper($jadwal['status_kerja']) }}
                        </span>
                    @else
                        <span class="text-[10px] text-slate-400 dark:text-slate-600 font-medium">-</span>
                    @endif
                </div>

                <!-- Detail Jam Masuk & Pulang -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-slate-50 dark:bg-slate-950/50 p-2 rounded-xl border border-slate-100 dark:border-slate-800/50">
                        <span class="block text-[10px] text-slate-400 dark:text-slate-500 mb-0.5 font-medium">Jam Masuk</span>
                        @if($jadwal['jam_masuk'])
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ substr($jadwal['jam_masuk'], 0, 5) }} WIB
                            </span>
                        @else
                            <span class="text-slate-400 dark:text-slate-600">-</span>
                        @endif
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-950/50 p-2 rounded-xl border border-slate-100 dark:border-slate-800/50">
                        <span class="block text-[10px] text-slate-400 dark:text-slate-500 mb-0.5 font-medium">Jam Pulang</span>
                        @if($jadwal['jam_keluar'])
                            <span class="font-semibold text-amber-600 dark:text-amber-400">
                                {{ substr($jadwal['jam_keluar'], 0, 5) }} WIB
                            </span>
                        @else
                            <span class="text-slate-400 dark:text-slate-600">-</span>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center text-slate-400 text-xs">
                Belum ada jadwal kerja yang ditetapkan untuk Anda. Silakan hubungi Pembimbing atau Administrator.
            </div>
        @endforelse
    </div>

    <!-- 2. DESKTOP VIEW: Table (Tampil di Layar >= lg) -->
    <div class="hidden lg:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
                <thead class="text-[10px] uppercase bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 border-b border-slate-200 dark:border-slate-800 tracking-wider">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-bold">Hari</th>
                        <th scope="col" class="px-5 py-3.5 font-bold text-center">Jam Masuk</th>
                        <th scope="col" class="px-5 py-3.5 font-bold text-center">Jam Pulang</th>
                        <th scope="col" class="px-5 py-3.5 font-bold text-center">Status Kerja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($jadwalMingguan as $jadwal)
                        @php
                            $isToday = $jadwal['is_hari_ini'] ?? false;
                        @endphp
                        <tr class="transition {{ $isToday ? 'bg-blue-50/60 dark:bg-blue-950/40 font-medium' : 'bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            <!-- Kolom Hari -->
                            <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span>{{ $jadwal['hari'] }}</span>
                                    @if($isToday)
                                        <span class="inline-flex items-center px-2 py-0.5 text-[9px] font-extrabold rounded-md bg-blue-600 text-white uppercase tracking-wider">
                                            Hari Ini
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Jam Masuk -->
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                @if($jadwal['jam_masuk'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                                        {{ substr($jadwal['jam_masuk'], 0, 5) }} WIB
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-600">-</span>
                                @endif
                            </td>

                            <!-- Jam Pulang -->
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                @if($jadwal['jam_keluar'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
                                        {{ substr($jadwal['jam_keluar'], 0, 5) }} WIB
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-600">-</span>
                                @endif
                            </td>

                            <!-- Status Kerja (WFO / WFH) -->
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                @if($jadwal['status_kerja'])
                                    @php
                                        $isWfh = strtolower($jadwal['status_kerja']) === 'wfh';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wide border {{ $isWfh ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800/60' : 'bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800/60' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isWfh ? 'bg-blue-500' : 'bg-purple-500' }}"></span>
                                        {{ strtoupper($jadwal['status_kerja']) }}
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500">
                                Belum ada jadwal kerja yang ditetapkan untuk Anda. Silakan hubungi Pembimbing atau Administrator.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Keterangan Status Kerja Footnote -->
    <div class="p-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 text-xs text-slate-500 dark:text-slate-400">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span>
            <span><strong class="text-slate-700 dark:text-slate-300">WFO (Work From Office):</strong> Presensi wajib dilakukan di lokasi kantor.</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
            <span><strong class="text-slate-700 dark:text-slate-300">WFH (Work From Home):</strong> Presensi dapat dilakukan dari lokasi mana saja.</span>
        </div>
    </div>

</div>