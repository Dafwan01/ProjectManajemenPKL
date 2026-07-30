<div class="w-full mx-auto max-w-4xl">
    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-wide">Upload File</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Unggah dokumen atau file pribadi Anda. Riwayat upload ditampilkan di bawah.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 rounded-xl bg-green-100 dark:bg-green-900/40 border border-green-400 dark:border-green-600/60 text-green-800 dark:text-green-200 text-sm shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-100 dark:bg-red-900/40 border border-red-400 dark:border-red-600/60 text-red-800 dark:text-red-200 text-sm shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Upload -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-3xl p-6 shadow-lg">
        <form wire:submit.prevent="submitDocument" class="space-y-6">
            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Nama File</label>
                <input
                    type="text"
                    wire:model="nama"
                    placeholder="Contoh: Proposal Kerja / Dokumentasi"
                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none"
                />
                @error('nama') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Upload File (Dokumen / Archive)</label>
                <input
                    type="file"
                    wire:model="fileProject"
                    class="w-full text-sm text-gray-700 dark:text-gray-100 file:mr-4 file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-full file:border-0 file:shadow-sm file:hover:bg-blue-500 file:transition cursor-pointer"
                    accept=".zip,.rar,.pdf,.png,.jpg,.jpeg"
                />
                @error('fileProject') <span class="text-red-500 dark:text-red-400 text-xs block mt-2">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Maksimum 50MB. Diperbolehkan: ZIP, RAR, PDF, PNG, JPG.</p>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    type="submit"
                    class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-500/20"
                >
                    Upload File
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Riwayat Upload -->
    <div class="mt-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-3xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Riwayat Upload</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Daftar file pribadi yang sudah Anda simpan.</p>
            </div>
        </div>

        @if($uploadedFiles->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-6 text-center text-gray-500 dark:text-gray-400">
                Belum ada file yang diunggah.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-gray-700 dark:text-gray-200">
                    <thead class="border-b border-gray-200 dark:border-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Nama File</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700/50">
                        @foreach($uploadedFiles as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $item->nama_file ?: '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->file)
                                        <button 
                                            wire:click="openPreviewModal({{ $item->file_id }})" 
                                            title="Lihat Detail & Preview"
                                            class="inline-flex items-center justify-center p-2 rounded-xl bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300 hover:bg-blue-600 hover:text-white border border-gray-300 dark:border-gray-600/50 transition shadow-sm"
                                        >
                                            <!-- Icon Mata -->
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">File Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- MODAL PREVIEW & DOWNLOAD -->
    @if($showModal && $selectedFile)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl w-full max-w-3xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $selectedFile->nama_file }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ekstensi File: <span class="uppercase font-semibold text-blue-600 dark:text-blue-400">{{ $fileExtension }}</span></p>
                    </div>
                    <button wire:click="closePreviewModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Preview Area) -->
                <div class="p-6 overflow-y-auto flex-1 bg-gray-100 dark:bg-gray-900/50 flex flex-col items-center justify-center min-h-[300px]">
                    @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <!-- Preview Gambar -->
                        <img src="{{ $previewUrl }}" alt="Preview Gambar" class="max-h-[60vh] max-w-full rounded-2xl object-contain border border-gray-200 dark:border-gray-700 shadow-md">
                    
                    @elseif($fileExtension === 'pdf')
                        <!-- Preview PDF dengan Google Docs Viewer -->
                        <iframe 
                            src="https://docs.google.com/viewer?url={{ urlencode($previewUrl) }}&embedded=true" 
                            class="w-full h-[60vh] rounded-2xl border border-gray-200 dark:border-gray-700 bg-white"
                            frameborder="0"
                        ></iframe>

                    @else
                        <!-- Preview Tidak Tersedia (ZIP, RAR, dll) -->
                        <div class="text-center p-8 border border-dashed border-gray-300 dark:border-gray-700 rounded-2xl bg-gray-50 dark:bg-gray-800/50 max-w-md">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-300 dark:border-blue-500/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-1">Preview Tidak Tersedia</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Format file ini (<span class="uppercase font-bold text-gray-700 dark:text-gray-300">{{ $fileExtension }}</span>) tidak mendukung pratinjau langsung. Silakan unduh untuk membuka file.</p>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <button 
                        wire:click="closePreviewModal" 
                        class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition"
                    >
                        Tutup
                    </button>
                    <button 
                        wire:click="downloadFile({{ $selectedFile->file_id }})" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-500 text-sm font-semibold transition shadow-lg shadow-blue-500/20"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download File
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>