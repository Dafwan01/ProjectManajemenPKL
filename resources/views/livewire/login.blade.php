<div style="background-image:url('{{ asset('images/Balkot.png') }}')" class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-no-repeat">
   
    <div class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 md:p-8">
        <form class="space-y-6" wire:submit.prevent="login">
            <h5 class="text-xl font-medium text-gray-900">Sign in to our platform</h5>

            @if ($errorMessage)
                <div class="p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">
                    {{ $errorMessage }}
                </div>
            @endif
            
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Masukan Email</label>
                <input 
                    type="email" 
                    wire:model="email" 
                    id="email" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('email') border-red-500 @enderror" 
                    placeholder="name@gmail.com" 
                    required 
                />
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div> 
            
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
                    <button type="button" wire:click="togglePasswordVisibility" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700" aria-label="Tampilkan password">
                        @if ($showPassword)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 1l22 22" />
                                <path d="M17.94 17.94A10.06 10.06 0 0 1 12 20c-5 0-9.27-3.11-11-7.5a18.84 18.84 0 0 1 4.46-6.6" />
                                <path d="M9.53 9.53a3 3 0 0 0 4.24 4.24" />
                                <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88" />
                                <path d="M6.1 6.1A18.74 18.74 0 0 1 12 4c5 0 9.27 3.11 11 7.5a18.84 18.84 0 0 1-1.64 3.04" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        @endif
                    </button>
                </div>
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-start">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input wire:model="remember" id="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300" />
                    </div>
                    <label for="remember" class="ms-2 text-sm font-medium text-gray-900">Remember me</label>
                </div>
            </div>

            <div>
                <label for="captcha" class="block mb-2 text-sm font-medium text-gray-900">Captcha: {{ $captchaQuestion }}</label>
                <input
                    type="text"
                    wire:model="captchaInput"
                    id="captcha"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('captchaInput') border-red-500 @enderror"
                    placeholder="Jawaban captcha"
                    required
                />
                @error('captchaInput') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex items-start">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input wire:model="agreeTerms" id="agreeTerms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300" />
                    </div>
                    <label for="agreeTerms" class="ms-2 text-sm font-medium text-gray-900">
                        Saya menyetujui <button type="button" wire:click="openAgreementModal" class="text-blue-600 hover:underline">pernyataan dan persetujuan</button>
                    </label>
                </div>
            </div>
            @error('agreeTerms') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            
            <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="login">Login</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
        </form>
    </div>

    @if ($showAgreementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/5">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Pernyataan dan Persetujuan</h2>
                    <div class="mt-4 space-y-5 text-sm text-gray-700">
                        <div>
                            <h3 class="font-semibold">Kebenaran Data Login</h3>
                            <p>Saya menyatakan bahwa data username dan password yang saya masukkan adalah benar, milik saya sendiri, dan sesuai dengan identitas yang terdaftar dalam sistem.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold">Larangan Penyalahgunaan Data & Informasi</h3>
                            <p>Saya berkomitmen untuk tidak menyalahgunakan data dan informasi yang saya akses melalui sistem ini untuk kepentingan pribadi, komersial, atau tujuan lain di luar tugas dan kewenangan saya.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold">Persetujuan Penggunaan Data Pribadi</h3>
                            <p>Saya memberikan persetujuan atas penggunaan data pribadi saya (nama, jabatan, akun pengguna) untuk keperluan pengelolaan dan pengoperasian aplikasi Bogor Besti sesuai dengan fungsi dan tujuan sistem.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold">Kerahasiaan & Keamanan Akses</h3>
                            <p>Saya bertanggung jawab menjaga kerahasiaan kredensial akun saya dan tidak akan membagikan username atau password kepada pihak lain, serta segera melaporkan jika terjadi akses yang tidak sah.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold">Kepatuhan terhadap UU PDP No. 27 Tahun 2022</h3>
                            <p>Saya memahami bahwa pengolahan data pribadi dalam sistem ini dilindungi oleh Undang-Undang Perlindungan Data Pribadi (UU PDP) dan saya bersedia mematuhi seluruh ketentuan yang berlaku.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold">Tanggung Jawab Hukum</h3>
                            <p>Saya menyadari bahwa setiap pelanggaran atas pernyataan ini dapat dikenai sanksi administratif maupun hukum sesuai peraturan perundang-undangan yang berlaku.</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 p-4">
                    <button type="button" wire:click="closeAgreementModal" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Tutup</button>
                    <button type="button" wire:click="closeAgreementModal" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">Saya Setuju</button>
                </div>
            </div>
        </div>
    @endif
</div>