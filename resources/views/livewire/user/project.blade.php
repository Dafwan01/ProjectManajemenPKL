<div class="max-w-4xl mx-auto py-6 px-4">
    <!-- Alert Notifikasi (Message / Success) -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/80 border border-green-400 dark:border-green-500 text-green-800 dark:text-green-200 rounded-xl flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" class="text-green-600 dark:text-green-300 hover:text-gray-900 dark:hover:text-white" @click="$el.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Alert Notifikasi (Warning / Lulus) -->
    @if (session()->has('warning'))
        <div class="mb-6 p-4 bg-yellow-100 dark:bg-yellow-900/80 border border-yellow-400 dark:border-yellow-500 text-yellow-800 dark:text-yellow-200 rounded-xl flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500 dark:text-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" class="text-yellow-600 dark:text-yellow-300 hover:text-gray-900 dark:hover:text-white" @click="$el.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Card Form Project Akhir -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    Form Project Akhir
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Unggah laporan/berkas project akhir dan link repositori GitHub kamu.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if ($isLulus)
                    <span class="px-3 py-1 bg-amber-50 dark:bg-amber-500/10 border border-amber-400 dark:border-amber-500 text-amber-600 dark:text-amber-400 text-xs font-medium rounded-full">
                        🎓 Status: Lulus
                    </span>
                @elseif ($sudahUpload)
                    <span class="px-3 py-1 bg-green-50 dark:bg-green-500/10 border border-green-400 dark:border-green-500 text-green-600 dark:text-green-400 text-xs font-medium rounded-full">
                        ✓ Sudah Mengirim
                    </span>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="simpanProject" class="space-y-5">
            <!-- Nama Project -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nama Project / Laporan <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    wire:model.defer="nama_project" 
                    placeholder="{{ $isLulus ? 'Formulir terkunci karena Anda telah LULUS.' : 'Contoh: Sistem Informasi Presensi berbasis Laravel' }}" 
                    @disabled($isLulus)
                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:border-purple-500 focus:outline-none text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                @error('nama_project') 
                    <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Link GitHub -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Link Repository GitHub (Opsional)
                </label>
                <input 
                    type="url" 
                    wire:model.defer="link_github" 
                    placeholder="https://github.com/username/repository" 
                    @disabled($isLulus)
                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:border-purple-500 focus:outline-none text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                @error('link_github') 
                    <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Upload File Project -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    File Project / Laporan (ZIP, RAR, PDF, DOCX - Max 20MB) 
                    @if (!$existing_file) <span class="text-red-500">*</span> @endif
                </label>
                
                <input 
                    type="file" 
                    wire:model="file_project" 
                    @disabled($isLulus)
                    class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-300 dark:border-gray-700 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed file:disabled:opacity-50 file:disabled:cursor-not-allowed">

                <!-- Loading Indicator -->
                <div wire:loading wire:target="file_project" class="text-xs text-purple-600 dark:text-purple-400 mt-2">
                    Mengunggah berkas... Mohon tunggu.
                </div>

                @error('file_project') 
                    <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> 
                @enderror

                <!-- Anggota Tim (Jika Project Kelompok) -->
                <div wire:ignore.self x-data="{}" class="mt-4">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Anggota Tim Lain (Opsional)
                        </label>
                        <button 
                            type="button"
                            wire:click="openAnggotaModal"
                            @disabled($isLulus)
                            class="w-7 h-7 flex items-center justify-center rounded-full bg-purple-600 hover:bg-purple-700 text-white shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Tambah Anggota Tim"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Pilih anak PKL lain yang mengerjakan project ini bersama kamu. Nama project, link GitHub, dan file laporan otomatis tersalin ke akun mereka.
                    </p>

                    <!-- Chip / Daftar Anggota Terpilih -->
                    @if (count($anggotaLain) > 0)
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach ($this->anggotaTerpilihDetail as $anggota)
                                <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-full pl-1.5 pr-2.5 py-1">
                                    <img 
                                        src="{{ $anggota->foto ? asset('storage/' . $anggota->foto) : asset('images/profile-placeholder.png') }}" 
                                        class="w-6 h-6 rounded-full object-cover border border-gray-200 dark:border-gray-700"
                                        alt="{{ $anggota->nama }}"
                                    >
                                    <span class="text-xs text-gray-700 dark:text-gray-200 font-medium">{{ $anggota->nama }}</span>
                                    @unless($isLulus)
                                        <button type="button" wire:click="hapusAnggota({{ $anggota->user_id }})" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endunless
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 dark:text-gray-500 italic mb-2">Belum ada anggota tim yang ditambahkan.</p>
                    @endif

                    @error('anggotaLain') 
                        <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Informasi File Lama -->
                @if ($existing_file)
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/80 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 text-green-500 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>File Ter-upload: <strong class="text-purple-600 dark:text-purple-300">{{ basename($existing_file) }}</strong></span>
                        </div>
                        <a href="{{ Storage::url($existing_file) }}" target="_blank" class="text-xs text-purple-600 dark:text-purple-400 hover:underline font-semibold">
                            Unduh / Lihat File
                        </a>
                    </div>
                @endif
            </div>

            <!-- Tombol Submit -->
            <button 
                type="submit" 
                wire:loading.attr="disabled" 
                @disabled($isLulus)
                class="w-full py-3 text-white font-semibold rounded-xl transition shadow-lg flex items-center justify-center gap-2 {{ $isLulus ? 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed opacity-60' : 'bg-purple-600 hover:bg-purple-700' }}">
                
                @if ($isLulus)
                    <span>Status Akun Lulus (Form Terkunci)</span>
                @else
                    <span wire:loading.remove wire:target="simpanProject">
                        {{ $sudahUpload ? 'Perbarui Project Akhir' : 'Kirim Project Akhir' }}
                    </span>
                    <span wire:loading wire:target="simpanProject">
                        Memproses...
                    </span>
                @endif
            </button>
        </form>
    </div>

    <!-- MODAL PILIH ANGGOTA TIM -->
    @if ($showAnggotaModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col max-h-[85vh]">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-800/50 shrink-0">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3v-1a4 4 0 00-3-3.87m-9-2.13a4 4 0 118 0"/>
                        </svg>
                        Pilih Anggota Tim
                    </h3>
                    <button type="button" wire:click="closeAnggotaModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-white p-1 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-800 shrink-0">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchAnggota"
                            placeholder="Cari nama atau asal sekolah..."
                            class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 text-gray-900 dark:text-white text-sm rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition"
                            autofocus
                        >
                    </div>
                </div>

                <!-- List User -->
                <div class="overflow-y-auto flex-1 p-3 space-y-1">
                    @forelse ($this->hasilPencarianAnggota as $anggota)
                        @php $sudahDipilih = in_array($anggota->user_id, $anggotaLain); @endphp
                        <button 
                            type="button"
                            wire:click="toggleAnggota({{ $anggota->user_id }})"
                            class="w-full flex items-center gap-3 p-2.5 rounded-xl transition text-left {{ $sudahDipilih ? 'bg-purple-50 dark:bg-purple-500/10 border border-purple-300 dark:border-purple-500/30' : 'hover:bg-gray-50 dark:hover:bg-gray-800/60 border border-transparent' }}"
                        >
                            <img 
                                src="{{ $anggota->foto ? asset('storage/' . $anggota->foto) : asset('images/profile-placeholder.png') }}" 
                                class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-700 shrink-0"
                                alt="{{ $anggota->nama }}"
                            >
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $anggota->nama }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $anggota->asal_sekolah ?? '-' }}</p>
                            </div>
                            @if ($sudahDipilih)
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </button>
                    @empty
                        <p class="text-center text-xs text-gray-400 dark:text-gray-500 py-8">Tidak ada user yang ditemukan.</p>
                    @endforelse
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between gap-3 p-4 border-t border-gray-200 dark:border-gray-800 shrink-0">
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($anggotaLain) }} dipilih</span>
                    <button 
                        type="button"
                        wire:click="closeAnggotaModal"
                        class="px-4 py-2 text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow-md transition"
                    >
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>