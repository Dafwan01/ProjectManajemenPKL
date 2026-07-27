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
                    <th scope="col" class="px-6 py-3">Asal Sekolah</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Tanggal Masuk</th>
                    <th scope="col" class="px-6 py-3">Tanggal Keluar</th>
                    <th scope="col" class="px-6 py-3">Mentor</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $user->nama }}
                        </th>
                        <td class="px-6 py-4">{{ $user->asal_sekolah }}</td>
                 <td class="px-6 py-4 align-middle">
    @php
        $isAktif = strtolower($user->status?->value ?? '') === 'aktif';
    @endphp
    <span class="inline-flex items-center justify-center min-w-[90px] whitespace-nowrap capitalize px-3 py-1 text-xs font-semibold rounded-full {{ $isAktif ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
        {{ $user->status }}
    </span>
</td>
                        <td class="px-6 py-4">{{ optional($user->tanggal_mulai)->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ optional($user->tanggal_Akhir)->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $user->mentor }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1">
                                <!-- Edit -->
                                <button 
                                    type="button" 
                                    wire:click="openEditProfile({{ $user->user_id }})" 
                                    title="Edit Profil"
                                    class="p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-400 dark:hover:text-blue-400 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                <!-- Jadwal -->
                                <button 
                                    type="button" 
                                    wire:click="openJadwalModal({{ $user->user_id }})" 
                                    title="Jadwal Masuk"
                                    class="p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>

                                <!-- Project -->
                                <button 
                                    type="button" 
                                    wire:click="openProjectModal({{ $user->user_id }})" 
                                    title="Detail Project"
                                    class="p-2 rounded-lg text-gray-500 hover:text-purple-600 hover:bg-purple-50 dark:text-gray-400 dark:hover:text-purple-400 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan.</td>
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