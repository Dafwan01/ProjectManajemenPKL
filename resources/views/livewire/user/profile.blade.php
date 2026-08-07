<div class="w-full mx-auto max-w-4xl space-y-6">

    <!-- Alert / Banner Jika User Sudah Lulus -->
    @if (strtolower($user->status->value ?? $user->status ?? '') === 'lulus')
        <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="text-sm font-medium">
                <span class="font-bold">Status Anda: Lulus!</span> Profil Anda telah dikunci dan tidak dapat diubah kembali.
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="border-b border-gray-200 dark:border-gray-800 pb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-wide">Profil Saya</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola informasi pribadi dan data keikutsertaan program magang Anda.</p>
        </div>

        @if (strtolower($user->status->value ?? $user->status ?? '') !== 'lulus')
            @if (! $editing)
                <button wire:click="startEditing" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-600/20 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Edit Profil
                </button>
            @endif
        @else
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Status: Lulus (Profil Dikunci)
            </span>
        @endif
    </div>
        @if(session()->has('message'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-400 dark:border-emerald-600/50 text-emerald-700 dark:text-emerald-300 text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/20">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <div 
                wire:ignore.self
                x-data="{
                showPhotoModal: false,
                isCameraOn: false,
                photoPreview: null,
                stream: null,

                init() {
                    // Stop camera when page is hidden or about to unload
                    const stopIfHidden = () => {
                        if (document.hidden) {
                            this.stopCamera();
                        }
                    };

                    document.addEventListener('visibilitychange', stopIfHidden);
                    window.addEventListener('pagehide', () => this.stopCamera());
                    window.addEventListener('beforeunload', () => this.stopCamera());
                },

                async initCamera() {
                    this.isCameraOn = true;
                    await this.$nextTick();

                    try {
                        const s = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } } });
                        this.stream = s;
                        if (this.$refs.video) {
                            this.$refs.video.srcObject = s;
                            try {
                                // some browsers require a play() call after setting srcObject
                                await this.$refs.video.play();
                            } catch (e) {
                                // ignore play errors (browser autoplay policies)
                                console.warn('Video play() failed', e);
                            }
                        }
                    } catch (err) {
                        alert('Kamera tidak dapat diakses atau izin ditolak!');
                        this.isCameraOn = false;
                        this.stream = null;
                    }
                },

                takeSnap() {
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const dataUrl = canvas.toDataURL('image/jpeg');

                    this.photoPreview = dataUrl;
                    this.$wire.fotoCaptured = dataUrl;

                    this.stopCamera();
                    this.showPhotoModal = false;
                },

                stopCamera() {
                    this.isCameraOn = false;
                    try {
                        if (this.stream) {
                            this.stream.getTracks().forEach(track => track.stop());
                            this.stream = null;
                        }
                        if (this.$refs.video) {
                            // Clear srcObject to fully release camera on some browsers
                            this.$refs.video.srcObject = null;
                        }
                    } catch (e) {
                        // ignore errors stopping tracks
                        console.error('Error stopping camera tracks', e);
                    }
                },

                openModal() {
                    this.showPhotoModal = true;
                    this.photoPreview = null;
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.photoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    this.showPhotoModal = false;
                },

                closeModal() {
                    this.stopCamera();
                    this.showPhotoModal = false;
                }
            }"
        >
            <!-- Modal Pilihan Foto -->
            <div 
                x-show="showPhotoModal" 
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
            >
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 max-w-sm w-full shadow-2xl space-y-4" @click.outside="closeModal()">
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Ubah Foto Profil</h3>
                        <button type="button" @click="closeModal()" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">✕</button>
                    </div>

                    <!-- Pilihan awal: hanya muncul saat kamera belum aktif -->
    <div x-show="!isCameraOn" x-cloak class="space-y-3">
        <button 
            type="button"
            @click="initCamera()"
            class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold py-3 rounded-xl transition shadow-lg shadow-emerald-600/20"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 011.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
            Ambil Foto via Kamera
        </button>

        <label class="w-full flex items-center justify-center gap-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-sm font-semibold py-3 rounded-xl border border-gray-300 dark:border-gray-700 transition cursor-pointer">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
            Upload dari Perangkat
            <input 
                type="file" 
                wire:model="fotoUpload"
                accept="image/*"
                class="hidden"
                @change="handleFileUpload($event)"
            />
        </label>
    </div>

    <!-- Tampilan kamera aktif: video preview + tombol jepret/batal -->
    <div x-show="isCameraOn" x-cloak class="space-y-3">
        <div class="w-full aspect-square rounded-xl overflow-hidden bg-black">
            <video 
                x-ref="video" 
                autoplay 
                playsinline 
                muted
                class="w-full h-full object-cover scale-x-[-1]"
            ></video>
        </div>

        <button 
            type="button"
            @click="takeSnap()"
            class="w-full bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold py-3 rounded-xl transition shadow-lg shadow-blue-600/20"
        >
            Jepret Foto
        </button>
        <button 
            type="button"
            @click="stopCamera()"
            class="w-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium py-2.5 rounded-xl transition border border-gray-300 dark:border-gray-700"
        >
            Batal
        </button>
    </div>

    <canvas x-ref="canvas" class="hidden"></canvas>
                </div>
            </div>

            @if ($editing)
                <!-- FORM MODE EDIT -->
                <form wire:submit.prevent="saveProfile" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 sm:p-8 space-y-8 shadow-xl">
                    
                    <!-- Avatar Upload Section -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-200 dark:border-gray-800">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-blue-500/30 bg-gray-100 dark:bg-gray-950 shadow-inner">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!photoPreview">
                                    @if ($fotoUpload)
                                        <img src="{{ $fotoUpload->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @else
                                        <img 
                                            src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('images/profile-placeholder.png') }}" 
                                            class="w-full h-full object-cover"
                                        >
                                    @endif
                                </template>
                            </div>
                            <button 
                                type="button"
                                @click="openModal()"
                                class="absolute -bottom-2 -right-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl p-2.5 shadow-lg border border-white dark:border-gray-900 transition"
                                title="Ubah Foto Profil"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 011.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            </button>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Foto Profil</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gunakan foto formal dengan format JPG atau PNG (Maks. 2MB).</p>
                        </div>
                    </div>

                    <!-- Section: Informasi Pribadi -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Informasi Pribadi</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                                <input type="text" wire:model="nama" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                @error('nama') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" wire:model="email" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                @error('email') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Tempat Lahir</label>
                                <input type="text" wire:model="tempat_lahir" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition" placeholder="Misal: Bogor">
                                @error('tempat_lahir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                                <input type="date" wire:model="tanggal_lahir" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                @error('tanggal_lahir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                                <select wire:model="jenis_kelamin" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Akademik & Magang -->
                    <div class="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Akademik & Magang</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div
    x-data="{
        query: '{{ $this->daftarSekolah->firstWhere('sekolah_id', $sekolah_id)?->nama_sekolah ?? '' }}',
        open: false,
        sekolahList: {{ $this->daftarSekolah->map(fn($s) => ['id' => $s->sekolah_id, 'nama' => $s->nama_sekolah])->values()->toJson() }},
        get filtered() {
            if (!this.query) return this.sekolahList;
            return this.sekolahList.filter(s => s.nama.toLowerCase().includes(this.query.toLowerCase()));
        },
        select(s) {
            this.query = s.nama;
            $wire.set('sekolah_id', s.id);
            this.open = false;
        },
        validateOnBlur() {
            // beri jeda supaya event klik pada opsi sempat tereksekusi dulu
            setTimeout(() => {
                const match = this.sekolahList.find(
                    s => s.nama.toLowerCase() === this.query.trim().toLowerCase()
                );
                if (match) {
                    this.query = match.nama;
                    $wire.set('sekolah_id', match.id);
                } else {
                    // tidak cocok dengan list -> reset, jangan simpan teks bebas
                    this.query = '';
                    $wire.set('sekolah_id', null);
                }
                this.open = false;
            }, 150);
        }
    }"
    class="relative"
