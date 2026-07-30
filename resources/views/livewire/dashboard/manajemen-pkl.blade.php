<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Manajemen Anak PKL</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data peserta PKL, atur jadwal masuk, serta pantau alokasi project.</p>
    </div>

    <!-- Flash Message Notifikasi -->
    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-700 dark:text-emerald-400 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <div class="relative overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl">
        <!-- Top Bar -->
        <div class="flex items-center justify-between p-5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 gap-4 overflow-x-auto no-scrollbar">
            
            <!-- Search Bar -->
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block p-2.5 ps-10 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 rounded-2xl w-64 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Cari nama atau email...">
                </div>
            </div>           
        </div>
        
        <!-- Tabel Data (READ) -->
        <div class="overflow-x-auto lg:overflow-x-visible">
            <table class="w-full table-fixed text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-4 py-4 w-[15%] text-center font-bold">Nama</th>
                        <th scope="col" class="px-4 py-4 w-[17%] text-center font-bold">Asal Sekolah</th>
                        <th scope="col" class="px-4 py-4 w-[9%] text-center font-bold">Status</th>
                        <th scope="col" class="px-4 py-4 w-[11%] text-center font-bold">Tanggal Masuk</th>
                        <th scope="col" class="px-4 py-4 w-[11%] text-center font-bold">Tanggal Keluar</th>
                        <th scope="col" class="px-4 py-4 w-[15%] text-center font-bold">Mentor</th>
                        <th scope="col" class="px-4 py-4 w-[22%] text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60">
                    @forelse($users as $user)
                        @php
                            $isAktif = strtolower($user->status?->value ?? $user->status ?? '') === 'aktif';
                        @endphp
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition align-middle">
                            <th scope="row" class="px-4 py-4 font-semibold text-gray-900 dark:text-white text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->nama }}</span>
                            </th>
                            <td class="px-4 py-4 text-gray-500 dark:text-gray-400 text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->asal_sekolah }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold uppercase border whitespace-nowrap
                                    {{ $isAktif 
                                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' 
                                        : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20' 
                                    }}">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isAktif ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-center whitespace-nowrap">{{ optional($user->tanggal_mulai)->format('d M Y') }}</td>
                            <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-center whitespace-nowrap">{{ optional($user->tanggal_Akhir)->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-center">
                                <span class="block line-clamp-2 break-words">{{ $user->mentor }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Edit -->
                                    <button 
                                        type="button" 
                                        wire:click="openEditProfile({{ $user->user_id }})" 
                                        title="Edit Profil"
                                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 border border-transparent hover:border-amber-200 dark:hover:border-amber-500/20 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <!-- Jadwal -->
                                    <button 
                                        type="button" 
                                        wire:click="openJadwalModal({{ $user->user_id }})" 
                                        title="Jadwal Masuk"
                                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 border border-transparent hover:border-blue-200 dark:hover:border-blue-500/20 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </button>

                                    <!-- Project -->
                                    <button 
                                        type="button" 
                                        wire:click="openProjectModal({{ $user->user_id }})" 
                                        title="Detail Project"
                                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-500/10 border border-transparent hover:border-purple-200 dark:hover:border-purple-500/20 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">Tidak ada data ditemukan.</td>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm px-4 py-6" @click="$event.target === $el && $wire.closeJadwalModal()" wire:key="jadwal-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white dark:bg-gray-900 p-6 shadow-2xl border border-gray-200 dark:border-gray-800" @click.stop>
                <div class="pt-2">
                    @livewire('form.jadwal', ['userId' => $selectedUserId], key('jadwal-modal-' . ($selectedUserId ?? 'new')))
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Project -->
    @if($showProjectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm px-4 py-6" @click="$event.target === $el && $wire.closeProjectModal()" wire:key="project-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl bg-white dark:bg-gray-900 p-6 shadow-2xl border border-gray-200 dark:border-gray-800" @click.stop>
                @livewire('form.project', ['userId' => $selectedUserId], key('project-modal-' . ($selectedUserId ?? 'new')))
            </div>
        </div>
    @endif
</div>