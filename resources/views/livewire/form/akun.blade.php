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
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('divisi') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition"
                    >
                        <option value="">-- Pilih Divisi --</option>
                        @foreach(App\Enums\UserDivisi::cases() as $divisiEnum)
                            <option value="{{ $divisiEnum->value }}">{{ $divisiEnum->label() }}</option>
                        @endforeach
                    </select>
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
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('mentor') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition"
                    >
                        <option value="">-- Pilih Mentor --</option>
                        @foreach($mentors as $mentorUser)
                            <option value="{{ $mentorUser->nama }}">{{ $mentorUser->nama }}</option>
                        @endforeach
                    </select>
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
                <div>
                    <label for="password" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Password @if(!$isEditMode)<span class="text-red-500">*</span>@else <span class="text-gray-400 text-[10px] lowercase font-normal">(opsional)</span> @endif
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        wire:model="password" 
                        placeholder="Min. 8 karakter" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('password') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('password') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="confirm-password" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Konfirmasi Password
                    </label>
                    <input 
                        type="password" 
                        id="confirm-password" 
                        wire:model="confirm_password" 
                        placeholder="Ulangi password" 
                        class="bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
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