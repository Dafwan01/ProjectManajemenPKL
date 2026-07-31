<!-- Modal Overlay dengan Background Blur -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
    
    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 p-6 sm:p-8 space-y-6" @click.stop>
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-4">
            <div>
                <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Akun Pengguna' : 'Buat Akun Baru' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isEditMode ? 'Perbarui informasi dan hak akses akun.' : 'Lengkapi formulir di bawah ini untuk mendaftarkan akun baru.' }}
                </p>
            </div>
            
            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-xl p-2 transition hover:bg-gray-100 dark:hover:bg-gray-800 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        @php
            $currentUser = Auth::user();
            $isMentorUser = $currentUser->role === App\Enums\UserRole::MENTOR || $currentUser->role?->value === App\Enums\UserRole::MENTOR->value;
        @endphp

        <!-- Form Submit -->
        <form wire:submit.prevent="save" class="space-y-5">
            
            <!-- Baris 1: Nama & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama" 
                        wire:model="nama" 
                        placeholder="Contoh: Budi Santoso" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('nama') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('nama') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        wire:model="email" 
                        placeholder="nama@domain.com" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('email') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('email') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 2: Role & Divisi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Role Akses <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="role" 
                        wire:model="role" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('role') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        @if(count($this->availableRoles) === 1) disabled @endif
                    >
                        @if(count($this->availableRoles) > 1)
                            <option value="">-- Pilih Role --</option>
                        @endif

                        @foreach($this->availableRoles as $roleEnum)
                            <option value="{{ $roleEnum->value }}">{{ $roleEnum->label() }}</option>
                        @endforeach
                    </select>

                    @if(count($this->availableRoles) === 1)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 font-medium">
                            * Sebagai Mentor, Anda hanya dapat mendaftarkan Peserta PKL.
                        </p>
                    @endif

                    @error('role') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="divisi" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Divisi <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="divisi" 
                        wire:model="divisi" 
                        @disabled($isMentorUser)
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('divisi') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <option value="">-- Pilih Divisi --</option>
                        @foreach(App\Enums\UserDivisi::cases() as $divisiEnum)
                            <option value="{{ $divisiEnum->value }}">{{ $divisiEnum->label() }}</option>
                        @endforeach
                    </select>

                    @if($isMentorUser)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 font-medium">
                            * Otomatis disesuaikan dengan divisi Anda sebagai Mentor.
                        </p>
                    @endif

                    @error('divisi') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 3: Mentor & Asal Sekolah/Instansi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
               <div>
                    <label for="mentor" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Mentor Pembimbing <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="mentor" 
                        wire:model="mentor" 
                        @disabled($isMentorUser)
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('mentor') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <option value="">-- Pilih Mentor --</option>
                        @foreach($mentors as $mentorUser)
                            <option value="{{ $mentorUser->nama }}">{{ $mentorUser->nama }}</option>
                        @endforeach
                    </select>

                    @if($isMentorUser)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 font-medium">
                            * Otomatis disesuaikan dengan nama Anda sebagai Mentor.
                        </p>
                    @endif

                    @error('mentor') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="asal_sekolah" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Asal Sekolah / Instansi
                    </label>
                    <input 
                        type="text" 
                        id="asal_sekolah" 
                        wire:model="asal_sekolah" 
                        placeholder="Contoh: SMK Negeri 1 Bogor" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('asal_sekolah') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('asal_sekolah') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 4: Password & Konfirmasi Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100 dark:border-gray-800/80">
                <!-- Field Password -->
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
                            class="bg-gray-50 dark:bg-gray-800/60 border @error('password') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 pr-10 transition placeholder-gray-400 dark:placeholder-gray-500"
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition focus:outline-none"
                        >
                            <!-- Icon Mata Terbuka -->
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <!-- Icon Mata Tertutup -->
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Field Konfirmasi Password -->
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
                            class="bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 pr-10 transition placeholder-gray-400 dark:placeholder-gray-500"
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

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200 dark:border-gray-800">
                <button 
                    type="button" 
                    wire:click="closeModal" 
                    class="px-5 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition"
                >
                    Batal
                </button>

                <button 
                    type="submit" 
                    class="px-6 py-3 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md shadow-blue-600/20 transition"
                >
                    {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Akun' }}
                </button>
            </div>
        </form>

    </div>
</div>