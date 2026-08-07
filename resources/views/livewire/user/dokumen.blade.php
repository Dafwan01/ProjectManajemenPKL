<div class="w-full mx-auto max-w-4xl">
    <!-- Header Judul -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-wide">Unggah Berkas</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Unggah dokumen atau berkas pribadi Anda. Riwayat unggahan dapat dilihat pada tabel di bawah ini.</p>
    </div>

    <!-- Alert Message Sukses -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 rounded-xl bg-green-100 dark:bg-green-900/40 border border-green-400 dark:border-green-600/60 text-green-800 dark:text-green-200 text-sm shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" class="text-green-600 dark:text-green-300 hover:text-gray-900 dark:hover:text-white" @click="$el.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Alert Error Message -->
    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-100 dark:bg-red-900/40 border border-red-400 dark:border-red-600/60 text-red-800 dark:text-red-200 text-sm shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="text-red-600 dark:text-red-300 hover:text-gray-900 dark:hover:text-white" @click="$el.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Alert Peringatan (Status User Lulus) -->
    @if (session()->has('warning'))
        <div class="mb-4 p-4 rounded-xl bg-amber-100 dark:bg-amber-900/40 border border-amber-400 dark:border-amber-600/60 text-amber-800 dark:text-amber-200 text-sm shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" class="text-amber-600 dark:text-amber-300 hover:text-gray-900 dark:hover:text-white" @click="$el.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Formulir Unggah -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-3xl p-6 shadow-lg">
        <form wire:submit.prevent="submitDocument" class="space-y-6">
            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Nama Berkas</label>
                <input
                    type="text"
                    wire:model="nama"
                    placeholder="{{ $isLulus ? 'Formulir terkunci karena Anda telah LULUS.' : 'Contoh: Proposal Magang / Laporan Kegiatan' }}"
                    @disabled($isLulus)
                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                />
                @error('nama') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Pilih Berkas (Dokumen / Arsip)</label>
                <input
                    type="file"
                    wire:model="fileProject"
                    @disabled($isLulus)
                    class="w-full text-sm text-gray-700 dark:text-gray-100 file:mr-4 file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-full file:border-0 file:shadow-sm file:hover:bg-blue-500 file:transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed file:disabled:opacity-50 file:disabled:cursor-not-allowed"
                    accept=".zip,.rar,.pdf,.png,.jpg,.jpeg"
                />
                @error('fileProject') <span class="text-red-500 dark:text-red-400 text-xs block mt-2">{{ $message }}</span> @enderror
                @if($fileProject)
                    <div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/40 p-3 text-emerald-700 dark:text-emerald-200 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <div>
                            <p class="font-semibold">Berkas siap diunggah</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">{{ $fileProject->getClientOriginalName() }} {{ $fileProject->getSize() ? '• ' . round($fileProject->getSize() / 1024, 1) . ' KB' : '' }}</p>
                        </div>
                    </div>
                @endif
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Ukuran maksimum 50MB. Format yang diperbolehkan: ZIP, RAR, PDF, PNG, JPG, JPEG.</p>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    type="submit"
                    @disabled($isLulus)
                    class="rounded-2xl px-6 py-3 text-sm font-semibold text-white transition shadow-lg {{ $isLulus ? 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed opacity-60' : 'bg-blue-600 hover:bg-blue-500 shadow-blue-500/20' }}"
                >
                    {{ $isLulus ? 'Status Akun Lulus (Formulir Terkunci)' : 'Unggah Berkas' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Riwayat Unggahan -->
    <div class="mt-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-3xl p-6 shadow-lg">
        <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Riwayat Unggahan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Daftar berkas pribadi yang telah Anda simpan.</p>
            </div>
            @if($isLulus)
                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-500/10 border border-amber-400 dark:border-amber-500 text-amber-600 dark:text-amber-400 text-xs font-medium rounded-full">
                    🎓 Status: Lulus
                </span>
            @endif
        </div>

        <!-- Filter Riwayat -->
        <div class="grid gap-4 sm:grid-cols-3 mb-6">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Cari Nama Berkas</label>
                <input
                    type="text"
                    wire:model.debounce.500ms="filterName"
                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none"
                    placeholder="Masukkan nama berkas..."
                />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Jenis Format</label>
                <select
                    wire:model="filterExtension"
                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none"
                >
                    <option value="">Semua Format</option>
                    <option value="pdf">PDF</option>
                    <option value="png">PNG</option>
                    <option value="jpg">JPG</option>
                    <option value="jpeg">JPEG</option>
                    <option value="rar">RAR</option>
                    <option value="zip">ZIP</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Tanggal Unggah</label>
                <input
                    type="date"
                    wire:model="filterUploadAt"
                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none"
                />
            </div>
        </div>

        @if($uploadedFiles->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-6 text-center text-gray-500 dark:text-gray-400 text-xs">
                Belum ada berkas yang diunggah.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-gray-700 dark:text-gray-200">
                    <thead class="border-b border-gray-200 dark:border-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">No.</th>
                            <th class="px-4 py-3">Nama Berkas</th>
                            <th class="px-4 py-3">Jenis Berkas</th>
                            <th class="px-4 py-3">Ukuran</th>
                            <th class="px-4 py-3">Waktu Unggah</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700/50">
                        @foreach($uploadedFiles as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $item->nama_file ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->file_extension ? strtoupper($item->file_extension) : '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->file_size_formatted ?: '-' }}</td>
                                
                                <!-- Format Waktu Indonesia Baku (contoh: 24 Okt 2023, 14:30) -->
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    {{ $item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                </td>

                                <td class="px-4 py-3 text-center space-x-2">
                                    @if($item->file)
                                        <button 
                                            wire:click="openPreviewModal({{ $item->file_id }})" 
                                            title="Lihat Detail & Pratinjau"
                                            class="inline-flex items-center justify-center p-2 rounded-xl bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300 hover:bg-blue-600 hover:text-white border border-gray-300 dark:border-gray-600/50 transition shadow-sm"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <button 
                                            type="button"
                                            wire:click="confirmDelete({{ $item->file_id }})" 
                                            title="Hapus Berkas"
                                            class="inline-flex items-center justify-center p-2 rounded-xl bg-red-100 dark:bg-red-700/60 text-red-600 dark:text-red-200 hover:bg-red-500 hover:text-white border border-red-300 dark:border-red-600/50 transition shadow-sm"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5"/>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Berkas Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- MODAL PRATINJAU & UNDUH -->
    @if($showModal && $selectedFile)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-3 bg-black/70 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-3xl w-full max-w-6xl lg:max-w-7xl overflow-hidden shadow-2xl flex flex-col h-[94vh]">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">{{ $selectedFile->nama_file }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ekstensi Berkas: <span class="uppercase font-semibold text-blue-600 dark:text-blue-400">{{ $fileExtension }}</span></p>
                    </div>
                    <button wire:click="closePreviewModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Isi Modal (Area Pratinjau) -->
                <div class="p-1 sm:p-2 overflow-hidden flex-1 bg-gray-100 dark:bg-gray-900/50 flex flex-col items-center justify-center min-h-0">
                    @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <!-- Pratinjau Gambar -->
                        <img src="{{ $previewUrl }}" alt="Pratinjau Gambar" class="max-h-full max-w-full rounded-2xl object-contain border border-gray-200 dark:border-gray-700 shadow-md">
                    
                    @elseif($fileExtension === 'pdf')
                        <!-- Pratinjau PDF -->
                        <iframe 
                            src="{{ $previewUrl }}" 
                            class="w-full h-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white"
                        ></iframe>

                    @else
                        <!-- Pratinjau Tidak Tersedia (ZIP, RAR, dll) -->
                        <div class="text-center p-8 border border-dashed border-gray-300 dark:border-gray-700 rounded-2xl bg-gray-50 dark:bg-gray-800/50 max-w-md">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-300 dark:border-blue-500/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-1">Pratinjau Tidak Tersedia</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Format berkas ini (<span class="uppercase font-bold text-gray-700 dark:text-gray-300">{{ $fileExtension }}</span>) tidak mendukung pratinjau langsung. Silakan unduh berkas untuk membukanya.</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Modal -->
                <div class="flex items-center justify-end gap-3 px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shrink-0">
                    <button 
                        wire:click="closePreviewModal" 
                        class="px-5 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition"
                    >
                        Tutup
                    </button>
                    <button 
                        wire:click="downloadFile({{ $selectedFile->file_id }})" 
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-500 text-sm font-semibold transition shadow-lg shadow-blue-500/20"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh Berkas
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS -->
    @if($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="w-full max-w-lg rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi Hapus Berkas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Apakah Anda yakin ingin menghapus berkas ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="px-6 py-5">
                    <div class="text-sm text-gray-700 dark:text-gray-200 mb-4">
                        Pilih tombol "Hapus" untuk melanjutkan penghapusan atau "Batal" untuk membatalkannya.
                    </div>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click="cancelDelete"
                            class="rounded-2xl px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="deleteFile"
                            class="rounded-2xl px-4 py-2 bg-red-600 text-white hover:bg-red-500 transition"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
