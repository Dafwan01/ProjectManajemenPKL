<div>
    <!-- Header Dashboard -->
    <div class="no-print mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Rekap Absensi Peserta PKL</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pilih periode dan klik tombol rekap pada siswa yang bersangkutan.</p>
    </div>

    <!-- Container Utama -->
    <div class="no-print relative overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl">
        
        <!-- Filter Periode -->
        <div class="p-5 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex items-center gap-3 flex-wrap">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Periode Rekap:</span>
            
            <!-- Dropdown Bulan -->
            <select 
                wire:model.live="bulan"
                class="p-2.5 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 rounded-2xl w-36 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
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
                class="p-2.5 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 rounded-2xl w-28 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            >
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

        <!-- Tabel List Anak PKL -->
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-4 py-4 w-[8%] text-center font-bold">No</th>
                        <th scope="col" class="px-4 py-4 w-[32%] text-center font-bold">Nama Lengkap</th>
                        <th scope="col" class="px-4 py-4 w-[32%] text-center font-bold">Asal Sekolah</th>
                        <th scope="col" class="px-4 py-4 w-[28%] text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60">
                    @forelse($usersPKL as $index => $user)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition align-middle">
                            <td class="px-4 py-4 text-center font-medium text-gray-700 dark:text-gray-300">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->nama ?? $user->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-4 text-gray-500 dark:text-gray-400 text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->sekolah?->nama_sekolah ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button 
                                    wire:click="bukaModalRekap('{{ $user->user_id }}')" 
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-2xl transition border
                                    text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 shadow-sm"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Lihat Rekap</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
                                Belum ada data peserta PKL terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL PDF PREVIEW (VIEWER BAZAAR BROWSER)  -->
    <!-- ========================================== -->
    @if($showModal && $selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/70 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-6xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col h-[90vh]">
                
                <!-- Modal Header Bar -->
                <div class="flex items-center justify-between px-6 py-3.5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                        Rekap Absensi - {{ $selectedUser->nama ?? $selectedUser->name }} ({{ $bulan }}/{{ $tahun }})
                    </h3>
                    <button 
                        wire:click="tutupModal" 
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body: Embed PDF viewer dalam iframe -->
                <div class="flex-1 w-full bg-gray-100 dark:bg-gray-950">
                    <iframe 
                        src="{{ route('cetak.rekap-absensi', ['userId' => $selectedUser->user_id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                        class="w-full h-full border-0"
                    ></iframe>
                </div>

            </div>
        </div>
    @endif

</div>