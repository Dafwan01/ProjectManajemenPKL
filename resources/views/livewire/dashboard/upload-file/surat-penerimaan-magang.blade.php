<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Upload Surat Penerimaan Magang</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola dan unggah berkas surat penerimaan magang peserta.</p>
    </div>

    <!-- Flash Message Notifikasi -->
    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-700 dark:text-emerald-400 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Container Utama -->
    <div class="relative overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl shadow-xl">
        
        <!-- Top Bar / Search Bar -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700/80 gap-4 overflow-x-auto no-scrollbar">
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="block p-2.5 ps-10 text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 rounded-2xl w-64 bg-gray-50 dark:bg-gray-900/50 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                        placeholder="Cari nama atau email..."
                    >
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-900/60 text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700/80">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama</th>
                        <th scope="col" class="px-6 py-4">Asal Sekolah</th>
                        <th scope="col" class="px-6 py-4">Status File</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700/60">
                    @forelse($users as $user)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                            <th scope="row" class="px-6 py-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $user->nama }}
                            </th>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->asal_sekolah }}</td>
                            <td class="px-6 py-4 align-middle">
                                @php
                                    $suratFile = $user->files->first();
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border
                                    {{ $suratFile 
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' 
                                        : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' 
                                    }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $suratFile ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-rose-500 dark:bg-rose-400' }}"></span>
                                    {{ $suratFile ? 'Sudah Upload' : 'Belum Upload' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
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
                                        class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md shadow-blue-600/20 disabled:opacity-50 transition shrink-0"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>

                                        <span wire:loading.remove wire:target="files.{{ $user->user_id }}">
                                            {{ $suratFile ? 'Ganti File' : 'Upload File' }}
                                        </span>
                                        <span wire:loading wire:target="files.{{ $user->user_id }}">Mengunggah...</span>
                                    </button>

                                    <!-- Tombol Lihat Berkas (Muncul jika file sudah diupload) -->
                                    @if($suratFile)
                                        <button 
                                            type="button" 
                                            wire:click="openPreview({{ $user->user_id }})"
                                            class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-2xl transition shrink-0 shadow-sm"
                                        >
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat File
                                        </button>
                                    @endif
                                </div>

                                @error("files.{$user->user_id}")
                                    <div class="text-center mt-1.5">
                                        <span class="text-rose-600 dark:text-rose-400 text-xs font-medium">{{ $message }}</span>
                                    </div>
                                @enderror
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
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

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL PREVIEW BERKAS (PDF / GAMBAR)        -->
    <!-- ========================================== -->
    @if ($showPreviewModal && $previewUrl)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/70 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-5xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col h-[85vh]">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between px-6 py-3.5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                        Surat Penerimaan Magang - {{ $previewUserName }}
                    </h3>
                    <button 
                        wire:click="closePreview" 
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content Area Modal (Otomatis Menyesuaikan Format Berkas) -->
                <div class="flex-1 w-full bg-gray-100 dark:bg-gray-950 p-2 overflow-auto flex items-center justify-center">
                    @if($previewFileType === 'image')
                        <img 
                            src="{{ $previewUrl }}" 
                            alt="Surat Penerimaan Magang {{ $previewUserName }}" 
                            class="max-h-full max-w-full object-contain rounded-lg shadow-md"
                        >
                    @else
                        <iframe 
                            src="{{ $previewUrl }}" 
                            class="w-full h-full border-0 rounded-b-xl"
                        ></iframe>
                    @endif
                </div>

            </div>
        </div>
    @endif

</div>