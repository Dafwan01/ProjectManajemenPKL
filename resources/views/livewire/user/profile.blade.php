<div class="w-full mx-auto max-w-4xl">
    <div class="mb-6 border-b border-gray-800 pb-4 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Profil Saya</h1>
            <p class="text-sm text-gray-400 mt-1">Lihat ringkasan data akun Anda. Gunakan tombol edit untuk mengubah informasi profil.</p>
        </div>
        @if (! $editing)
            <button wire:click="startEditing" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-500/20">
                Edit Profil
            </button>
        @endif
    </div>

    @if(session()->has('message'))
        <div class="mb-4 p-4 rounded-xl bg-green-900/40 border border-green-600/60 text-green-200 text-sm shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-gray-800 border border-gray-700/60 rounded-3xl p-6 shadow-lg">
        @if ($editing)
            <form wire:submit.prevent="saveProfile" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Nama Lengkap</label>
                        <input type="text" wire:model="nama" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Nama lengkap">
                        @error('nama') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Email</label>
                        <input type="email" wire:model="email" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="email@example.com">
                        @error('email') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Asal Sekolah / Universitas</label>
                        <input type="text" wire:model="asal_sekolah" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Asal sekolah atau kampus">
                        @error('asal_sekolah') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Nama Mentor</label>
                        <input type="text" wire:model="mentor" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Nama mentor magang">
                        @error('mentor') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Tanggal Mulai</label>
                        <input type="date" wire:model="tanggal_mulai" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none">
                        @error('tanggal_mulai') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Tanggal Akhir</label>
                        <input type="date" wire:model="tanggal_akhir" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none">
                        @error('tanggal_akhir') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-300">Skill</label>
                    <textarea wire:model="skill" rows="4" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Contoh: Laravel, Tailwind, Vue.js"></textarea>
                    @error('skill') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Password Baru</label>
                        <input type="password" wire:model="password" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Kosongkan jika tidak ingin ganti password">
                        @error('password') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300">Konfirmasi Password</label>
                        <input type="password" wire:model="confirm_password" class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="cancelEditing" type="button" class="rounded-2xl border border-gray-700 bg-transparent px-6 py-3 text-sm font-semibold text-gray-200 transition hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-500/20">
                        Simpan Profil
                    </button>
                </div>
            </form>
        @else
            <div class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Nama Lengkap</p>
                        <p class="mt-2 text-sm text-white">{{ $nama }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Email</p>
                        <p class="mt-2 text-sm text-white">{{ $email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Asal Sekolah / Universitas</p>
                        <p class="mt-2 text-sm text-white">{{ $asal_sekolah ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Nama Mentor</p>
                        <p class="mt-2 text-sm text-white">{{ $mentor ?: '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Tanggal Mulai</p>
                        <p class="mt-2 text-sm text-white">{{ $tanggal_mulai ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Tanggal Akhir</p>
                        <p class="mt-2 text-sm text-white">{{ $tanggal_akhir ?: '-' }}</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-700 bg-gray-900 p-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Skill</p>
                    <p class="mt-2 text-sm text-white">{{ $skill ?: '-' }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
