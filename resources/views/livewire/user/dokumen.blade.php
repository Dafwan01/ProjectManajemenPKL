<div class="w-full mx-auto max-w-4xl space-y-6">
    <!-- Header Judul Ringkas -->
    <div class="pb-3 border-b border-slate-200 dark:border-slate-800">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-wide">UNGGAH BERKAS</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Unggah dokumen atau berkas pribadi Anda. Riwayat unggahan dapat dilihat pada bagian bawah.</p>
    </div>

    <!-- Alert Message Sukses -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" class="p-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" class="text-emerald-600 dark:text-emerald-400 hover:text-slate-900 dark:hover:text-white text-xs" @click="show = false">✕</button>
        </div>
    @endif

    <!-- Alert Error Message -->
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" class="p-3 bg-rose-50 dark:bg-rose-950/50 border border-rose-300 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="text-rose-600 dark:text-rose-400 hover:text-slate-900 dark:hover:text-white text-xs" @click="show = false">✕</button>
        </div>
    @endif

    <!-- Alert Peringatan (Status User Lulus) -->
    @if (session()->has('warning'))
        <div x-data="{ show: true }" x-show="show" class="p-3 bg-amber-50 dark:bg-amber-950/50 border border-amber-300 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-xs rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" class="text-amber-600 dark:text-amber-400 hover:text-slate-900 dark:hover:text-white text-xs" @click="show = false">✕</button>
        </div>
    @endif

    <!-- Wadah Formulir Unggah -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form wire:submit.prevent="submitDocument" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Berkas</label>
                <input
                    type="text"
                    wire:model="nama"
                    placeholder="{{ $isLulus ? 'Formulir terkunci karena Anda telah LULUS.' : 'Contoh: Proposal Magang / Laporan Kegiatan' }}"
                    @disabled($isLulus)
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                />
                @error('nama') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">Pilih Berkas (Dokumen / Arsip)</label>
                <input
                    type="file"
                    wire:model="fileProject"
                    @disabled($isLulus)
                    class="w-full text-xs text-slate-700 dark:text-slate-300 file:mr-3 file:bg-blue-600 file:text-white file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:font-semibold file:hover:bg-blue-500 file:transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed file:disabled:opacity-50"
                    accept=".zip,.rar,.pdf,.png,.jpg,.jpeg"
                />
                @error('fileProject') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                
                @if($fileProject)
                    <div class="mt-2.5 rounded-xl border border-emerald-300 dark:border-emerald-800/80 bg-emerald-50 dark:bg-emerald-950/50 p-2.5 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <div>
                            <p class="font-semibold">Berkas siap diunggah</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $fileProject->getClientOriginalName() }} {{ $fileProject->getSize() ? '• ' . round($fileProject->getSize() / 1024, 1) . ' KB' : '' }}</p>
                        </div>
                    </div>
                @endif
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5">Ukuran maksimum 50MB. Format yang diperbolehkan: ZIP, RAR, PDF, PNG, JPG, JPEG.</p>
            </div>

            <div class="flex justify-end pt-2">
                <button
                    type="submit"
                    @disabled($isLulus)
                    class="w-full sm:w-auto text-white font-semibold rounded-xl text-xs px-5 py-2.5 transition flex items-center justify-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed {{ $isLulus ? 'bg-slate-400 dark:bg-slate-700' : 'bg-blue-600 hover:bg-blue-500 focus:ring-2 focus:ring-blue-800' }}"
                >
                    <span wire:loading.remove wire:target="submitDocument">
                        {{ $isLulus ? 'Status Akun Lulus (Formulir Terkunci)' : 'Unggah Berkas' }}
                    </span>
                    <span wire:loading wire:target="submitDocument">Mengunggah...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Section Riwayat Unggahan -->
    <div class="space-y-4 pt-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">Riwayat Unggahan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar berkas pribadi yang telah Anda simpan.</p>
            </div>
            @if($isLulus)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800 text-amber-600 dark:text-amber-400 text-[10px] font-semibold rounded-lg w-fit">
                    🎓 Status: Lulus
                </span>
            @endif
        </div>

        <!-- Filter Riwayat -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Cari Nama Berkas</label>
                <input
                    type="text"
                    wire:model.debounce.500ms="filterName"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Masukkan nama berkas..."
                />
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis Format</label>
                <div class="relative">
                    <select
                        wire:model="filterExtension"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3 py-2 pr-8 focus:ring-2 focus:ring-blue-500 focus:outline-none appearance-none cursor-pointer"
                    >
                        <option value="">Semua Format</option>
                        <option value="pdf">PDF</option>
                        <option value="png">PNG</option>
                        <option value="jpg">JPG</option>
                        <option value="jpeg">JPEG</option>
                        <option value="rar">RAR</option>
                        <option value="zip">ZIP</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Unggah</label>
                <input
                    type="date"
                    wire:model="filterUploadAt"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
            </div>
        </div>

        @if($uploadedFiles->isEmpty())
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center text-slate-400 text-xs">
                <svg class="w-10 h-10 mx-auto mb-3 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Belum ada berkas yang diunggah.
            </div>
        @else
            <!-- 1. MOBILE & TABLET VIEW: Card Grid (Tampil di Layar < lg) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:hidden">
                @foreach($uploadedFiles as $index => $item)
                    @php
                        $ext = $item->file_extension ? strtoupper($item->file_extension) : '-';
                        $extColor = match(strtolower($ext)) {
                            'pdf' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800/60',
                            'zip', 'rar' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800/60',
                            'png', 'jpg', 'jpeg' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/60',
                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                        };
                    @endphp
                    <div wire:key="card-file-{{ $item->file_id ?? $loop->index }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col justify-between gap-3">
                        
                        <!-- Header Card -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                    {{ $index + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-slate-900 dark:text-white truncate leading-snug">
                                        {{ $item->nama_file ?: '-' }}
                                    </h3>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        {{ $item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                    </p>
                                </div>
                            </div>

                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-lg border shrink-0 {{ $extColor }}">
                                {{ $ext }}
                            </span>
                        </div>

                        <!-- Mid Section Info -->
                        <div class="flex items-center justify-between text-xs bg-slate-50 dark:bg-slate-950/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/60">
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">Ukuran Berkas</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $item->file_size_formatted ?: '-' }}</span>
                        </div>

                        <!-- Actions Foot -->
                        <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100 dark:border-slate-800/60">
                            @if($item->file)
                                <button 
                                    wire:click="openPreviewModal({{ $item->file_id }})" 
                                    title="Lihat Detail & Pratinjau"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white border border-slate-200 dark:border-slate-700/60 text-xs font-medium transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Pratinjau</span>
                                </button>

                                <button 
                                    type="button"
                                    wire:click="confirmDelete({{ $item->file_id }})" 
                                    title="Hapus Berkas"
                                    class="inline-flex items-center justify-center p-2 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white border border-rose-200 dark:border-rose-800/60 transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5"/>
                                    </svg>
                                </button>
                            @else
                                <span class="text-xs text-slate-400 dark:text-slate-500 italic">Berkas Kosong</span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- 2. DESKTOP VIEW: Table (Tampil di Layar >= lg) -->
            <div class="hidden lg:block bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-500 dark:text-slate-400">
                        <thead class="bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5 w-12 text-center">NO.</th>
                                <th class="px-4 py-3.5">NAMA BERKAS</th>
                                <th class="px-4 py-3.5">JENIS BERKAS</th>
                                <th class="px-4 py-3.5">UKURAN</th>
                                <th class="px-4 py-3.5">WAKTU UNGGAH</th>
                                <th class="px-4 py-3.5 text-center w-28">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($uploadedFiles as $index => $item)
                                <tr class="bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-4 py-3.5 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3.5 text-slate-900 dark:text-white font-semibold">{{ $item->nama_file ?: '-' }}</td>
                                    <td class="px-4 py-3.5 font-bold text-slate-700 dark:text-slate-300">
                                        {{ $item->file_extension ? strtoupper($item->file_extension) : '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">{{ $item->file_size_formatted ?: '-' }}</td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        {{ $item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($item->file)
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button 
                                                    wire:click="openPreviewModal({{ $item->file_id }})" 
                                                    title="Lihat Detail & Pratinjau"
                                                    class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-blue-600 hover:text-white border border-slate-200 dark:border-slate-700 transition"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>

                                                <button 
                                                    type="button"
                                                    wire:click="confirmDelete({{ $item->file_id }})" 
                                                    title="Hapus Berkas"
                                                    class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white border border-rose-200 dark:border-rose-800/60 transition"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">Berkas Kosong</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

     <div class="mt-4">
        {{ $uploadedFiles->links() }}
    </div>
    
    <!-- MODAL PRATINJAU & UNDUH -->
    @if($showModal && $selectedFile)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/70 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-5xl overflow-hidden shadow-xl flex flex-col h-[90vh]">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-800 shrink-0">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-tight truncate max-w-md sm:max-w-xl">{{ $selectedFile->nama_file }}</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Ekstensi Berkas: <span class="uppercase font-semibold text-blue-600 dark:text-blue-400">{{ $fileExtension }}</span></p>
                    </div>
                    <button wire:click="closePreviewModal" class="text-slate-400 hover:text-slate-900 dark:hover:text-white p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Isi Modal (Area Pratinjau) -->
                <div class="p-2 sm:p-3 overflow-hidden flex-1 bg-slate-50 dark:bg-slate-950/60 flex flex-col items-center justify-center min-h-0">
                    @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <!-- Pratinjau Gambar -->
                        <img src="{{ $previewUrl }}" alt="Pratinjau Gambar" class="max-h-full max-w-full rounded-xl object-contain border border-slate-200 dark:border-slate-800 shadow-sm">
                    
                    @elseif($fileExtension === 'pdf')
                        <!-- Pratinjau PDF -->
                        <iframe 
                            src="{{ $previewUrl }}" 
                            class="w-full h-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white"
                        ></iframe>

                    @else
                        <!-- Pratinjau Tidak Tersedia (ZIP, RAR, dll) -->
                        <div class="text-center p-6 border border-dashed border-slate-300 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 max-w-sm">
                            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mx-auto mb-3 border border-blue-200 dark:border-blue-900/60">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white mb-1">Pratinjau Tidak Tersedia</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Format berkas ini (<span class="uppercase font-bold text-slate-700 dark:text-slate-300">{{ $fileExtension }}</span>) tidak mendukung pratinjau langsung. Silakan unduh berkas untuk membukanya.</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Modal -->
                <div class="flex items-center justify-end gap-2 px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0">
                    <button 
                        wire:click="closePreviewModal" 
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-medium transition"
                    >
                        Tutup
                    </button>
                    <button 
                        wire:click="downloadFile({{ $selectedFile->file_id }})" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-500 text-xs font-semibold transition shadow-sm"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Konfirmasi Hapus Berkas</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Apakah Anda yakin ingin menghapus berkas ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="px-5 py-4">
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="cancelDelete"
                            class="rounded-xl px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-medium transition"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="deleteFile"
                            class="rounded-xl px-4 py-2 bg-rose-600 text-white hover:bg-rose-500 text-xs font-semibold transition"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>