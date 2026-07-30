<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-white uppercase">Manajemen Anak PKL</h1>
        <p class="text-sm text-slate-400 mt-1">Kelola data peserta PKL, atur jadwal masuk, serta pantau alokasi project.</p>
    </div>

    <!-- Flash Message Notifikasi -->
    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <div class="relative overflow-hidden bg-[#0d1322] border border-slate-800/80 rounded-2xl shadow-xl">
        <!-- Top Bar -->
        <div class="flex items-center justify-between p-4 bg-[#0d1322] border-b border-slate-800/80 gap-4 overflow-x-auto no-scrollbar">
            
            <!-- Search Bar -->
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block p-2.5 ps-10 text-sm text-slate-200 border border-slate-700/80 rounded-xl w-64 bg-[#0b0f19] placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Cari nama atau email...">
                </div>
            </div>           
        </div>
        
        <!-- Tabel Data (READ) -->
      <!-- Tabel Data (READ) -->
<div class="overflow-x-auto lg:overflow-x-visible">
    <table class="w-full table-fixed text-sm text-slate-300">
    <thead class="text-xs uppercase bg-[#080c14] text-slate-400 border-b border-slate-800/80">
        <tr>
            <th scope="col" class="px-4 py-4 w-[15%] text-center">Nama</th>
            <th scope="col" class="px-4 py-4 w-[17%] text-center">Asal Sekolah</th>
            <th scope="col" class="px-4 py-4 w-[9%] text-center">Status</th>
            <th scope="col" class="px-4 py-4 w-[11%] text-center">Tanggal Masuk</th>
            <th scope="col" class="px-4 py-4 w-[11%] text-center">Tanggal Keluar</th>
            <th scope="col" class="px-4 py-4 w-[15%] text-center">Mentor</th>
            <th scope="col" class="px-4 py-4 w-[22%] text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-800/60">
        @forelse($users as $user)
            @php
                $isAktif = strtolower($user->status?->value ?? $user->status ?? '') === 'aktif';
            @endphp
            <tr class="bg-[#0d1322] hover:bg-slate-800/40 transition align-middle">
                <th scope="row" class="px-4 py-4 font-semibold text-white text-center">
                    <span class="block line-clamp-2 break-words">{{ $user->nama }}</span>
                </th>
                <td class="px-4 py-4 text-slate-400 text-center">
                    <span class="block line-clamp-2 break-words">{{ $user->asal_sekolah }}</span>
                </td>
                <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium uppercase border whitespace-nowrap
                        {{ $isAktif 
                            ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' 
                            : 'bg-rose-500/10 text-rose-400 border-rose-500/20' 
                        }}">
                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isAktif ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                        {{ $user->status }}
                    </span>
                </td>
                <td class="px-4 py-4 text-slate-300 text-center whitespace-nowrap">{{ optional($user->tanggal_mulai)->format('d M Y') }}</td>
                <td class="px-4 py-4 text-slate-300 text-center whitespace-nowrap">{{ optional($user->tanggal_Akhir)->format('d M Y') ?? '-' }}</td>
                <td class="px-4 py-4 text-slate-300 text-center">
                    <span class="block line-clamp-2 break-words">{{ $user->mentor }}</span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-center gap-1">
                        <!-- Edit -->
                        <button 
                            type="button" 
                            wire:click="openEditProfile({{ $user->user_id }})" 
                            title="Edit Profil"
                            class="p-2 rounded-xl text-slate-400 hover:text-amber-400 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 transition"
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
                            class="p-2 rounded-xl text-slate-400 hover:text-blue-400 hover:bg-blue-500/10 border border-transparent hover:border-blue-500/20 transition"
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
                            class="p-2 rounded-xl text-slate-400 hover:text-purple-400 hover:bg-purple-500/10 border border-transparent hover:border-purple-500/20 transition"
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
                <td colspan="7" class="px-6 py-10 text-center text-slate-500">Tidak ada data ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-800/80 bg-[#0d1322]">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Panggil File Form Modal Terpisah -->
    @if($showEditProfileModal)
        @include('livewire.form.profile', ['userId' => $selectedUserId])
    @endif

    @if($showJadwalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-6" @click="$event.target === $el && $wire.closeJadwalModal()" wire:key="jadwal-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-2xl bg-[#0d1322] p-6 shadow-2xl border border-slate-800/80" @click.stop>
                <div class="pt-2">
                    @livewire('form.jadwal', ['userId' => $selectedUserId], key('jadwal-modal-' . ($selectedUserId ?? 'new')))
                </div>
            </div>
        </div>
    @endif

    @if($showProjectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-6" @click="$event.target === $el && $wire.closeProjectModal()" wire:key="project-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-[#0d1322] p-6 shadow-2xl border border-slate-800/80" @click.stop>
                @livewire('form.project', ['userId' => $selectedUserId], key('project-modal-' . ($selectedUserId ?? 'new')))
            </div>
        </div>
    @endif
</div>