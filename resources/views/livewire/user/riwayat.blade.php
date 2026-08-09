<div>
    <div class="w-full mx-auto max-w-7xl space-y-4 sm:space-y-6">

        <!-- Banner Alert Jika User Sudah Lulus -->
        @if (strtolower(auth()->user()->status->value ?? auth()->user()->status ?? '') === 'lulus')
            <div class="flex items-center gap-3 p-3.5 sm:p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="text-xs sm:text-sm font-medium">
                    <span class="font-bold block sm:inline">Status Anda: Lulus!</span> Riwayat presensi dan Jurnal telah dikunci.
                </div>
            </div>
        @endif

        <!-- Header Judul & Ringkasan Statistik -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center pb-4 border-b border-slate-200 dark:border-slate-800 gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-wide">RIWAYAT PRESENSI</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar lengkap catatan kehadiran, Jurnal harian, dan status pengajuan Anda.</p>
            </div>

            <!-- Ringkasan Statistik Singkat -->
            <div class="grid grid-cols-3 gap-2 sm:gap-3 text-xs w-full lg:w-auto shrink-0">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-2.5 sm:p-3 rounded-2xl text-center shadow-sm">
                    <span class="text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs block mb-0.5">Total Hadir</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold text-base sm:text-xl">{{ $totalHadir }} <span class="text-[10px] font-normal">Hari</span></span>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-2.5 sm:p-3 rounded-2xl text-center shadow-sm">
                    <span class="text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs block mb-0.5">Izin / Sakit</span>
                    <span class="text-amber-600 dark:text-amber-400 font-bold text-base sm:text-xl">{{ $totalIzinSakit }} <span class="text-[10px] font-normal">Hari</span></span>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-2.5 sm:p-3 rounded-2xl text-center shadow-sm">
                    <span class="text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs block mb-0.5">Menunggu</span>
                    <span class="text-blue-600 dark:text-blue-400 font-bold text-base sm:text-xl">{{ $totalMenunggu }} <span class="text-[10px] font-normal">Pengajuan</span></span>
                </div>
            </div>
        </div>

        <!-- Notifikasi Pesan Sistem -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" class="p-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs rounded-xl flex items-center justify-between">
                <span>{{ session('message') }}</span>
                <button type="button" class="text-emerald-600 dark:text-emerald-400 hover:text-slate-900 dark:hover:text-white" @click="show = false">✕</button>
            </div>
        @endif

        <!-- Section Filter -->
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col lg:flex-row gap-3 sm:gap-4 items-stretch lg:items-center justify-between">
            
            <!-- Filter Tanggal -->
            <div class="flex flex-col gap-2 w-full lg:w-auto">
                <div class="grid grid-cols-2 gap-2 items-center w-full lg:flex lg:w-auto">
                    <input 
                        type="date" 
                        wire:model.live="tanggalMulai"
                        class="bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-blue-500 p-2.5 shadow-inner w-full min-w-0 lg:w-40"
                    >
                    <input 
                        type="date" 
                        wire:model.live="tanggalSelesai"
                        class="bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-blue-500 p-2.5 shadow-inner w-full min-w-0 lg:w-40"
                    >
                </div>

                @if($tanggalMulai || $tanggalSelesai)
                    <button 
                        type="button"
                        wire:click="resetFilterTanggal"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-500 whitespace-nowrap self-start"
                    >
                        Atur Ulang
                    </button>
                @endif
            </div>

            <!-- Filter Status -->
            <div class="flex items-center gap-2 w-full lg:w-auto lg:justify-end">
                <label class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap shrink-0">Status:</label>
                <select wire:model.live="filterStatus" class="bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-blue-500 block p-2.5 shadow-inner cursor-pointer w-full lg:w-48">
                    <option value="semua">Semua Status</option>
                    <option value="hadir">HADIR</option>
                    <option value="terlambat">TERLAMBAT</option>
                    <option value="izin">IZIN</option>
                    <option value="sakit">SAKIT</option>
                    <option value="menunggu">Menunggu Persetujuan</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="disetujui">Disetujui</option>
                </select>
            </div>
        </div>

        <!-- 1. MOBIL/TABLET VIEW: Card Grid (Tampil di Layar Kecil - < lg) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:hidden">
            @forelse ($dataRiwayat as $item)
                @php $statusText = strtoupper($item['status']); @endphp
                <div wire:key="card-riwayat-{{ $item['presensi_id'] }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col justify-between gap-3">
                    
                    <!-- Top Section: Tanggal & Badge Status -->
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800/60 pb-3">
                        <div class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $item['tanggal'] }}
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($statusText == 'HADIR')
                                <span class="bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 text-[10px] font-semibold px-2.5 py-1 rounded-lg border border-emerald-300 dark:border-emerald-800/80 inline-flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    HADIR
                                </span>
                            @elseif($statusText == 'TERLAMBAT')
                                <span class="bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 text-[10px] font-semibold px-2.5 py-1 rounded-lg border border-amber-300 dark:border-amber-800/80 inline-flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    TERLAMBAT
                                </span>
                            @elseif($statusText == 'IZIN')
                                <span class="bg-yellow-50 dark:bg-yellow-950/60 text-yellow-600 dark:text-yellow-400 text-[10px] font-semibold px-2.5 py-1 rounded-lg border border-yellow-300 dark:border-yellow-800/80 inline-flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                    IZIN
                                </span>
                            @elseif($statusText == 'SAKIT')
                                <span class="bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 text-[10px] font-semibold px-2.5 py-1 rounded-lg border border-rose-300 dark:border-rose-800/80 inline-flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    SAKIT
                                </span>
                            @elseif(str_starts_with($statusText, 'MENUNGGU'))
                                <span class="bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 text-[10px] font-semibold px-2.5 py-1 rounded-lg border border-blue-300 dark:border-blue-800/80 inline-flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    {{ $statusText }}
                                </span>
                            @else
                                <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold px-2.5 py-1 rounded-lg border border-slate-300 dark:border-slate-700 inline-flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                                    {{ $statusText }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Middle Section: Jam Masuk & Pulang Grid -->
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 dark:bg-slate-950/50 p-2.5 rounded-xl text-xs font-mono">
                        <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span>Msk: {{ $item['jam_masuk'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400 justify-end">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span>Klr: {{ $item['jam_pulang'] ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Logbook Content -->
                    <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50/50 dark:bg-slate-800/30 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="font-semibold text-slate-400 text-[10px] uppercase block mb-1">Catatan Jurnal:</span>
                        {{ $item['logbook'] ?? 'Tidak ada catatan jurnal.' }}
                    </div>

                    <!-- Bottom Action Button -->
                    <div class="pt-1 flex justify-end">
                        @if(strtolower(auth()->user()->status->value ?? auth()->user()->status ?? '') === 'lulus')
                            <button 
                                type="button" 
                                disabled
                                class="w-full opacity-50 cursor-not-allowed text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 py-2 rounded-xl inline-flex items-center justify-center gap-1.5 text-xs font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Terkunci</span>
                            </button>
                        @elseif($item['bisa_edit'] ?? true)
                            <button 
                                type="button" 
                                wire:click="editLogbook({{ $item['presensi_id'] }})"
                                class="w-full text-blue-600 dark:text-blue-400 bg-blue-50/60 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-900/60 py-2 rounded-xl transition inline-flex items-center justify-center gap-1.5 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span>Ubah Jurnal</span>
                            </button>
                        @else
                            <span class="text-[11px] text-slate-400 italic">Menunggu diproses admin</span>
                        @endif
                    </div>

                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center text-slate-400 text-xs">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Data riwayat presensi tidak ditemukan.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP VIEW: Table (Tampil di Layar Besar - >= lg) -->
        <div class="hidden lg:block bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm w-full">
            <div class="relative overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                    <thead class="text-xs text-slate-700 dark:text-slate-200 uppercase bg-slate-100 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 whitespace-nowrap">HARI / TANGGAL</th>
                            <th scope="col" class="px-6 py-3.5 whitespace-nowrap">JAM MASUK / KELUAR</th>
                            <th scope="col" class="px-6 py-3.5 whitespace-nowrap">STATUS</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[200px]">JURNAL / ALASAN</th>
                            <th scope="col" class="px-6 py-3.5 text-center whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($dataRiwayat as $item)
                            <tr wire:key="table-riwayat-row-{{ $item['presensi_id'] }}" class="bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                                    {{ $item['tanggal'] }}
                                </td>

                                <td class="px-6 py-4 font-mono text-xs whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 mb-1">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                        <span>Msk: {{ $item['jam_masuk'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                        <span>Klr: {{ $item['jam_pulang'] ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $statusText = strtoupper($item['status']); @endphp
                                    @if($statusText == 'HADIR')
                                        <span class="bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-emerald-300 dark:border-emerald-800/80 inline-flex items-center gap-1.5">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> HADIR
                                        </span>
                                    @elseif($statusText == 'TERLAMBAT')
                                        <span class="bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-amber-300 dark:border-amber-800/80 inline-flex items-center gap-1.5">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> TERLAMBAT
                                        </span>
                                    @elseif($statusText == 'IZIN')
                                        <span class="bg-yellow-50 dark:bg-yellow-950/60 text-yellow-600 dark:text-yellow-400 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-yellow-300 dark:border-yellow-800/80 inline-flex items-center gap-1.5">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span> IZIN
                                        </span>
                                    @elseif($statusText == 'SAKIT')
                                        <span class="bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-rose-300 dark:border-rose-800/80 inline-flex items-center gap-1.5">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> SAKIT
                                        </span>
                                    @elseif(str_starts_with($statusText, 'MENUNGGU'))
                                        <span class="bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-blue-300 dark:border-blue-800/80 inline-flex items-center gap-1.5">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span> {{ $statusText }}
                                        </span>
                                    @else
                                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-slate-300 dark:border-slate-700 inline-flex items-center gap-1.5">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span> {{ $statusText }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-xs leading-relaxed max-w-sm">
                                    {{ $item['logbook'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if(strtolower(auth()->user()->status->value ?? auth()->user()->status ?? '') === 'lulus')
                                        <button type="button" disabled class="opacity-50 cursor-not-allowed text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 px-2.5 py-1.5 rounded-lg inline-flex items-center gap-1 text-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Terkunci</span>
                                        </button>
                                    @elseif($item['bisa_edit'] ?? true)
                                        <button type="button" wire:click="editLogbook({{ $item['presensi_id'] }})" class="text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 border border-blue-200 dark:border-blue-900/60 px-2.5 py-1.5 rounded-lg transition inline-flex items-center gap-1 text-xs font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            <span>Ubah</span>
                                        </button>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Menunggu diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    Data riwayat presensi tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Penomoran Halaman (Pagination) -->
        <div class="mt-4">
            {{ $dataRiwayat->links() }}
        </div>

        <!-- Modal Ubah Logbook -->
        @if ($isEditModalOpen)
            <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm p-0 sm:p-4 transition-opacity">
                <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-t-2xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full text-left shadow-2xl">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-200 dark:border-slate-800 pb-3">
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Ubah Jurnal Harian</h3>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-900 dark:hover:text-white p-1">✕</button>
                    </div>

                    <form wire:submit.prevent="updateLogbook">
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-2">Isi Jurnal Kegiatan:</label>
                            <textarea 
                                wire:model="editingLogbook" 
                                rows="4" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl p-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                placeholder="Tuliskan catatan kegiatan harian..."></textarea>
                            @error('editingLogbook')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-xl text-xs font-semibold transition">
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                                <span wire:loading.remove wire:target="updateLogbook">Simpan Perubahan</span>
                                <span wire:loading wire:target="updateLogbook">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>