<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-white uppercase">Nilai Magang</h1>
        <p class="text-sm text-slate-400 mt-1">Kelola dan input penilaian untuk peserta magang.</p>
    </div>

    <!-- Flash Message Notifikasi -->
    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    @if(session('debug'))
        <div class="p-4 mb-6 text-sm text-amber-400 rounded-xl bg-amber-500/10 border border-amber-500/20">
            {{ session('debug') }}
        </div>
    @endif

    <!-- Container Utama -->
    <div class="relative overflow-hidden bg-[#0d1322] border border-slate-800/80 rounded-2xl shadow-xl">
        
        <!-- Top Bar / Search Bar -->
        <div class="flex items-center justify-between p-4 bg-[#0d1322] border-b border-slate-800/80 gap-4 overflow-x-auto no-scrollbar">
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="block p-2.5 ps-10 text-sm text-slate-200 border border-slate-700/80 rounded-xl w-64 bg-[#0b0f19] placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                        placeholder="Cari nama atau email..."
                    >
                </div>
            </div>
        </div>
        
        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-300">
                <thead class="text-xs uppercase bg-[#080c14] text-slate-400 border-b border-slate-800/80">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama</th>
                        <th scope="col" class="px-6 py-4">Asal Sekolah</th>
                        <th scope="col" class="px-6 py-4">Mentor</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $user)
                        <tr class="bg-[#0d1322] hover:bg-slate-800/40 transition">
                            <th scope="row" class="px-6 py-4 font-semibold text-white whitespace-nowrap">
                                {{ $user->nama }}
                            </th>
                            <td class="px-6 py-4 text-slate-400">{{ $user->asal_sekolah }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $user->mentor }}</td>
                            <td class="px-6 py-4 align-middle">
                                @php
                                    $nilaiUser = $user->nilais->first();
                                    $nilaiLengkap = $nilaiUser 
                                        && $nilaiUser->kedisiplinan > 0
                                        && $nilaiUser->kemampuan_teknis > 0
                                        && $nilaiUser->problem_solving > 0
                                        && $nilaiUser->komunikasi_kerjasama > 0
                                        && $nilaiUser->kualitas_ketepatan > 0;
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium tracking-wide uppercase border
                                    {{ $nilaiLengkap 
                                        ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' 
                                        : 'bg-rose-500/10 text-rose-400 border-rose-500/20' 
                                    }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $nilaiLengkap ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                    {{ $nilaiLengkap ? 'Sudah Diisi' : 'Belum Diisi' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <button 
                                        type="button" 
                                        wire:click="openForm({{ $user->user_id }})"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-500 shadow-md shadow-blue-600/20 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Buka Form Nilai
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">Tidak ada data ditemukan.</td>
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

    <!-- Modal Form Terpisah -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-6" 
             @click="$event.target === $el && $wire.closeForm()" 
             wire:key="nilai-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-[#0d1322] border border-slate-800 p-6 shadow-2xl text-slate-200" @click.stop>
               @livewire('form.nilai', ['userId' => $selectedUserId], key('nilai-modal-' . ($selectedUserId ?? 'new')))
            </div>
        </div>
    @endif
</div>