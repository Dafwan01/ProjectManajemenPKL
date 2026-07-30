<div style="background-image:url('{{ asset('images/Balkot.png') }}')" class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-no-repeat">
   
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
        class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 md:p-8"
    >
        <form class="space-y-5" wire:submit.prevent="login">
            <h5 class="text-xl font-medium text-gray-900">Sign in to our platform</h5>

            @if ($errorMessage)
                <div class="p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">
                    {{ $errorMessage }}
                </div>
            @endif
            
            {{-- Input Email --}}
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Masukan Email</label>
                <input 
                    type="email" 
                    wire:model.live.debounce.300ms="email" 
                    id="email" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('email') border-red-500 @enderror" 
                    placeholder="name@gmail.com" 
                    required 
                />
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div> 
            
            {{-- Input Password dengan Icon Mata --}}
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Masukan Password</label>
                <div class="relative">
                    <input 
                        type="{{ $showPassword ? 'text' : 'password' }}" 
                        wire:model="password" 
                        id="password" 
                        placeholder="••••••••" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 @error('password') border-red-500 @enderror" 
                        required 
                    />
                    <button 
                        type="button" 
                        wire:click="togglePasswordVisibility" 
                        class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700 focus:outline-none" 
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
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input wire:model="remember" id="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300" />
                </div>
                <label for="remember" class="ms-2 text-sm font-medium text-gray-900">Remember me</label>
            </div>

            {{-- Section Captcha --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Captcha</label>
                
                <div class="p-3 border border-blue-200 bg-blue-50/50 rounded-xl space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex-1 overflow-hidden rounded-lg shadow-sm border border-blue-100 flex justify-center bg-white">
                            <img src="{{ $captchaImage }}" alt="Captcha" class="h-16 w-full object-cover" />
                        </div>
                        <button 
                            type="button" 
                            wire:click="generateCaptcha"
                            class="flex flex-col items-center justify-center p-2 text-xs font-medium text-blue-700 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors shadow-sm min-w-[75px]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Muat Ulang
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Masukkan 5 karakter pada gambar.</p>
                </div>

                <input
                    type="text"
                    wire:model="captchaInput"
                    id="captcha"
                    class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('captchaInput') border-red-500 @enderror"
                    placeholder="Ketik kode captcha"
                    required
                />
                @error('captchaInput') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            {{-- Persetujuan Terms --}}
            <div>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input wire:model="agreeTerms" id="agreeTerms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300" />
                    </div>
                    <label for="agreeTerms" class="ms-2 text-sm font-medium text-gray-900">
                        Saya setuju dengan <button type="button" wire:click="openAgreementModal" class="text-blue-600 hover:underline">Pernyataan & Persetujuan</button>
                    </label>
                </div>
                @error('agreeTerms') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            {{-- Tombol Submit dengan Kunci Timeout & Countdown --}}
            <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                :disabled="isLocked"
                class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors uppercase font-semibold disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-400"
            >
                <span wire:loading.remove wire:target="login">
                    <template x-if="!isLocked">
                        <span>MASUK</span>
                    </template>
                    <template x-if="isLocked">
                        <span>TERKUNCI (<span x-text="seconds"></span>S)</span>
                    </template>
                </span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
        </form>
    </div>

    {{-- Modal Pernyataan dan Persetujuan --}}
    @if ($showAgreementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/5">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Pernyataan dan Persetujuan</h2>
                    <div class="mt-4 space-y-4 text-sm text-gray-700 max-h-96 overflow-y-auto pr-2">
                        <div>
                            <h3 class="font-semibold text-gray-900">Kebenaran Data Login</h3>
                            <p>Saya menyatakan bahwa data username dan password yang saya masukkan adalah benar, milik saya sendiri, dan sesuai dengan identitas yang terdaftar dalam sistem.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Larangan Penyalahgunaan Data & Informasi</h3>
                            <p>Saya berkomitmen untuk tidak menyalahgunakan data dan informasi yang saya akses melalui sistem ini untuk kepentingan pribadi, komersial, atau tujuan lain di luar tugas dan kewenangan saya.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Persetujuan Penggunaan Data Pribadi</h3>
                            <p>Saya memberikan persetujuan atas penggunaan data pribadi saya (nama, jabatan, akun pengguna) untuk keperluan pengelolaan dan pengoperasian sistem sesuai dengan fungsi dan tujuan sistem.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Kerahasiaan & Keamanan Akses</h3>
                            <p>Saya bertanggung jawab menjaga kerahasiaan kredensial akun saya dan tidak akan membagikan username atau password kepada pihak lain, serta segera melaporkan jika terjadi akses yang tidak sah.</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 p-4">
                    <button type="button" wire:click="closeAgreementModal" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Tutup</button>
                    <button type="button" wire:click="acceptTermsAndCloseModal" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">Saya Setuju</button>
                </div>
            </div>
        </div>
    @endif
</div>