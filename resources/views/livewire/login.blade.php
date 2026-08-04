<div class="min-h-screen w-full bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto flex min-h-[calc(100vh-3rem)] max-w-6xl items-center justify-center">
        <div class="w-full overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-[0_32px_90px_rgba(15,23,42,0.12)]">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr]">
                <div class="relative overflow-hidden bg-white p-8 text-slate-900 sm:p-10 lg:p-12">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.08),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.08),_transparent_25%)]"></div>
                    <div class="relative flex h-full flex-col justify-between">
                        <div>
                            <div class="mt-4 flex items-center gap-4">
                                <img src="{{ asset('images/logoEpresensiPKL.png') }}" alt="Logo SIMPATI" class="w-16 h-16 rounded-3xl object-cover shadow-lg shadow-slate-900/10" />
                                <div>
                                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">SIMPATI</h2>
                                    <p class="mt-1 text-sm text-slate-600">Sistem Informasi Magang dan Presensi dan Aktivitas</p>
                                </div>
                            </div>

                            <h1 class="mt-8 text-4xl font-semibold leading-tight text-slate-900 sm:text-5xl">
                                Selamat datang kembali
                            </h1>
                            <p class="mt-3 max-w-md text-sm leading-6 text-slate-600 sm:text-base">
                                Kelola absensi, jadwal, dan aktivitas proyek dengan lebih cepat, aman, dan nyaman.
                            </p>
                        </div>

                        <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-100 text-sky-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                                        <circle cx="12" cy="12" r="9" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Akses cepat dan aman</p>
                                    <p class="mt-1 text-sm text-slate-600">
                                        Login dengan akun resmi Anda untuk mengakses semua fitur yang tersedia.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 sm:p-8 lg:p-10">
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
                        class="mx-auto w-full max-w-md"
                    >
                        <div class="text-center lg:text-left">
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-600">Masuk akun</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Silakan login untuk melanjutkan</h2>
                            <p class="mt-2 text-sm text-slate-500">Masukkan email, password, dan kode captcha yang valid.</p>
                        </div>

                        <form class="mt-6 space-y-4" wire:submit.prevent="login">
                            @if ($errorMessage)
                                <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                                    {{ $errorMessage }}
                                </div>
                            @endif

                            <div>
                                <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <input
                                        type="email"
                                        wire:model.live.debounce.300ms="email"
                                        id="email"
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('email') border-red-400 @enderror"
                                        placeholder="name@gmail.com"
                                        required
                                    />
                                </div>
                                @error('email') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 10V7a5 5 0 1110 0v3" />
                                            <rect x="4" y="10" width="16" height="10" rx="2" />
                                        </svg>
                                    </span>
                                    <input
                                        type="{{ $showPassword ? 'text' : 'password' }}"
                                        wire:model="password"
                                        id="password"
                                        placeholder="••••••••"
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-10 text-sm text-slate-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('password') border-red-400 @enderror"
                                        required
                                    />
                                    <button
                                        type="button"
                                        wire:click="togglePasswordVisibility"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 transition hover:text-slate-700"
                                        aria-label="Tampilkan password"
                                    >
                                        @if ($showPassword)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                                @error('password') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <label for="remember" class="flex items-center gap-2 text-sm text-slate-600">
                                    <input wire:model="remember" id="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 bg-white text-blue-600 focus:ring-blue-500" />
                                    Ingat saya
                                </label>
                                <button type="button" wire:click="openAgreementModal" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                    Syarat & Ketentuan
                                </button>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <label class="mb-2 block text-sm font-medium text-slate-700">Captcha</label>
                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <div class="flex-1 overflow-hidden rounded-xl border border-blue-200 bg-white shadow-sm">
                                        <img src="{{ $captchaImage }}" alt="Captcha" class="h-16 w-full object-cover" />
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="generateCaptcha"
                                        class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-sky-700 shadow-sm transition hover:bg-slate-100"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Muat Ulang
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Masukkan 5 karakter pada gambar.</p>
                                <input
                                    type="text"
                                    wire:model="captchaInput"
                                    id="captcha"
                                    class="mt-3 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('captchaInput') border-red-400 @enderror"
                                    placeholder="Ketik kode captcha"
                                    required
                                />
                                @error('captchaInput') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <label for="agreeTerms" class="flex items-start gap-2 text-sm text-slate-600">
                                    <input wire:model="agreeTerms" id="agreeTerms" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 bg-white text-blue-600 focus:ring-blue-500" />
                                    <span>
                                        Saya setuju dengan
                                        <span class="font-medium text-blue-600">Pernyataan & Persetujuan</span>
                                    </span>
                                </label>
                                @error('agreeTerms') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="login"
                                :disabled="isLocked"
                                class="w-full rounded-2xl bg-gradient-to-r from-sky-600 to-blue-600 px-5 py-3 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-sky-600/20 transition hover:from-sky-700 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-sky-200 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="login">
                                    <template x-if="!isLocked">
                                        <span>Masuk</span>
                                    </template>
                                    <template x-if="isLocked">
                                        <span>Terkunci (<span x-text="seconds"></span>s)</span>
                                    </template>
                                </span>
                                <span wire:loading wire:target="login">Memproses...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($showAgreementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Pernyataan dan Persetujuan</h2>
                    <div class="mt-4 max-h-96 space-y-4 overflow-y-auto pr-2 text-sm text-slate-700">
                        <div>
                            <h3 class="font-semibold text-slate-900">Kebenaran Data Login</h3>
                            <p>Saya menyatakan bahwa data username dan password yang saya masukkan adalah benar, milik saya sendiri, dan sesuai dengan identitas yang terdaftar dalam sistem.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Larangan Penyalahgunaan Data & Informasi</h3>
                            <p>Saya berkomitmen untuk tidak menyalahgunakan data dan informasi yang saya akses melalui sistem ini untuk kepentingan pribadi, komersial, atau tujuan lain di luar tugas dan kewenangan saya.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Persetujuan Penggunaan Data Pribadi</h3>
                            <p>Saya memberikan persetujuan atas penggunaan data pribadi saya (nama, jabatan, akun pengguna) untuk keperluan pengelolaan dan pengoperasian sistem sesuai dengan fungsi dan tujuan sistem.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Kerahasiaan & Keamanan Akses</h3>
                            <p>Saya bertanggung jawab menjaga kerahasiaan kredensial akun saya dan tidak akan membagikan username atau password kepada pihak lain, serta segera melaporkan jika terjadi akses yang tidak sah.</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 p-4">
                    <button type="button" wire:click="closeAgreementModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Tutup</button>
                    <button type="button" wire:click="acceptTermsAndCloseModal" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">Saya Setuju</button>
                </div>
            </div>
        </div>
    @endif
</div>