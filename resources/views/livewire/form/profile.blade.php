<!-- Modal Overlay -->
<div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
    
    <!-- Container Modal dengan Batas Tinggi Max 90vh -->
    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 p-6 flex flex-col max-h-[90vh] my-auto" @click.stop>
        
        <!-- Header Modal (Tetap di Atas) -->
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-4 shrink-0">
            <div>
                <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Data Pengguna' : 'Buat Pengguna Baru' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isEditMode ? 'Perbarui data profil dan jadwal magang pengguna.' : 'Isi data di bawah ini untuk mendaftarkan pengguna baru.' }}
                </p>
            </div>
            
            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-xl p-2 transition hover:bg-gray-100 dark:hover:bg-gray-800 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body Form (Bisa di-scroll jika konten panjang) -->
        <form wire:submit.prevent="save" class="space-y-4 overflow-y-auto pr-1 pt-4 my-2 flex-1 scrollbar-thin">
            
            <!-- Baris 1: Nama Lengkap & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="nama" 
                        wire:model="nama" 
                        placeholder="Masukkan nama lengkap" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('nama') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('nama') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Alamat Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        wire:model="email" 
                        placeholder="nama@email.com" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('email') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('email') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 2: Tempat Lahir & Tanggal Lahir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tempat_lahir" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tempat Lahir</label>
                    <input 
                        type="text" 
                        id="tempat_lahir" 
                        wire:model="tempat_lahir" 
                        placeholder="Contoh: Jakarta" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('tempat_lahir') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('tempat_lahir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tanggal_lahir" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal Lahir</label>
                    <input 
                        type="date" 
                        id="tanggal_lahir" 
                        wire:model="tanggal_lahir" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('tanggal_lahir') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition text-gray-900 dark:text-white"
                    >
                    @error('tanggal_lahir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 3: Jenis Kelamin & Jurusan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jenis_kelamin" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jenis Kelamin</label>
                    <select 
                        id="jenis_kelamin" 
                        wire:model="jenis_kelamin" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('jenis_kelamin') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                    >
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="laki-laki">Laki-laki</option>
                        <option value="perempuan">Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="jurusan" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jurusan / Program Studi</label>
                    <input 
                        type="text" 
                        id="jurusan" 
                        wire:model="jurusan" 
                        placeholder="Contoh: Teknik Informatika / RPL" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('jurusan') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('jurusan') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 4: Asal Sekolah & Mentor -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="asal_sekolah" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Asal Sekolah / Universitas</label>
                    <input 
                        type="text" 
                        id="asal_sekolah" 
                        wire:model="asal_sekolah" 
                        placeholder="Masukkan sekolah/kampus" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('asal_sekolah') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                    >
                    @error('asal_sekolah') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="mentor" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Mentor</label>
                    @php
                        $isMentorUser = auth()->user()->role === \App\Enums\UserRole::MENTOR || auth()->user()->role?->value === \App\Enums\UserRole::MENTOR->value;
                    @endphp
                    <select 
                        id="mentor" 
                        wire:model="mentor" 
                        @disabled($isMentorUser)
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('mentor') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <option value="">-- Pilih Mentor --</option>
                        @foreach($mentors as $m)
                            <option value="{{ $m->nama }}">{{ $m->nama }}</option>
                        @endforeach
                    </select>
                    @if($isMentorUser)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">*Sebagai Mentor, Anda otomatis menjadi pembimbing pengguna ini.</p>
                    @endif
                    @error('mentor') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 5: Tanggal Mulai & Tanggal Akhir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal Mulai Magang</label>
                    <input 
                        type="date" 
                        id="tanggal_mulai" 
                        wire:model="tanggal_mulai" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('tanggal_mulai') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                    >
                    @error('tanggal_mulai') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tanggal_akhir" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal Akhir Magang</label>
                    <input 
                        type="date" 
                        id="tanggal_akhir" 
                        wire:model="tanggal_akhir" 
                        class="bg-gray-50 dark:bg-gray-800/60 border @error('tanggal_akhir') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                    >
                    @error('tanggal_akhir') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Baris 6: Skill -->
            <div>
                <label for="skill" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Skill / Keahlian</label>
                <textarea 
                    id="skill" 
                    wire:model="skill" 
                    rows="3" 
                    placeholder="Tuliskan skill pengguna, misalnya PHP, Laravel, Tailwind, dll." 
                    class="bg-gray-50 dark:bg-gray-800/60 border @error('skill') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                ></textarea>
                @error('skill') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Tombol Aksi (Tetap di Bawah) -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800 shrink-0">
                <button 
                    type="button" 
                    wire:click="closeModal" 
                    class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition"
                >
                    Batal
                </button>

                <button 
                    type="submit" 
                    wire:loading.attr="disabled" 
                    class="px-5 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md shadow-blue-600/20 transition disabled:opacity-50"
                >
                    <span wire:loading.remove>{{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Data' }}</span>
                    <span wire:loading>Proses Menyimpan...</span>
                </button>
            </div>
        </form>

    </div>
</div>