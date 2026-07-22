<!-- Modal Overlay dengan Background Blur -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-md p-4 overflow-y-auto">
    
    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-5 my-8">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b dark:border-gray-700 pb-3">
            <div>
                <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Data Pengguna' : 'Buat Pengguna Baru' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $isEditMode ? 'Perbarui data profil dan jadwal magang pengguna.' : 'Isi data di bawah ini untuk mendaftarkan pengguna baru.' }}
                </p>
            </div>
            
            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-lg p-1.5 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Form Submit -->
        <form wire:submit.prevent="save" class="space-y-4">
            
            <!-- Baris 1: Nama Lengkap & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                    <input type="text" id="nama" wire:model="nama" placeholder="Masukkan nama lengkap" class="bg-gray-50 border @error('nama') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Alamat Email</label>
                    <input type="email" id="email" wire:model="email" placeholder="nama@email.com" class="bg-gray-50 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 2: Asal Sekolah & Mentor -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="asal_sekolah" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Asal Sekolah / Universitas</label>
                    <input type="text" id="asal_sekolah" wire:model="asal_sekolah" placeholder="Masukkan sekolah/kampus" class="bg-gray-50 border @error('asal_sekolah') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('asal_sekolah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="mentor" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Nama Mentor</label>
                    <input type="text" id="mentor" wire:model="mentor" placeholder="Masukkan nama mentor" class="bg-gray-50 border @error('mentor') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('mentor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 3: Tanggal Mulai & Tanggal Akhir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" wire:model="tanggal_mulai" class="bg-gray-50 border @error('tanggal_mulai') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('tanggal_mulai') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tanggal_akhir" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Tanggal Akhir</label>
                    <input type="date" id="tanggal_akhir" wire:model="tanggal_akhir" class="bg-gray-50 border @error('tanggal_akhir') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('tanggal_akhir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 4: Skill -->
            <div>
                <label for="skill" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">Skill</label>
                <textarea id="skill" wire:model="skill" rows="5" placeholder="Tuliskan skill pengguna, misalnya PHP, Laravel, Tailwind, dll." class="bg-gray-50 border @error('skill') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                @error('skill') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Baris 5: Password & Konfirmasi Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-white">
                        Password {{ $isEditMode ? '(Kosongkan jika tidak diubah)' : '' }}
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
                    {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Data' }}
                </button>
            </div>
        </form>

    </div>
</div>