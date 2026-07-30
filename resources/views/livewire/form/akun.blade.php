<!-- Modal Overlay dengan Background Blur -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-md p-4 overflow-y-auto">
    
    <div class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-5">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b dark:border-gray-700 pb-3">
            <div>
                <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Akun' : 'Buat Akun Baru' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $isEditMode ? 'Perbarui data akun pengguna.' : 'Isi data di bawah ini untuk mendaftar.' }}
                </p>
            </div>
            
            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-lg p-1.5 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Form Submit -->
        <form wire:submit.prevent="save" class="space-y-4">
            
            <!-- Baris 1: Nama & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                    <input type="text" id="nama" wire:model="nama" placeholder="Masukkan nama" class="bg-gray-50 border @error('nama') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Alamat Email</label>
                    <input type="email" id="email" wire:model="email" placeholder="nama@perusahaan.com" class="bg-gray-50 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

           <!-- Baris 2: Role, Divisi, Mentor -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label for="role" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Pilih Role</label>
        <select id="role" wire:model="role" class="bg-gray-50 border @error('role') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="" selected>-- Pilih Role Pengguna --</option>
            @foreach(App\Enums\UserRole::cases() as $roleEnum)
                <option value="{{ $roleEnum->value }}">{{ $roleEnum->label() }}</option>
            @endforeach
        </select>
        @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="divisi" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Pilih divisi</label>
        <select id="divisi" wire:model="divisi" class="bg-gray-50 border @error('divisi') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="" selected>-- Pilih divisi Pengguna --</option>
            @foreach(App\Enums\UserDivisi::cases() as $divisiEnum)
                <option value="{{ $divisiEnum->value }}">{{ $divisiEnum->label() }}</option>
            @endforeach
        </select>
        @error('divisi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="mentor" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Pilih Mentor</label>
        <select id="mentor" wire:model="mentor" class="bg-gray-50 border @error('mentor') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">-- Pilih Mentor --</option>
            @foreach($mentors as $mentorUser)
                <option value="{{ $mentorUser->nama }}">{{ $mentorUser->nama }}</option>
            @endforeach
        </select>
        @error('mentor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>
</div>

            <!-- Baris 3: Password & Konfirmasi Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">
                        Password {{ $isEditMode ? '(Opsional)' : '' }}
                    </label>
                    <input type="password" id="password" wire:model="password" placeholder="••••••••" class="bg-gray-50 border @error('password') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="confirm-password" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Konfirmasi Password</label>
                    <input type="password" id="confirm-password" wire:model="confirm_password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                <button type="button" wire:click="closeModal" class="py-2 px-4 text-xs font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    Batal
                </button>

                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-xs px-5 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700">
                    {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Akun' }}
                </button>
            </div>
        </form>

    </div>
</div>