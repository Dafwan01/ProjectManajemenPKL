<div 
    style="background-image:url('{{ asset('images/Balkot.png') }}')" 
    class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-no-repeat relative p-4 sm:p-6 transition-colors duration-200"
>
    <!-- Overlay gelap tambahan dengan backdrop blur lembut -->
    <div class="absolute inset-0 bg-black/30 dark:bg-black/60 backdrop-blur-[2px] transition-colors duration-200 pointer-events-none"></div>

    <div 
        x-data="{ 
            isLocked: @entangle('isLocked'), 
            seconds: @entangle('secondsRemaining'), 
            timer: null,
            startCountdown() {
                if (this.timer) clearInterval(this.timer);
                this.timer = setInterval(() => {
                    if (this.seconds > 0) {
                        this.seconds--;
                    } else {
                        this.isLocked = false;
                        clearInterval(this.timer);
                    }
                }, 1000);
            }
        }"
        x-init="
            $watch('isLocked', value => {
                if (value) startCountdown();
            });
            if (isLocked) startCountdown();
        "
        class="relative z-10 w-full max-w-md p-6 sm:p-8 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border border-gray-200/80 dark:border-gray-800 rounded-2xl shadow-2xl transition-all duration-300"
    >
        <form class="space-y-5" wire:submit.prevent="login">
            
            <!-- Judul & Sub-judul Form -->
            <div class="text-center mb-2">
                <h5 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Masuk ke Sistem SIMPATI</h5>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1">Sistem Informasi Magang Presensi dan Aktivitas</p>
            </div>

            <!-- Pesan Error Rate Limiter / Akses Dikunci / Kesalahan Auth -->
            @if ($errorMessage)
                <div class="p-3.5 text-xs font-medium text-red-800 dark:text-red-300 bg-red-50/90 dark:bg-red-900/30 border border-red-200 dark:border-red-800/60 rounded-xl shadow-sm flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif
            
            {{-- Input Email --}}
            <div>
                <label for="email" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Masukkan Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input 
                        type="email" 
                        wire:model.live.debounce.300ms="email" 
                        id="email" 
                        class="bg-gray-50 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block w-full pl-10 p-2.5 transition duration-150 placeholder-gray-400 dark:placeholder-gray-500 @error('email') border-red-500 dark:border-red-500 focus:ring-red-500/20 @enderror" 
                        placeholder="nama@email.com" 
                        required 
                    />
                </div>
                @error('email') <span class="text-red-500 dark:text-red-400 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
            </div> 
            
            {{-- Input Kata Sandi --}}
            <div>
                <label for="password" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Masukkan Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input 
                        type="{{ $showPassword ? 'text' : 'password' }}" 
                        wire:model="password" 
                        id="password" 
                        placeholder="••••••••" 
                        class="bg-gray-50 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block w-full pl-10 pr-10 p-2.5 transition duration-150 placeholder-gray-400 dark:placeholder-gray-500 @error('password') border-red-500 dark:border-red-500 focus:ring-red-500/20 @enderror" 
                        required 
                    />
                    <button 
                        type="button" 
                        wire:click="togglePasswordVisibility" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition focus:outline-none" 
                        aria-label="Tampilkan kata sandi"
                    >
                        @if ($showPassword)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        @endif
                    </button>
                </div>
                @error('password') <span class="text-red-500 dark:text-red-400 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
            </div>

            {{-- Ingat Saya / Remember Me --}}
            <div class="flex items-center">
                <input wire:model="remember" id="remember" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 cursor-pointer" />
                <label for="remember" class="ms-2.5 text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">Ingat Saya</label>
            </div>

            {{-- Bagian Captcha --}}
            <div>
                <label class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Kode Captcha</label>
                
                <div class="p-3 border border-blue-200/80 dark:border-blue-800/50 bg-blue-50/50 dark:bg-blue-900/20 rounded-2xl space-y-2.5 backdrop-blur-sm">
                    <div class="flex items-center justify-between gap-2.5">
                        <div class="flex-1 overflow-hidden rounded-xl shadow-sm border border-blue-100 dark:border-blue-800/60 flex justify-center bg-white dark:bg-gray-800 p-1">
                            <img src="{{ $captchaImage }}" alt="Kode Captcha" class="h-14 w-full object-contain rounded-lg" />
                        </div>
                        <button 
                            type="button" 
                            wire:click="generateCaptcha"
                            class="flex flex-col items-center justify-center p-2 text-xs font-semibold text-blue-700 dark:text-blue-400 bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-800/60 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/40 active:scale-95 transition-all shadow-sm shrink-0 h-14 w-20"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Muat Ulang</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium px-1">Masukkan 5 karakter yang tertera pada gambar di atas.</p>
                </div>

                <input
                    type="text"
                    wire:model="captchaInput"
                    id="captcha"
                    class="mt-2 bg-gray-50 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block w-full p-2.5 transition duration-150 placeholder-gray-400 dark:placeholder-gray-500 @error('captchaInput') border-red-500 dark:border-red-500 focus:ring-red-500/20 @enderror"
                    placeholder="Ketik kode captcha"
                    required
                />
                @error('captchaInput') <span class="text-red-500 dark:text-red-400 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
            </div>
            
            {{-- Persetujuan Syarat & Ketentuan --}}
            <div>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input wire:model="agreeTerms" id="agreeTerms" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 cursor-pointer" />
                    </div>
                    <label for="agreeTerms" class="ms-2.5 text-xs font-medium text-gray-700 dark:text-gray-300 leading-tight cursor-pointer">
                        Saya setuju dengan <button type="button" wire:click="openAgreementModal" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">Pernyataan & Persetujuan</button>
                    </label>
                </div>
                @error('agreeTerms') <span class="text-red-500 dark:text-red-400 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
            </div>
            
            {{-- Tombol Masuk --}}
            <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                :disabled="isLocked"
                class="w-full text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-semibold rounded-xl text-sm px-5 py-3 text-center transition-all duration-200 tracking-wider shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-400 dark:disabled:bg-gray-600 disabled:shadow-none uppercase"
            >
                <span wire:loading.remove wire:target="login">
                    <template x-if="!isLocked">
                        <span>MASUK</span>
                    </template>
                    <template x-if="isLocked">
                        <span>TERKUNCI (<span x-text="seconds"></span>DETIK)</span>
                    </template>
                </span>
                <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </form>
    </div>

    {{-- Modal Pernyataan dan Persetujuan --}}
    @if ($showAgreementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all duration-300">
            <div class="w-full max-w-xl overflow-hidden rounded-3xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-800">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Pernyataan dan Persetujuan</h2>
                </div>
                <div class="p-6 space-y-4 text-sm text-gray-600 dark:text-gray-300 max-h-[60vh] overflow-y-auto leading-relaxed">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Kebenaran Data Login</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Saya menyatakan bahwa data nama pengguna/email dan kata sandi yang saya masukkan adalah benar, milik saya sendiri, dan sesuai dengan identitas yang terdaftar dalam sistem.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Larangan Penyalahgunaan Data & Informasi</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Saya berkomitmen untuk tidak menyalahgunakan data dan informasi yang saya akses melalui sistem ini untuk kepentingan pribadi, komersial, atau tujuan lain di luar tugas dan kewenangan saya.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Persetujuan Penggunaan Data Pribadi</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Saya memberikan persetujuan atas penggunaan data pribadi saya (nama, jabatan, akun pengguna) untuk keperluan pengelolaan dan pengoperasian sistem sesuai dengan fungsi dan tujuan sistem.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Kerahasiaan & Keamanan Akses</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Saya bertanggung jawab menjaga kerahasiaan kredensial akun saya dan tidak akan membagikan nama pengguna atau kata sandi kepada pihak lain, serta segera melaporkan jika terjadi akses yang tidak sah.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 p-4">
                    <button type="button" wire:click="closeAgreementModal" class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Tutup</button>
                    <button type="button" wire:click="acceptTermsAndCloseModal" class="rounded-xl bg-blue-600 dark:bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 dark:hover:bg-blue-500 shadow-md shadow-blue-600/20 transition">Saya Setuju</button>
                </div>
            </div>
        </div>
    @endif
</div>
