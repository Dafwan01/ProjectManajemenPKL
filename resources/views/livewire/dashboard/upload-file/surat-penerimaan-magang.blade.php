<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-white uppercase">Upload Surat Penerimaan Magang</h1>
        <p class="text-sm text-slate-400 mt-1">Kelola dan unggah berkas surat penerimaan magang peserta.</p>
    </div>

    <!-- Flash Message Notifikasi -->
    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
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
                        <th scope="col" class="px-6 py-4">Status File</th>
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
                           <td class="px-6 py-4">
    @php
        $suratFile = $user->files->first(); // sudah difilter di query jadi cuma kategori surat_penerimaan_magang
    @endphp
    <span class="capitalize px-2.5 py-0.5 text-xs font-semibold rounded {{ $suratFile ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
        {{ $suratFile ? 'Sudah Upload' : 'Belum Upload' }}
    </span>
</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <!-- Input File Tersembunyi -->
                                    <input 
                                        type="file"
                                        id="fileInput-{{ $user->user_id }}"
                                        wire:model="files.{{ $user->user_id }}"
                                        class="hidden"
                                    >

                                    <!-- Tombol Upload -->
                                    <button 
                                        type="button"
                                        onclick="document.getElementById('fileInput-{{ $user->user_id }}').click()"
                                        wire:loading.attr="disabled"
                                        wire:target="files.{{ $user->user_id }}"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-500 shadow-md shadow-blue-600/20 disabled:opacity-50 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>

                                        <span wire:loading.remove wire:target="files.{{ $user->user_id }}">Upload File</span>
                                        <span wire:loading wire:target="files.{{ $user->user_id }}">Mengunggah...</span>
                                    </button>
                                </div>

                                @error("files.{$user->user_id}")
                                    <span class="text-rose-400 text-xs mt-1.5 block text-center">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">Tidak ada data ditemukan.</td>
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
</div>