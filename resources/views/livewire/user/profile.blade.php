<div class="w-full mx-auto max-w-4xl space-y-6">
    <!-- Header Section -->
    <div class="border-b border-gray-200 dark:border-gray-800 pb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-wide">Profil Saya</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola informasi pribadi dan data keikutsertaan program magang Anda.</p>
        </div>
        @if (! $editing)
            <button wire:click="startEditing" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-600/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                Edit Profil
            </button>
        @endif
    </div>

    @if(session()->has('message'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-400 dark:border-emerald-600/50 text-emerald-700 dark:text-emerald-300 text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/20">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div 
        x-data="{
            showPhotoModal: false,
            isCameraOn: false,
            photoPreview: null,

            async initCamera() {
                this.isCameraOn = true;
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } } });
                    this.$refs.video.srcObject = stream;
                } catch (err) {
                    alert('Kamera tidak dapat diakses atau izin ditolak!');
                    this.isCameraOn = false;
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
                if (this.$refs.video && this.$refs.video.srcObject) {
                    this.$refs.video.srcObject.getTracks().forEach(track => track.stop());
                }
            },

            openModal() {
                this.showPhotoModal = true;
                this.photoPreview = null;
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

                <template x-if="!isCameraOn">
                    <div class="space-y-3">
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
                                @change="closeModal()"
                            >
                        </label>

                        @error('fotoUpload') <span class="text-red-500 dark:text-red-400 text-xs block text-center mt-1">{{ $message }}</span> @enderror
                    </div>
                </template>

                <template x-if="isCameraOn">
                    <div class="space-y-3">
                        <div class="w-full aspect-square bg-gray-100 dark:bg-gray-950 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 relative">
                            <video x-ref="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                            <canvas x-ref="canvas" class="hidden"></canvas>
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
                </template>
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
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Asal Sekolah / Kampus</label>
                            <input type="text" wire:model="asal_sekolah" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                            @error('asal_sekolah') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Jurusan</label>
                            <input type="text" wire:model="jurusan" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                            @error('jurusan') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Nama Mentor</label>
                            <input type="text" wire:model="mentor" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
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
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Password Baru</label>
                            <input type="password" wire:model="password" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition" placeholder="••••••••">
                            @error('password') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                            <input type="password" wire:model="confirm_password" class="w-full rounded-xl border border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white text-sm px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition" placeholder="••••••••">
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
                                {{ $asal_sekolah ?: 'Sekolah/Kampus belum diset' }}
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

                <!-- Card 3: Skill Set -->
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