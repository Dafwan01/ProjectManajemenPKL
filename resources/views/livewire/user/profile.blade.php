<div class="w-full mx-auto max-w-4xl">
    <div class="mb-6 border-b border-gray-800 pb-4 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Profil Saya</h1>
            <p class="text-sm text-gray-400 mt-1">Lihat ringkasan data akun Anda. Gunakan tombol edit untuk mengubah informasi profil.</p>
        </div>
        @if (! $editing)
            <button wire:click="startEditing" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-500/20">
                Edit Profil
            </button>
        @endif
    </div>

    @if(session()->has('message'))
        <div class="mb-4 p-4 rounded-xl bg-green-900/40 border border-green-600/60 text-green-200 text-sm shadow-sm">
            {{ session('message') }}
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
        class="bg-gray-800 border border-gray-700/60 rounded-3xl p-6 shadow-lg"
    >
        <!-- Modal Pilihan Foto -->
        <div 
            x-show="showPhotoModal" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
        >
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 max-w-sm w-full shadow-2xl" @click.outside="closeModal()">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-white">Ubah Foto Profil</h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-white">✕</button>
                </div>

                <template x-if="!isCameraOn">
                    <div class="space-y-3">
                        <button 
                            type="button"
                            @click="initCamera()"
                            class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold py-3 rounded-xl transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            Ambil Foto
                        </button>

                        <label class="w-full flex items-center justify-center gap-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold py-3 rounded-xl transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
                            Upload Gambar
                            <input 
                                type="file" 
                                wire:model="fotoUpload"
                                accept="image/*"
                                class="hidden"
                                @change="closeModal()"
                            >
                        </label>

                        @error('fotoUpload') <span class="text-red-400 text-xs block text-center">{{ $message }}</span> @enderror
                    </div>
                </template>

                <template x-if="isCameraOn">
                    <div class="space-y-3">
                        <div class="w-full aspect-square bg-gray-900 rounded-xl overflow-hidden">
                            <video x-ref="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                            <canvas x-ref="canvas" class="hidden"></canvas>
                        </div>
                        <button 
                            type="button"
                            @click="takeSnap()"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold py-3 rounded-xl transition"
                        >
                            Jepret Foto
                        </button>
                        <button 
                            type="button"
                            @click="stopCamera()"
                            class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold py-2.5 rounded-xl transition"
                        >
                            Batal
                        </button>
                    </div>
                </template>
            </div>
        </div>

        @if ($editing)
            <form wire:submit.prevent="saveProfile" class="space-y-6">
                <!-- Foto Profil Bulat Polos Saat Mode Edit -->
                <div class="flex justify-center mb-6">
                    <div class="relative">
                        <div class="w-28 h-28 rounded-full overflow-hidden border-2 border-gray-600 bg-gray-900 shadow-md">
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
                            class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-500 text-white rounded-full p-2 shadow-lg border-2 border-gray-900 transition"
                            title="Ubah Foto Profil"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Nama Lengkap</label>
                        <input type="text" wire:model="nama" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Nama lengkap">
                        @error('nama') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Email</label>
                        <input type="email" wire:model="email" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="email@example.com">
                        @error('email') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Asal Sekolah / Universitas</label>
                        <input type="text" wire:model="asal_sekolah" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Asal sekolah atau kampus">
                        @error('asal_sekolah') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Nama Mentor</label>
                        <input type="text" wire:model="mentor" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Nama mentor magang">
                        @error('mentor') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Tanggal Mulai</label>
                        <input type="date" wire:model="tanggal_mulai" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none">
                        @error('tanggal_mulai') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Tanggal Akhir</label>
                        <input type="date" wire:model="tanggal_akhir" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none">
                        @error('tanggal_akhir') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-300">Skill</label>
                    <textarea wire:model="skill" rows="4" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Contoh: Laravel, Tailwind, Vue.js"></textarea>
                    @error('skill') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Password Baru</label>
                        <input type="password" wire:model="password" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Kosongkan jika tidak ingin ganti password">
                        @error('password') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Konfirmasi Password</label>
                        <input type="password" wire:model="confirm_password" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="cancelEditing" type="button" class="rounded-2xl border border-gray-700 bg-transparent px-6 py-3 text-sm font-semibold text-gray-200 transition hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-500/20">
                        Simpan Profil
                    </button>
                </div>
            </form>
        @else
            <!-- Tampilan Profil Biasa (View Mode) -->
            <div class="grid grid-cols-1 gap-4">
                
                <!-- Foto Profil Bulat Polos di Atas Grid Data -->
                <div class="flex justify-center my-2">
                    <div class="w-28 h-28 rounded-full overflow-hidden border-2 border-gray-600 bg-gray-900 shadow-md">
                        <img 
                            src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('images/profile-placeholder.png') }}" 
                            class="w-full h-full object-cover"
                            alt="Foto Profil"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Nama Lengkap</p>
                        <p class="mt-2 text-sm text-white">{{ $nama }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Email</p>
                        <p class="mt-2 text-sm text-white">{{ $email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Asal Sekolah / Universitas</p>
                        <p class="mt-2 text-sm text-white">{{ $asal_sekolah ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Nama Mentor</p>
                        <p class="mt-2 text-sm text-white">{{ $mentor ?: '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Tanggal Mulai</p>
                        <p class="mt-2 text-sm text-white">{{ $tanggal_mulai ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Tanggal Akhir</p>
                        <p class="mt-2 text-sm text-white">{{ $tanggal_akhir ?: '-' }}</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Skill</p>
                    <p class="mt-2 text-sm text-white">{{ $skill ?: '-' }}</p>
                </div>
            </div>
        @endif
    </div>
</div>