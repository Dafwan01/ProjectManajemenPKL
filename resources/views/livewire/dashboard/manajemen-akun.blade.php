<div>
    <!-- JUDUL HEADER & SUBJUDUL -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Manajemen Akun</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data akun pengguna, pendaftaran pengguna baru, serta hak akses peran (role) sistem.</p>
    </div>

    <!-- Pesan Notifikasi Flash -->
    @if (session()->has('message'))
        <div class="mb-4 p-3.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-600/50 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm rounded-2xl flex items-center gap-2.5 shadow-sm" role="alert">
            <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <div class="relative overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl sm:rounded-3xl shadow-xl">
        <!-- Bar Atas / Pencarian & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between p-4 sm:p-5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 gap-3">
            
            <!-- Kolom Pencarian -->
            <div class="relative w-full sm:w-auto">
                <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none z-10">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    class="w-full block p-2.5 ps-10 text-xs sm:text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 rounded-2xl sm:w-64 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                    placeholder="Cari nama, email, sekolah..."
                >
            </div>

            <!-- Tombol Tambah Akun -->
            <button 
                type="button" 
                wire:click="openCreateModal" 
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 text-xs sm:text-sm font-semibold text-white bg-blue-600 rounded-2xl hover:bg-blue-500 shadow-md shadow-blue-600/20 transition shrink-0"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Akun Baru
            </button>
        </div>
        
        <!-- MOBILE / TABLET VIEW: Card-List (Hanya tampil di layar < lg) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 lg:hidden">
            @forelse($users as $user)
                @php
                    $userStatus = strtolower($user->status?->value ?? $user->status ?? '');
                    $isAktif = $userStatus === 'aktif';
                @endphp
                <div class="p-4 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20 flex flex-col justify-between gap-3">
                    <div class="space-y-2.5">
                        <!-- Header Card: Nama, Status & Role -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="overflow-hidden">
                                <h3 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base leading-tight truncate">{{ $user->nama }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $user->email }}</p>
                            </div>
                            
                            <div class="flex flex-col gap-1 items-end shrink-0">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase border
                                    {{ $isAktif 
                                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' 
                                        : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20' 
                                    }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isAktif ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                    {{ ucfirst($userStatus ?: 'N/A') }}
                                </span>

                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20">
                                    {{ $user->role }}
                                </span>
                            </div>
                        </div>

                        <!-- Info Detail: Sekolah & Tanggal Mulai -->
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800/80 text-xs">
                            <div>
                                <span class="text-[10px] text-gray-400 block font-semibold uppercase">Asal Sekolah</span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 block truncate">{{ $user->sekolah?->nama_sekolah ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 block font-semibold uppercase">Tgl Mulai</span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 block">{{ $user->tanggal_mulai ? \Illuminate\Support\Carbon::parse($user->tanggal_mulai)->translatedFormat('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Mobile -->
                    <div class="pt-2 border-t border-gray-200/60 dark:border-gray-800/60 flex items-center gap-2">
                        <button 
                            type="button" 
                            wire:click="openEditModal({{ $user->user_id }})" 
                            class="flex-1 py-2 px-3 inline-flex items-center justify-center gap-1.5 text-xs font-semibold rounded-xl text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 active:scale-95 transition"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Ubah
                        </button>

                        <button 
                            type="button" 
                            wire:click="delete({{ $user->user_id }})" 
                            onclick="confirm('Apakah Anda yakin ingin menghapus akun ini?') || event.stopImmediatePropagation()" 
                            class="flex-1 py-2 px-3 inline-flex items-center justify-center gap-1.5 text-xs font-semibold rounded-xl text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 active:scale-95 transition"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10 text-xs sm:text-sm text-gray-400 dark:text-gray-500">
                    Tidak ada data akun yang ditemukan.
                </div>
            @endforelse
        </div>

      <!-- DESKTOP VIEW: Tabel Data (Hanya tampil di layar >= lg) -->
        <div class="hidden lg:block">
            <table class="w-full table-fixed text-sm text-left text-gray-600 dark:text-gray-300">
                <colgroup>
                    <col class="w-[18%]">
                    <col class="w-[22%]">
                    <col class="w-[18%]">
                    <col class="w-[14%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                    <col class="w-[8%]">
                </colgroup>
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold cursor-pointer hover:text-gray-900 dark:hover:text-white transition" wire:click="sortBy('nama')">
                            Nama
                        </th>
                        <th scope="col" class="px-6 py-4 font-bold">Email</th>
                        <th scope="col" class="px-6 py-4 font-bold cursor-pointer hover:text-gray-900 dark:hover:text-white transition" wire:click="sortBy('sekolah')">
                            Asal Sekolah
                        </th>
                        <th scope="col" class="px-6 py-4 font-bold text-center cursor-pointer hover:text-gray-900 dark:hover:text-white transition" wire:click="sortBy('tanggal_mulai')">
                            Tanggal Mulai
                        </th>
                        <th scope="col" class="px-6 py-4 font-bold">Status</th>
                        <th scope="col" class="px-6 py-4 font-bold">Peran (Role)</th>
                        <th scope="col" class="px-6 py-4 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60">
                    @forelse($users as $user)
                        @php
                            $userStatus = strtolower($user->status?->value ?? $user->status ?? '');
                            $isAktif = $userStatus === 'aktif';
                        @endphp
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">
                            <th scope="row" class="px-6 py-4 font-semibold text-gray-900 dark:text-white truncate" title="{{ $user->nama }}">
                                {{ $user->nama }}
                            </th>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 truncate" title="{{ $user->email }}">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 truncate" title="{{ $user->sekolah?->nama_sekolah ?? '-' }}">
                                {{ $user->sekolah?->nama_sekolah ?? '-' }}
                            </td>
                            
                            <td class="px-6 py-4 text-center whitespace-nowrap text-gray-500 dark:text-gray-400">
                                {{ $user->tanggal_mulai ? \Illuminate\Support\Carbon::parse($user->tanggal_mulai)->translatedFormat('d F Y') : '-' }}
                            </td>

                            <td class="px-6 py-4 align-middle">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border
                                    {{ $isAktif 
                                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' 
                                        : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20' 
                                    }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isAktif ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                    {{ ucfirst($userStatus ?: 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-middle">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Edit -->
                                    <button 
                                        type="button" 
                                        wire:click="openEditModal({{ $user->user_id }})" 
                                        title="Ubah Akun"
                                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 border border-transparent hover:border-amber-200 dark:hover:border-amber-500/20 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button 
                                        type="button" 
                                        wire:click="delete({{ $user->user_id }})" 
                                        onclick="confirm('Apakah Anda yakin ingin menghapus akun ini?') || event.stopImmediatePropagation()" 
                                        title="Hapus Akun"
                                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 border border-transparent hover:border-rose-200 dark:hover:border-rose-500/20 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
                                Tidak ada data akun yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Navigasi Paginasi -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Panggil Modal Formulir Akun -->
    @if($showModal)
        @include('livewire.form.akun')
    @endif
</div>