<div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
    
    <!-- Container Modal dengan Batas Tinggi Max 90vh -->
    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 p-6 flex flex-col max-h-[90vh] my-auto" @click.stop>
        
        <!-- Header Modal (Tetap di Atas) -->
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-4 shrink-0">
            <div>
                <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Data Pengguna' : 'Buat Pengguna Baru' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isEditMode ? 'Perbarui data profil dan jadwal magang pengguna.' : 'Isi data di bawah ini untuk mendaftarkan pengguna baru.' }}
                </p>
            </div>
            
            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-xl p-2 transition hover:bg-gray-100 dark:hover:bg-gray-800 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        @php
            $isMentorUser = auth()->user()->role === \App\Enums\UserRole::MENTOR || auth()->user()->role?->value === \App\Enums\UserRole::MENTOR->value;
        @endphp

        <!-- Body Form (Bisa di-scroll jika konten panjang) -->
        <form wire:submit.prevent="save" class="space-y-4 overflow-y-auto pr-1 pt-4 my-2 flex-1 scrollbar-thin">
            
            <!-- Baris 1: Nama Lengkap & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        id="nama" 
                        wire:model="nama" 
                        placeholder="Masukkan nama lengkap" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('nama') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('nama') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Alamat Email <span class="text-red-500">*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        wire:model="email" 
                        placeholder="nama@email.com" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('email') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('email') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris Role Akses -->
            <div>
                <label for="role" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Role Akses <span class="text-red-500">*</span></label>
                <select
                    id="role"
                    wire:model.live="role"
                    class="bg-gray-50 dark:bg-gray-800/60 border @error('role') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    @if(count($this->availableRoles) === 1) disabled @endif
                >
                    @if(count($this->availableRoles) > 1)
                        <option value="">-- Pilih Role --</option>
                    @endif

                    @foreach($this->availableRoles as $roleEnum)
                        <option value="{{ $roleEnum->value }}">{{ $roleEnum->label() }}</option>
                    @endforeach
                </select>
                @error('role') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Baris 2: Tempat Lahir & Tanggal Lahir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tempat_lahir" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tempat Lahir</label>
                    <input 
                        type="text" 
                        id="tempat_lahir" 
                        wire:model="tempat_lahir" 
                        placeholder="Contoh: Jakarta" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('tempat_lahir') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('tempat_lahir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tanggal_lahir" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal Lahir</label>
                    <input 
                        type="date" 
                        id="tanggal_lahir" 
                        wire:model="tanggal_lahir" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('tanggal_lahir') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                    >
                    @error('tanggal_lahir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 3: Jenis Kelamin & Jurusan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jenis_kelamin" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jenis Kelamin</label>
                    <select 
                        id="jenis_kelamin" 
                        wire:model="jenis_kelamin" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('jenis_kelamin') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                    >
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="laki-laki">Laki-laki</option>
                        <option value="perempuan">Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="jurusan" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jurusan / Program Studi</label>
                    <input 
                        type="text" 
                        id="jurusan" 
                        wire:model="jurusan" 
                        placeholder="Contoh: Teknik Informatika / RPL" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('jurusan') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('jurusan') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 4: Bidang & Divisi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="bidang_id" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Bidang <span class="text-red-500">*</span></label>
                    <select 
                        id="bidang_id" 
                        wire:model.live="bidang_id" 
                        @disabled($isMentorUser)
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('bidang_id') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <option value="">-- Pilih Bidang --</option>
                        @foreach($this->daftarBidang as $bidang)
                            <option value="{{ $bidang->bidang_id }}">{{ $bidang->nama_bidang }}</option>
                        @endforeach
                    </select>
                    @if($isMentorUser)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">*Otomatis disesuaikan dengan bidang Anda sebagai Mentor.</p>
                    @endif
                    @error('bidang_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="divisi_id" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Divisi <span class="text-red-500">*</span></label>
                    <select 
                        id="divisi_id" 
                        wire:model="divisi_id" 
                        @disabled($isMentorUser || empty($bidang_id))
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('divisi_id') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($this->daftarDivisi as $divisi)
                            <option value="{{ $divisi->divisi_id }}">{{ $divisi->nama_divisi }}</option>
                        @endforeach
                    </select>
                    @if($isMentorUser)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">*Otomatis disesuaikan dengan divisi Anda sebagai Mentor.</p>
                    @elseif(empty($bidang_id))
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">*Pilih Bidang terlebih dahulu.</p>
                    @endif
                    @error('divisi_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 5: Asal Sekolah (Dropdown + Search) & Mentor -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Field Asal Sekolah -->
                <div 
                    x-data="{
                        open: false,
                        tambahBaru: @entangle('tambahSekolahBaru'),
                        namaBaru: @entangle('namaSekolahBaru'),
                        search: @entangle('searchSekolah').live,

                        enableTambahBaru() {
                            $wire.set('sekolah_id', null);
                            this.tambahBaru = true;
                            this.namaBaru = this.search;
                            this.open = false;
                        },

                        batalTambahBaru() {
                            this.tambahBaru = false;
                            this.namaBaru = '';
                            this.search = '';
                            $wire.set('sekolah_id', null);
                        }
                    }"
                    class="relative"
                >
                    <label class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Asal Sekolah / Universitas
                    </label>

                  <div class="relative" @click.outside="open = false">
    <button
        type="button"
        @click="open = !open; if (open) { $nextTick(() => $refs.searchSekolahInput.focus()) }"
        class="w-full flex items-center justify-between bg-gray-50 dark:bg-gray-800/60 border @error('sekolah_id') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 sm:p-3 transition disabled:opacity-60 disabled:cursor-not-allowed text-left"
    >
       <span x-show="!tambahBaru" class="truncate {{ !$sekolah_id ? 'text-gray-400 dark:text-gray-500' : '' }}">
    {{ $sekolah_id && $this->sekolahTerpilih ? $this->sekolahTerpilih->nama_sekolah : '-- Pilih Sekolah --' }}
</span>
<span x-show="tambahBaru" x-cloak x-text="namaBaru" class="truncate text-emerald-600 dark:text-emerald-400 font-medium"></span> 
        <svg class="w-4 h-4 shrink-0 text-gray-400 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Panel Dropdown -->
    <div 
        x-show="open" 
        x-cloak
        class="absolute z-30 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden"
    >
        <!-- Search bar di dalam panel -->
        <div class="p-2 border-b border-gray-100 dark:border-gray-700/60">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-ref="searchSekolahInput"
                    x-model="search"
                    autocomplete="off"
                    placeholder="Cari nama sekolah..." 
                    class="w-full bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 pl-9 transition placeholder-gray-400 dark:placeholder-gray-500"
                >
            </div>
        </div>

        <!-- Daftar Opsi -->
        <div class="max-h-48 overflow-y-auto py-1 scrollbar-thin">
            @forelse($this->daftarSekolah as $sekolah)
                <div class="flex items-center gap-1 px-1.5 hover:bg-blue-50 dark:hover:bg-blue-900/40 rounded-xl mx-1 transition group">
                    <button 
                        type="button"
                        wire:click="pilihSekolah({{ $sekolah->sekolah_id }}, '{{ addslashes($sekolah->nama_sekolah) }}')"
                        @click="tambahBaru = false; namaBaru = ''; open = false"
                        class="flex-1 min-w-0 text-left px-2.5 py-2.5 text-sm text-gray-700 dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition flex items-center justify-between gap-2"
                    >
                        <span class="truncate">{{ $sekolah->nama_sekolah }}</span>
                        @if($sekolah_id == $sekolah->sekolah_id)
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </button>

                    <!-- Tombol Hapus per-item -->
                    <button
                        type="button"
                        wire:click.stop="confirmHapusSekolah({{ $sekolah->sekolah_id }}, '{{ addslashes($sekolah->nama_sekolah) }}')"
                        @click.stop
                        title="Hapus sekolah ini dari sistem"
                        class="shrink-0 p-1.5 text-gray-400 hover:text-white hover:bg-red-500 dark:hover:bg-red-600 rounded-lg transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @empty
                <div class="px-4 py-2 text-xs text-gray-400 dark:text-gray-500 italic">
                    Sekolah tidak ditemukan.
                </div>
            @endforelse

            <!-- Opsi Tambah Baru -->
            <div 
                x-show="search.trim().length >= 3"
                @mousedown.prevent="enableTambahBaru()"
                class="px-4 py-2.5 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-medium cursor-pointer border-t border-gray-100 dark:border-gray-700/60 flex items-center gap-2 text-xs transition"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="truncate">Tambah "<strong x-text="search"></strong>" sebagai Sekolah Baru</span>
            </div>
        </div>
    </div>
</div>

                    <!-- Indikator mode Sekolah Baru diset -->
                    <template x-if="tambahBaru">
                        <div class="mt-2 text-xs text-blue-600 dark:text-blue-400 flex items-center justify-between bg-blue-50 dark:bg-blue-950/40 px-3 py-2 rounded-xl border border-blue-200 dark:border-blue-800/60">
                            <span class="truncate">Menambahkan: <strong x-text="namaBaru"></strong></span>
                            <button type="button" @click="batalTambahBaru()" class="text-red-500 hover:text-red-600 font-semibold ml-2 shrink-0 transition">Batal</button>
                        </div>
                    </template>

                    @error('sekolah_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    @error('namaSekolahBaru') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Dropdown Mentor -->
                <div>
                    <label for="mentor" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Mentor</label>
                    <select 
                        id="mentor" 
                        wire:model="mentor" 
                        @disabled($isMentorUser)
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('mentor') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <option value="">-- Pilih Mentor --</option>
                        @foreach($mentors as $m)
                            <option value="{{ $m->nama }}">{{ $m->nama }}</option>
                        @endforeach
                    </select>
                    @if($isMentorUser)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">*Sebagai Mentor, Anda otomatis menjadi pembimbing pengguna ini.</p>
                    @endif
                    @error('mentor') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 6: Tanggal Mulai & Tanggal Akhir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal Mulai Magang</label>
                    <input 
                        type="date" 
                        id="tanggal_mulai" 
                        wire:model="tanggal_mulai" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('tanggal_mulai') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                    >
                    @error('tanggal_mulai') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tanggal_akhir" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal Akhir Magang</label>
                    <input 
                        type="date" 
                        id="tanggal_akhir" 
                        wire:model="tanggal_akhir" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('tanggal_akhir') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                    >
                    @error('tanggal_akhir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 7: Skill -->
            <div>
                <label for="skill" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Skill / Keahlian</label>
                <textarea 
                    id="skill" 
                    wire:model="skill" 
                    rows="3" 
                    placeholder="Tuliskan skill pengguna, misalnya PHP, Laravel, Tailwind, dll." 
                    class="bg-gray-50 dark:bg-gray-800/60 border @error('skill') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                ></textarea>
                @error('skill') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            @if($isEditMode)
                <!-- Status Akun (Hanya saat Mode Edit) -->
                <div>
                    <label for="status" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status Akun <span class="text-red-500">*</span></label>
                    <select 
                        id="status" 
                        wire:model="status" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('status') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                    >
                        @foreach($this->daftarStatus as $statusEnum)
                            <option value="{{ $statusEnum->value }}">{{ $statusEnum->label() }}</option>
                        @endforeach
                    </select>
                    @error('status') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Baris Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100 dark:border-gray-800/80">
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Password @if(!$isEditMode)<span class="text-red-500">*</span>@else <span class="text-gray-400 text-[10px] lowercase font-normal">(opsional)</span> @endif
                    </label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            wire:model="password" 
                            placeholder="Min. 8 karakter" 
                            class="bg-gray-50 dark:bg-gray-800/60 border @error('password') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 transition placeholder-gray-400 dark:placeholder-gray-500"
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition focus:outline-none"
                        >
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div x-data="{ showConfirmPassword: false }">
                    <label for="confirm-password" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input 
                            :type="showConfirmPassword ? 'text' : 'password'" 
                            id="confirm-password" 
                            wire:model="confirm_password" 
                            placeholder="Ulangi password" 
                            class="bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 transition placeholder-gray-400 dark:placeholder-gray-500"
                        >
                        <button 
                            type="button" 
                            @click="showConfirmPassword = !showConfirmPassword" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition focus:outline-none"
                        >
                            <svg x-show="showConfirmPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi (Tetap di Bawah) -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800 shrink-0">
                <button 
                    type="button" 
                    wire:click="closeModal" 
                    class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition"
                >
                    Batal
                </button>

                <button 
                    type="submit" 
                    wire:loading.attr="disabled" 
                    class="px-5 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md shadow-blue-600/20 transition disabled:opacity-50"
                >
                    <span wire:loading.remove>{{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Data' }}</span>
                    <span wire:loading>Proses Menyimpan...</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Popup Konfirmasi Hapus Sekolah -->
@if($showDeleteSekolahConfirm)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/70 dark:bg-black/80 backdrop-blur-sm p-4">
        <div class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Hapus Data Sekolah?</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Yakin ingin menghapus sekolah <strong>"{{ $namaSekolahToDelete }}"</strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button 
                    type="button" 
                    wire:click="batalHapusSekolah" 
                    class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    wire:click="hapusSekolah" 
                    wire:loading.attr="disabled"
                    class="px-5 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-500 rounded-2xl shadow-md shadow-red-600/20 transition disabled:opacity-60"
                >
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
@endif