>
    <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Asal Sekolah / Kampus</label>

    <input
        type="text"
        x-model="query"
        @focus="open = true"
        @input="open = true"
        @blur="validateOnBlur()"
        placeholder="Ketik nama sekolah / kampus..."
        autocomplete="off"
        class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition"
    >

    <div
        x-show="open && filtered.length > 0"
        x-cloak
        @click.outside="open = false"
        class="absolute z-30 mt-1 w-full max-h-52 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xl"
    >
        <template x-for="s in filtered" :key="s.id">
            <div
                @mousedown.prevent="select(s)"
                class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 cursor-pointer"
                x-text="s.nama"
            ></div>
        </template>
    </div>

    <div
        x-show="open && filtered.length === 0"
        x-cloak
        class="absolute z-30 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xl px-4 py-2.5 text-sm text-gray-400 dark:text-gray-500"
    >
        Sekolah tidak ditemukan
    </div>

    @error('sekolah_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
</div>
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Jurusan</label>
                                <input type="text" wire:model="jurusan" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                @error('jurusan') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Bidang & Divisi (Read-only, ditentukan oleh admin) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Bidang <span class="text-gray-400 dark:text-gray-500 font-normal">(Tidak dapat diubah)</span>
                                </label>
                                <input 
                                    type="text" 
                                    value="{{ $this->bidangSaatIni?->nama_bidang ?? '-' }}"
                                    disabled 
                                    readonly
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-200/70 dark:bg-gray-900/80 text-gray-500 dark:text-gray-400 text-sm px-4 py-3 cursor-not-allowed focus:outline-none transition"
                                >
                            </div>

                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Divisi <span class="text-gray-400 dark:text-gray-500 font-normal">(Tidak dapat diubah)</span>
                                </label>
                                <input 
                                    type="text" 
                                    value="{{ $this->divisiSaatIni?->nama_divisi ?? '-' }}"
                                    disabled 
                                    readonly
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-200/70 dark:bg-gray-900/80 text-gray-500 dark:text-gray-400 text-sm px-4 py-3 cursor-not-allowed focus:outline-none transition"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
        <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">
            Nama Mentor <span class="text-gray-400 dark:text-gray-500 font-normal">(Tidak dapat diubah)</span>
        </label>
        <input 
            type="text" 
            wire:model="mentor" 
            disabled 
            readonly
            class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-200/70 dark:bg-gray-900/80 text-gray-500 dark:text-gray-400 text-sm px-4 py-3 cursor-not-allowed focus:outline-none transition"
        >
        @error('mentor') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Mulai Magang</label>
                                <input type="date" wire:model="tanggal_mulai" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                @error('tanggal_mulai') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Selesai Magang</label>
                                <input type="date" wire:model="tanggal_akhir" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                @error('tanggal_akhir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Skill / Keahlian</label>
                            <textarea wire:model="skill" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition" placeholder="Contoh: PHP, Laravel, Tailwind CSS"></textarea>
                            @error('skill') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Section: Keamanan -->
                    <div class="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Ubah Password (Opsional)</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Password Baru -->
                            <div x-data="{ showPassword: false }">
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Password Baru</label>
                                <div class="relative">
                                    <input 
                                        :type="showPassword ? 'text' : 'password'" 
                                        wire:model="password" 
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 pr-10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition" 
                                        placeholder="••••••••"
                                    >
                                    <button 
                                        type="button" 
                                        @click="showPassword = !showPassword" 
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition focus:outline-none"
                                    >
                                        <!-- Icon Mata Terbuka (Muncul saat Password Terlihat) -->
                                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <!-- Icon Mata Tertutup (Muncul saat Password Tersembunyi) -->
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Konfirmasi Password -->
                            <div x-data="{ showConfirmPassword: false }">
                                <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                                <div class="relative">
                                    <input 
                                        :type="showConfirmPassword ? 'text' : 'password'" 
                                        wire:model="confirm_password" 
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 pr-10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition" 
                                        placeholder="••••••••"
                                    >
                                    <button 
                                        type="button" 
                                        @click="showConfirmPassword = !showConfirmPassword" 
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition focus:outline-none"
                                    >
                                        <!-- Icon Mata Terbuka -->
                                        <svg x-show="showConfirmPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <!-- Icon Mata Tertutup -->
                                        <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button wire:click="cancelEditing" type="button" class="rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 px-6 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-600/20 active:scale-95">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            @else
                <!-- VIEW MODE (DASHBOARD CARD STYLE) -->
                @php
                    $sekolahTerpilih = $this->daftarSekolah->firstWhere('sekolah_id', $sekolah_id);
                    $divisiTerpilih = $this->divisiSaatIni;
                    $bidangTerpilih = $this->bidangSaatIni;
                @endphp
                <div class="space-y-6">
                    
                    <!-- Hero Card (Foto + Ringkasan Utama) -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6 shadow-xl relative overflow-hidden">
                        <div class="w-28 h-28 rounded-2xl overflow-hidden border-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-950 flex-shrink-0 shadow-md">
                            <img 
                                src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('images/profile-placeholder.png') }}" 
                                class="w-full h-full object-cover"
                                alt="Foto Profil"
                            >
                        </div>

                        <div class="flex-1 text-center sm:text-left space-y-2">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-wide">{{ $nama }}</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $email }}</p>
                            </div>

                            <div class="flex flex-wrap justify-center sm:justify-start gap-2 pt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-300 dark:border-blue-500/20">
                                    {{ $jurusan ?: 'Jurusan belum diset' }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-700">
                                    {{ $sekolahTerpilih?->nama_sekolah ?? 'Sekolah/Kampus belum diset' }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-300 dark:border-purple-500/20">
                                    {{ $bidangTerpilih?->nama_bidang ?? 'Bidang belum diset' }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-300 dark:border-indigo-500/20">
                                    {{ $divisiTerpilih?->nama_divisi ?? 'Divisi belum diset' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Content Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Card 1: Informasi Biodata -->
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 space-y-4 shadow-xl">
                            <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-3">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Biodata Diri</h3>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Tempat, Tanggal Lahir</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">
                                        {{ ($tempat_lahir || $tanggal_lahir) ? (($tempat_lahir ?: '-') . ', ' . ($tanggal_lahir ?: '-')) : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Jenis Kelamin</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $jenis_kelamin ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Program Magang -->
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 space-y-4 shadow-xl">
                            <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-3">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Detail Magang</h3>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Mentor Pembimbing</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $mentor ?: '-' }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Tanggal Mulai</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $tanggal_mulai ?: '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Tanggal Selesai</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $tanggal_akhir ?: '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Card 3: Bidang & Divisi -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 space-y-4 shadow-xl">
                        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-3">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 6v-3a1 1 0 011-1h2a1 1 0 011 1v3" /></svg>
                            <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Bidang & Divisi</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Bidang</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $bidangTerpilih?->nama_bidang ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Divisi</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5">{{ $divisiTerpilih?->nama_divisi ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Skill Set -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 space-y-4 shadow-xl">
                        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-3">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                            <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Keahlian & Skill</h3>
                        </div>

                        <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed whitespace-pre-line">{{ $skill ?: 'Belum ada keahlian yang ditambahkan.' }}</p>
                    </div>

                </div>
            @endif
        </div>
    </div>