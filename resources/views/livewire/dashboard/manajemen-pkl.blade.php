<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Manajemen Peserta PKL</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data peserta PKL, atur jadwal presensi harian, serta pantau alokasi proyek.</p>
    </div>

    <!-- Flash Message Notifikasi -->
    @if (session()->has('message'))
        <div class="p-3 sm:p-4 mb-4 sm:mb-6 text-xs sm:text-sm text-emerald-700 dark:text-emerald-400 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center gap-2 shadow-sm" role="alert">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <div class="relative overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl sm:rounded-3xl shadow-xl">
        
        <!-- Top Bar -->
        <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
            <!-- Search Bar -->
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" class="block w-full p-2.5 ps-10 text-xs sm:text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 rounded-2xl placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Cari nama, email, sekolah...">
            </div>
        </div>

        <!-- MOBILE / TABLET VIEW: Card List (Sembunyi di lg) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 p-3 sm:p-4 lg:hidden">
            @forelse($users as $user)
                @php
                    $userStatusVal = strtolower($user->status?->value ?? $user->status ?? '');
                    $isAktif = $userStatusVal === 'aktif';
                @endphp
                <div class="p-4 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20 flex flex-col justify-between gap-3" wire:key="mobile-card-{{ $user->user_id }}">
                    <div class="space-y-2">
                        <!-- Nama & Status -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="overflow-hidden">
                                <h3 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base leading-tight truncate" title="{{ $user->nama }}">{{ $user->nama }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $user->sekolah?->nama_sekolah ?? '-' }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase border shrink-0
                                {{ $isAktif 
                                    ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' 
                                    : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20' 
                                }}">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isAktif ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                {{ ucfirst($userStatusVal) }}
                            </span>
                        </div>

                        <!-- Detail Informasi Tanggal & Mentor -->
                        <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-gray-200/60 dark:border-gray-800/60">
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tgl Masuk</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ optional($user->tanggal_mulai)->translatedFormat('d M Y') ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tgl Keluar</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ optional($user->tanggal_akhir)->translatedFormat('d M Y') ?? '-' }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Mentor</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium truncate block">{{ $user->mentor ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi: Horizontal di HP, Vertikal (Kebawah) di Medium / Tablet -->
                    <div class="flex flex-col sm:flex-row md:flex-col items-stretch gap-1.5 pt-2 border-t border-gray-200/60 dark:border-gray-800/60">
                        <button 
                            type="button" 
                            wire:click="openEditProfile({{ $user->user_id }})" 
                            title="Ubah Profil"
                            class="w-full py-2 px-3 inline-flex items-center justify-center gap-1.5 text-xs font-semibold rounded-xl text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 active:scale-95 transition"
                        >
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            <span>Profil</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="openJadwalModal({{ $user->user_id }})" 
                            title="Kelola Jadwal"
                            class="w-full py-2 px-3 inline-flex items-center justify-center gap-1.5 text-xs font-semibold rounded-xl text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 active:scale-95 transition"
                        >
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Jadwal</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="openProjectModal({{ $user->user_id }})" 
                            title="Rincian Proyek"
                            class="w-full py-2 px-3 inline-flex items-center justify-center gap-1.5 text-xs font-semibold rounded-xl text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/20 active:scale-95 transition"
                        >
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Proyek</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10 text-xs sm:text-sm text-gray-400 dark:text-gray-500">
                    Tidak ada data peserta PKL yang ditemukan.
                </div>
            @endforelse
        </div>

        <!-- DESKTOP VIEW: Tabel Data (Muncul hanya di >= lg) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full table-fixed text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-4 py-4 w-[16%] text-center font-bold">Nama Lengkap</th>
                        <th scope="col" class="px-4 py-4 w-[16%] text-center font-bold">Asal Sekolah</th>
                        <th scope="col" class="px-4 py-4 w-[10%] text-center font-bold">Status</th>
                        <th scope="col" class="px-4 py-4 w-[12%] text-center font-bold">Tanggal Masuk</th>
                        <th scope="col" class="px-4 py-4 w-[12%] text-center font-bold">Tanggal Keluar</th>
                        <th scope="col" class="px-4 py-4 w-[14%] text-center font-bold">Mentor</th>
                        <th scope="col" class="px-4 py-4 w-[20%] text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60">
                    @forelse($users as $user)
                        @php
                            $userStatusVal = strtolower($user->status?->value ?? $user->status ?? '');
                            $isAktif = $userStatusVal === 'aktif';
                        @endphp
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition align-middle" wire:key="desktop-row-{{ $user->user_id }}">
                            <th scope="row" class="px-4 py-4 font-semibold text-gray-900 dark:text-white text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->nama }}</span>
                            </th>
                            <td class="px-4 py-4 text-gray-500 dark:text-gray-400 text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->sekolah?->nama_sekolah ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold uppercase border whitespace-nowrap
                                    {{ $isAktif 
                                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' 
                                        : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20' 
                                    }}">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isAktif ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                    {{ ucfirst($userStatusVal) }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-center whitespace-nowrap">
                                {{ optional($user->tanggal_mulai)->translatedFormat('d F Y') ?? '-' }}
                            </td>

                            <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-center whitespace-nowrap">
                                {{ optional($user->tanggal_akhir)->translatedFormat('d F Y') ?? '-' }}
                            </td>

                            <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->mentor ?? '-' }}</span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button 
                                        type="button" 
                                        wire:click="openEditProfile({{ $user->user_id }})" 
                                        title="Ubah Profil"
                                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 border border-transparent hover:border-amber-200 dark:hover:border-amber-500/20 transition"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openJadwalModal({{ $user->user_id }})" 
                                        title="Kelola Jadwal Masuk"
                                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 border border-transparent hover:border-blue-200 dark:hover:border-blue-500/20 transition"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openProjectModal({{ $user->user_id }})" 
                                        title="Rincian Proyek"
                                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-500/10 border border-transparent hover:border-purple-200 dark:hover:border-purple-500/20 transition"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">Tidak ada data peserta PKL yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Panggil File Form Modal Terpisah -->
    @if($showEditProfileModal)
        @include('livewire.form.profile', ['userId' => $selectedUserId])
    @endif

    <!-- Modal Jadwal -->
    @if($showJadwalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm p-3 sm:p-6" @click="$event.target === $el && $wire.closeJadwalModal()" wire:key="jadwal-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-2xl sm:rounded-3xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-2xl border border-gray-200 dark:border-gray-800" @click.stop>
                <div class="pt-1 sm:pt-2">
                    @livewire('form.jadwal', ['userId' => $selectedUserId], key('jadwal-modal-' . ($selectedUserId ?? 'new')))
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Project -->
    @if($showProjectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm p-3 sm:p-6" @click="$event.target === $el && $wire.closeProjectModal()" wire:key="project-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl sm:rounded-3xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-2xl border border-gray-200 dark:border-gray-800" @click.stop>
                @livewire('form.project', ['userId' => $selectedUserId], key('project-modal-' . ($selectedUserId ?? 'new')))
            </div>
        </div>
    @endif
</div>