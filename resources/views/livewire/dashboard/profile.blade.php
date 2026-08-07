<div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Profil Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Informasi akun dan data kepegawaian Anda.</p>
        </div>

        @if (!$isEditing)
            <button wire:click="$set('isEditing', true)" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Profil
            </button>
        @endif
    </div>

    <!-- Alert Success -->
    @if (session('message'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm shadow-xs transition-all">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Single Form Card -->
    <form wire:submit="save" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden transition-all">

       {{-- ============================================================ --}}
{{-- Section 1: Top Avatar Banner — dengan ikon edit + modal pilihan --}}
{{-- ============================================================ --}}
<div x-data="{
        showChoiceModal: false,
        showCameraModal: false,
        stream: null,
        async openCamera() {
            this.showChoiceModal = false;
            this.showCameraModal = true;
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                this.$refs.video.srcObject = this.stream;
            } catch (err) {
                alert('Tidak bisa mengakses kamera: ' + err.message);
                this.showCameraModal = false;
            }
        },
        closeCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.showCameraModal = false;
        },
        capturePhoto() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob((blob) => {
                const file = new File([blob], 'foto-kamera-' + Date.now() + '.jpg', { type: 'image/jpeg' });

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                const fileInput = this.$refs.fileInput;
                fileInput.files = dataTransfer.files;
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));

                this.closeCamera();
            }, 'image/jpeg', 0.9);
        },
        openGallery() {
            this.showChoiceModal = false;
            this.$refs.fileInput.click();
        }
     }"
     class="p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6 bg-slate-50/50 dark:bg-slate-800/30 border-b border-slate-200/80 dark:border-slate-800">

    <div class="relative">
        @if ($foto)
            <img src="{{ $foto->temporaryUrl() }}" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-blue-500 shadow-md">
        @elseif ($user->foto)
            <img src="{{ Storage::url($user->foto) }}" alt="Foto Profil"
                 class="w-24 h-24 rounded-2xl object-cover ring-4 ring-white dark:ring-slate-800 shadow-md">
        @else
            <div class="w-24 h-24 rounded-2xl bg-blue-600 text-white font-bold text-2xl flex items-center justify-center tracking-wider shadow-md shadow-blue-600/30 ring-4 ring-white dark:ring-slate-800">
                {{ strtoupper(substr($user->nama ?? 'TM', 0, 2)) }}
            </div>
        @endif

        {{-- Ikon Edit di pojok avatar (hanya saat mode edit) --}}
        @if ($isEditing)
            <button type="button" @click="showChoiceModal = true"
                    class="absolute -bottom-1.5 -right-1.5 w-8 h-8 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-md ring-2 ring-white dark:ring-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>
        @endif

        {{-- Input file asli (tersembunyi) --}}
        <input type="file" wire:model="foto" x-ref="fileInput" accept="image/*" class="hidden">
    </div>

    <div class="text-center sm:text-left space-y-1 flex-1">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $user->nama }}</h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 w-fit mx-auto sm:mx-0">
                {{ ucfirst($user->role->value ?? $user->role) }}
            </span>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>

        @error('foto') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    {{-- Modal Pilihan: Upload atau Ambil Foto --}}
    <div x-show="showChoiceModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
         style="display: none;">
        <div @click.outside="showChoiceModal = false" class="bg-white dark:bg-slate-900 rounded-2xl p-5 max-w-sm w-full space-y-3">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Ubah Foto Profil</h3>

            <button type="button" @click="openGallery()"
                    class="w-full flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-left transition">
                <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-950 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16M4 6a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2H4z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Upload dari Galeri</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pilih foto dari perangkat Anda</p>
                </div>
            </button>

            <button type="button" @click="openCamera()"
                    class="w-full flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-left transition">
                <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-950 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Ambil Foto</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan kamera perangkat</p>
                </div>
            </button>

            <button type="button" @click="showChoiceModal = false"
                    class="w-full text-center text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pt-1 transition">
                Batal
            </button>
        </div>
    </div>

    {{-- Modal Kamera --}}
    <div x-show="showCameraModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
         style="display: none;">
        <div @click.outside="closeCamera()" class="bg-white dark:bg-slate-900 rounded-2xl p-4 max-w-md w-full space-y-4">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Ambil Foto Profil</h3>

            <video x-ref="video" autoplay playsinline class="w-full rounded-xl bg-black aspect-square object-cover"></video>
            <canvas x-ref="canvas" class="hidden"></canvas>

            <div class="flex justify-end gap-2">
                <button type="button" @click="closeCamera()"
                        class="px-4 py-2 text-sm font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                    Batal
                </button>
                <button type="button" @click="capturePhoto()"
                        class="px-4 py-2 text-sm font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition">
                    Ambil Foto
                </button>
            </div>
        </div>
    </div>
</div>
{{-- ============ AKHIR Section 1 ============ --}}

        {{-- Section 2: Form Inputs & Readonly Information --}}
        <div class="p-6 sm:p-8 space-y-6">

            <!-- Readonly Information (Divisi & Bidang TIDAK BISA DIGANTI) -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Struktur Kepegawaian (Permanen)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Divisi -->
                    <div class="p-3.5 rounded-xl bg-slate-100/70 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800">
                        <label class="block text-xs font-medium text-slate-400 dark:text-slate-500 uppercase">Divisi</label>
                        <div class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>{{ $user->divisi->nama_divisi ?? '-' }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>

                    <!-- Bidang -->
                    <div class="p-3.5 rounded-xl bg-slate-100/70 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800">
                        <label class="block text-xs font-medium text-slate-400 dark:text-slate-500 uppercase">Bidang / Unit</label>
                        <div class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>{{$user->divisi->bidang->nama_bidang ?? '-' }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100 dark:border-slate-800">

            <!-- Editable Profile Info -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Informasi Pengguna</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap</label>
                        <input type="text" wire:model="nama" @disabled(!$isEditing)
                               class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-slate-50 dark:disabled:bg-slate-800/40 disabled:text-slate-500 transition">
                        @error('nama') <span class="text-rose-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email</label>
                        <input type="email" wire:model="email" @disabled(!$isEditing)
                               class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-slate-50 dark:disabled:bg-slate-800/40 disabled:text-slate-500 transition">
                        @error('email') <span class="text-rose-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Field Password Opsional (Hanya Muncul Saat Klik Edit) --}}
            @if ($isEditing)
                <hr class="border-slate-100 dark:border-slate-800">

                <div class="space-y-4 bg-slate-50 dark:bg-slate-800/20 p-4 sm:p-5 rounded-xl border border-slate-200/60 dark:border-slate-800">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Ganti Password (Opsional)</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kosongkan kolom password di bawah ini jika Anda tidak ingin mengubahnya.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Password Baru</label>
                            <input type="password" wire:model="password" placeholder="••••••••"
                                   class="w-full px-3.5 py-2 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500">
                            @error('password') <span class="text-rose-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" wire:model="password_confirmation" placeholder="••••••••"
                                   class="w-full px-3.5 py-2 border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Action Buttons Saat Edit --}}
            @if ($isEditing)
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" wire:click="cancelEdit"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition flex items-center gap-2 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            @endif

        </div>
    </form>
</div>