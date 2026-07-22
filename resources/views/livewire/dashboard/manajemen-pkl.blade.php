<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manajemen Anak PKL</h1>

    <!-- Flash Message Notifikasi -->
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <!-- Top Bar -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 gap-4 overflow-x-auto no-scrollbar">
            
            <!-- Search Bar -->
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari nama atau email...">
                </div>
            </div>           
        </div>
        
        <!-- Tabel Data (READ) -->
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama</th>
                    <th scope="col" class="px-6 py-3">asal sekolah</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Tanggal Masuk</th>
                    <th scope="col" class="px-6 py-3">Tanggal keluar</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $user->nama }}
                        </th>
                        <td class="px-6 py-4">{{ $user->asal_sekolah }}</td>
                        <td class="px-6 py-4"><span class="capitalize px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">{{ $user->status }}</span></td>
                        <td class="px-6 py-4">{{ optional($user->tanggal_mulai)->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ optional($user->tanggal_Akhir)->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4 flex flex-wrap gap-2">
                            <button type="button" wire:click="openEditProfile({{ $user->user_id }})" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</button>
                            <button type="button" wire:click="openJadwalModal({{ $user->user_id }})" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">jadwal masuk</button>
                            <button type="button" wire:click="openProjectModal({{ $user->user_id }})" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Project</button>
                            <button type="button" wire:click="delete({{ $user->user_id }})" onclick="confirm('Apakah Anda yakin ingin menghapus akun ini?') || event.stopImmediatePropagation()" class="font-medium text-red-600 dark:text-red-500 hover:underline">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Panggil File Form Modal Terpisah -->
    @if($showEditProfileModal)
        @include('livewire.form.profile', ['userId' => $selectedUserId])
    @endif

    @if($showJadwalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 py-6" @click="$event.target === $el && $wire.closeJadwalModal()" wire:key="jadwal-modal-{{ $selectedUserId }}">
            <div class="w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-xl bg-white p-4 shadow-2xl dark:bg-gray-800" @click.stop>
                <div class="pt-2">
                    @livewire('form.jadwal', ['userId' => $selectedUserId], key('jadwal-modal-' . ($selectedUserId ?? 'new')))
                </div>
            </div>
        </div>
    @endif
    @if($showProjectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 py-6" @click="$event.target === $el && $wire.closeProjectModal()" wire:key="project-modal-{{ $selectedUserId }}">
        <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800" @click.stop>
            @livewire('form.project', ['userId' => $selectedUserId], key('project-modal-' . ($selectedUserId ?? 'new')))
        </div>
    </div>
@endif
</div>