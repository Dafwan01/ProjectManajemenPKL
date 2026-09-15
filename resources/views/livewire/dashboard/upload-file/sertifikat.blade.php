<div class="p-3 sm:p-6">
    <!-- JUDUL HEADER & SUBJUDUL -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Sertifikat Magang</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-1">Kelola dan terbitkan sertifikat kelulusan bagi peserta magang.</p>
    </div>

    <!-- Pesan Notifikasi Sukses -->
    @if (session()->has('message'))
        <div class="p-3.5 sm:p-4 mb-4 sm:mb-6 text-xs sm:text-sm text-emerald-700 dark:text-emerald-400 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Pesan Notifikasi Gagal -->
    @if (session()->has('error'))
        <div class="p-3.5 sm:p-4 mb-4 sm:mb-6 text-xs sm:text-sm text-rose-700 dark:text-rose-400 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('error') }}</span>
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
                    placeholder="Cari nama, email, atau sekolah..."
                >
            </div>
        </div>
        
        <!-- MOBILE / TABLET VIEW: Card-List (Layar < lg) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 lg:hidden">
            @forelse($users as $user)
                @php
                    $hasSertifikat = $user->files->contains('nama_file', 'Sertifikat');
                @endphp
                <div class="p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/40 flex flex-col justify-between gap-4" wire:key="mobile-user-{{ $user->user_id }}">
                    <div class="space-y-3">
                        <!-- Header Card: Nama & Status Sertifikat -->
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
                                {{ $hasSertifikat 
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' 
                                    : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' 
                                }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $hasSertifikat ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                {{ $hasSertifikat ? 'Sudah Diterbitkan' : 'Belum Diterbitkan' }}
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 001.946.806 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            <span>{{ $hasSertifikat ? 'Terbitkan Ulang' : 'Terbitkan' }}</span>
                        </button>

                        @if($hasSertifikat)
                            <button 
                                type="button"
                                wire:click="openPdfModal({{ $user->user_id }})"
                                class="flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-xl transition active:scale-95 shrink-0"
                                title="Lihat PDF Sertifikat"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                        <th scope="col" class="px-6 py-4 text-center font-bold">Status Sertifikat</th>
                        <th scope="col" class="px-6 py-4 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700/60">
                    @forelse($users as $user)
                        @php
                            $hasSertifikat = $user->files->contains('nama_file', 'Sertifikat');
                        @endphp
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
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border
                                    {{ $hasSertifikat 
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' 
                                        : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' 
                                    }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $hasSertifikat ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                    {{ $hasSertifikat ? 'Sudah Diterbitkan' : 'Belum Diterbitkan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        type="button"
                                        wire:click="openForm({{ $user->user_id }})"
                                        class="flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-md shadow-blue-600/20 transition shrink-0"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 001.946.806 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                        <span>{{ $hasSertifikat ? 'Terbitkan Ulang' : 'Terbitkan' }}</span>
                                    </button>

                                    @if($hasSertifikat)
                                        <button 
                                            type="button"
                                            wire:click="openPdfModal({{ $user->user_id }})"
                                            class="flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-xl transition shrink-0 shadow-sm"
                                            title="Lihat PDF Sertifikat"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat PDF</span>
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

        <!-- Paginasi -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- 1. MODAL FORMULIR TERBITKAN SERTIFIKAT -->
    @if($showModal && $selectedUserId)
        @php
            $targetUser = $users->firstWhere('user_id', $selectedUserId) ?? \App\Models\User::where('user_id', $selectedUserId)->first();
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-gray-900/60 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-lg max-h-[90vh] bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">
                        Terbitkan Sertifikat Magang
                    </h3>
                    <button wire:click="closeForm" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Bodi Modal (Scrollable) -->
                <form wire:submit.prevent="generate" class="p-4 sm:p-5 space-y-4 overflow-y-auto">

                    <!-- Peringatan Kesalahan di Dalam Modal -->
                    @if (session()->has('error'))
                        <div class="p-3 text-xs text-rose-700 dark:text-rose-400 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 flex items-center justify-between" role="alert">
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Rincian Peserta -->
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl space-y-1 border border-gray-100 dark:border-gray-700/50">
                        <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500">Peserta Magang</p>
                        <p class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $targetUser?->nama }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $targetUser?->sekolah?->nama_sekolah ?? '-' }} (Mentor: {{ $targetUser?->mentor ?? '-' }})</p>
                    </div>

                    <!-- Input Nomor Sertifikat -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nomor Sertifikat</label>
                        <input 
                            type="text" 
                            wire:model="nomorSertifikat" 
                            class="w-full p-2.5 text-xs sm:text-sm rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
                        >
                        @error('nomorSertifikat') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Tanggal Terbit -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Terbit</label>
                        <input 
                            type="date" 
                            wire:model="tanggalTerbit" 
                            class="w-full p-2.5 text-xs sm:text-sm rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
                        >
                        @error('tanggalTerbit') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Nama Penandatangan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Penandatangan</label>
                        <input 
                            type="text" 
                            wire:model="namaPenandatangan" 
                            placeholder="Contoh: Dr. Budi Santoso, M.T."
                            class="w-full p-2.5 text-xs sm:text-sm rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
                        >
                        @error('namaPenandatangan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Jabatan Penandatangan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Jabatan Penandatangan</label>
                        <input 
                            type="text" 
                            wire:model="jabatanPenandatangan" 
                            placeholder="Contoh: Kepala Bagian SDM"
                            class="w-full p-2.5 text-xs sm:text-sm rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
                        >
                        @error('jabatanPenandatangan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pilihan Jenis Tanda Tangan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Jenis Tanda Tangan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-1">
                            <label class="flex items-center gap-2 p-2.5 sm:p-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <input type="radio" wire:model="jenisTtd" value="elektronik" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-800 dark:text-gray-200">TTD Elektronik</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 sm:p-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <input type="radio" wire:model="jenisTtd" value="non_elektronik" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-800 dark:text-gray-200">Non-Elektronik</span>
                            </label>
                        </div>
                        @error('jenisTtd') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Modal -->
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button 
                            type="button" 
                            wire:click="closeForm" 
                            class="px-3.5 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-md transition disabled:opacity-50 active:scale-95"
                        >
                            <svg wire:loading wire:target="generate" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Proses & Buat PDF</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- 2. MODAL PRATINJAU PDF (IFRAME) -->
    @if ($showPdfModal && $previewUrl)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/70 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-5xl bg-white dark:bg-gray-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col h-[85vh] sm:h-[90vh]">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between px-4 sm:px-6 py-3.5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide truncate pr-2">
                        Sertifikat Magang - {{ $previewUserName }}
                    </h3>
                    <button 
                        wire:click="closePdfModal" 
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition shrink-0"
                    >
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Bodi Modal (Pratinjau PDF) -->
                <div class="flex-1 w-full bg-gray-100 dark:bg-gray-950">
                    <iframe 
                        src="{{ $previewUrl }}" 
                        class="w-full h-full border-0"
                    ></iframe>
                </div>

            </div>
        </div>
    @endif

</div>