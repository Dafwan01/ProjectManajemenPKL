<div class="p-3 sm:p-6">
    <!-- JUDUL HEADER & SUBJUDUL -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Penilaian Magang</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-1">Kelola dan masukkan penilaian evaluasi untuk peserta magang.</p>
    </div>

    <!-- Pesan Notifikasi Flash -->
    @if (session()->has('message'))
        <div class="p-3.5 sm:p-4 mb-4 sm:mb-6 text-xs sm:text-sm text-emerald-700 dark:text-emerald-400 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    @if(session('debug'))
        <div class="p-3.5 sm:p-4 mb-4 sm:mb-6 text-xs sm:text-sm text-amber-700 dark:text-amber-400 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20">
            {{ session('debug') }}
        </div>
    @endif

    <!-- Kontainer Utama -->
    <div class="relative overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl sm:rounded-3xl shadow-xl">
        
        <!-- Bar Atas / Kolom Pencarian -->
        <div class="p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700/80">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    class="block p-2.5 ps-10 text-xs sm:text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 rounded-2xl w-full bg-gray-50 dark:bg-gray-900/50 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                    placeholder="Cari nama atau email..."
                >
            </div>
        </div>
        
        <!-- MOBILE / TABLET VIEW: Card-List (Layar < lg) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 lg:hidden">
            @forelse($users as $user)
                @php
                    $nilaiUser = $user->nilai ?? $user->nilais?->first();
                    $nilaiLengkap = $nilaiUser 
                        && $nilaiUser->kedisiplinan > 0
                        && $nilaiUser->kemampuan_teknis > 0
                        && $nilaiUser->problem_solving > 0
                        && $nilaiUser->komunikasi_kerjasama > 0
                        && $nilaiUser->kualitas_ketepatan > 0;
                @endphp
                <div class="p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/40 flex flex-col justify-between gap-4" wire:key="mobile-user-{{ $user->user_id }}">
                    <div class="space-y-3">
                        <!-- Header Card: Nama & Status Penilaian -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="overflow-hidden">
                                <h3 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base truncate">
                                    {{ $user->nama }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                    {{ $user->sekolah?->nama_sekolah ?? '-' }}
                                </p>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wide uppercase border shrink-0
                                {{ $nilaiLengkap 
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' 
                                    : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' 
                                }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $nilaiLengkap ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                {{ $nilaiLengkap ? 'Sudah Diisi' : 'Belum Diisi' }}
                            </span>
                        </div>

                        <!-- Info Mentor & Tanggal -->
                        <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-gray-200/60 dark:border-gray-700/40">
                            <div>
                                <span class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500">Mentor Pembimbing</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300 truncate block">{{ $user->mentor ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500">Tanggal Mulai</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300 block">
                                    {{ $user->tanggal_mulai ? \Illuminate\Support\Carbon::parse($user->tanggal_mulai)->translatedFormat('d M Y') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-200/60 dark:border-gray-700/40">
                        <button 
                            type="button" 
                            wire:click="openForm({{ $user->user_id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-md shadow-blue-600/20 transition active:scale-95"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Formulir Nilai</span>
                        </button>

                        @if($nilaiLengkap)
                            <button 
                                type="button" 
                                wire:click="openPdfModal({{ $user->user_id }})"
                                class="flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-xl transition active:scale-95 shrink-0"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>PDF</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-10 text-center text-gray-400 dark:text-gray-500">
                    <div class="inline-flex p-3 bg-gray-100 dark:bg-gray-900/50 rounded-full mb-3 text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p class="text-xs font-medium">Tidak ada data peserta magang ditemukan.</p>
                </div>
            @endforelse
        </div>

        <!-- DESKTOP VIEW: Tabel Data (Layar >= lg) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-900/60 text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700/80">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Nama</th>
                        <th scope="col" class="px-6 py-4 font-bold">Asal Sekolah</th>
                        <th scope="col" class="px-6 py-4 font-bold">Mentor Pembimbing</th>
                        <th scope="col" class="px-6 py-4 text-center font-bold">Tanggal Mulai</th>
                        <th scope="col" class="px-6 py-4 text-center font-bold">Status Penilaian</th>
                        <th scope="col" class="px-6 py-4 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700/60">
                    @forelse($users as $user)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition" wire:key="desktop-user-{{ $user->user_id }}">
                            <th scope="row" class="px-6 py-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $user->nama }}
                            </th>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->sekolah?->nama_sekolah ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->mentor ?? '-' }}</td>
                            
                            <td class="px-6 py-4 text-center whitespace-nowrap text-gray-600 dark:text-gray-400">
                                {{ $user->tanggal_mulai ? \Illuminate\Support\Carbon::parse($user->tanggal_mulai)->translatedFormat('d F Y') : '-' }}
                            </td>

                            <td class="px-6 py-4 text-center align-middle">
                                @php
                                    $nilaiUser = $user->nilai ?? $user->nilais?->first();
                                    $nilaiLengkap = $nilaiUser 
                                        && $nilaiUser->kedisiplinan > 0
                                        && $nilaiUser->kemampuan_teknis > 0
                                        && $nilaiUser->problem_solving > 0
                                        && $nilaiUser->komunikasi_kerjasama > 0
                                        && $nilaiUser->kualitas_ketepatan > 0;
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border
                                    {{ $nilaiLengkap 
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' 
                                        : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' 
                                    }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $nilaiLengkap ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                    {{ $nilaiLengkap ? 'Sudah Diisi' : 'Belum Diisi' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        type="button" 
                                        wire:click="openForm({{ $user->user_id }})"
                                        class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md shadow-blue-600/20 transition shrink-0"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Formulir Nilai
                                    </button>

                                    @if($nilaiLengkap)
                                        <button 
                                            type="button" 
                                            wire:click="openPdfModal({{ $user->user_id }})"
                                            class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-2xl transition shrink-0 shadow-sm"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat PDF
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                <div class="inline-flex p-3 bg-gray-100 dark:bg-gray-900/50 rounded-full mb-3 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="text-xs font-medium">Tidak ada data peserta magang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Navigasi Halaman / Paginasi -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- 1. MODAL FORMULIR INPUT NILAI -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-3 sm:p-6" 
             @click="$event.target === $el && $wire.closeForm()" 
             wire:key="nilai-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl sm:rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 p-4 sm:p-6 shadow-2xl text-gray-900 dark:text-gray-100" @click.stop>
               @livewire('form.nilai', ['userId' => $selectedUserId], key('nilai-modal-' . ($selectedUserId ?? 'new')))
            </div>
        </div>
    @endif

    <!-- 2. MODAL PRATINJAU PDF TRANSKRIP -->
    @if ($showPdfModal && $pdfUserId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/70 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-6xl bg-white dark:bg-gray-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col h-[85vh] sm:h-[90vh]">
                
                <!-- Bar Header Modal -->
                <div class="flex items-center justify-between px-4 sm:px-6 py-3.5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                        Laporan Transkrip Nilai Magang
                    </h3>
                    <button 
                        wire:click="closePdfModal" 
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    >
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Bodi Modal (Tampilan PDF Frame) -->
                <div class="flex-1 w-full bg-gray-100 dark:bg-gray-950">
                    <iframe 
                        src="{{ route('cetak.nilai', ['userId' => $pdfUserId]) }}" 
                        class="w-full h-full border-0"
                    ></iframe>
                </div>

            </div>
        </div>
    @endif

</div>