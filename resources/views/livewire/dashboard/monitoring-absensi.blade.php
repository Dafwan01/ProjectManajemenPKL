<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Monitoring Absensi</h1>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <!-- Top Bar -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 gap-4 overflow-x-auto no-scrollbar">
            
            <!-- Date Picker -->
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none z-10">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input 
                        type="date" 
                        wire:model.live="tanggal"
                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-52 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                </div>
            </div>

            <!-- Tombol Lihat Lokasi -->
            <button 
                type="button"
                wire:click="openMap"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Lihat Lokasi
            </button>
        </div>
        
        <!-- Tabel Data -->
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama</th>
                    <th scope="col" class="px-6 py-3">Asal Sekolah</th>
                    <th scope="col" class="px-6 py-3">Kehadiran</th>
                    <th scope="col" class="px-6 py-3 text-center">Absen Masuk</th>
                    <th scope="col" class="px-6 py-3 text-center">Absen Keluar</th>
                    <th scope="col" class="px-6 py-3">Logbook</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensis as $presensi)
                    @php
                        $user = $presensi->user ?? $presensi->logBooks->first()?->user;
                    @endphp
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $user->nama ?? $user->name ?? '-' }}
                        </th>
                        <td class="px-6 py-4">{{ $user->asal_sekolah ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="capitalize px-2.5 py-1 text-xs font-semibold rounded-full 
                                @if(($presensi->status_kehadiran?->value ?? $presensi->status_kehadiran) === 'hadir')
                                    bg-green-100 text-green-800 dark:bg-green-900/60 dark:text-green-300
                                @elseif(($presensi->status_kehadiran?->value ?? $presensi->status_kehadiran) === 'izin')
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900/60 dark:text-yellow-300
                                @else
                                    bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300
                                @endif
                            ">
                                {{ $presensi->status_kehadiran?->value ?? $presensi->status_kehadiran ?? '-' }}
                            </span>
                        </td>
                        
                        <!-- Foto + Jam Masuk -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                @if($presensi->foto_masuk)
                                    <a href="{{ asset('storage/' . $presensi->foto_masuk) }}" target="_blank" class="block group relative">
                                        <img src="{{ asset('storage/' . $presensi->foto_masuk) }}" alt="Foto Masuk" class="w-12 h-12 object-cover rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm group-hover:opacity-80 transition">
                                    </a>
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-[10px] text-gray-400">No Photo</div>
                                @endif
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                    {{ $presensi->absen_masuk ? substr($presensi->absen_masuk, 0, 5) : '-' }}
                                </span>
                            </div>
                        </td>

                        <!-- Foto + Jam Keluar -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                @if($presensi->foto_keluar)
                                    <a href="{{ asset('storage/' . $presensi->foto_keluar) }}" target="_blank" class="block group relative">
                                        <img src="{{ asset('storage/' . $presensi->foto_keluar) }}" alt="Foto Keluar" class="w-12 h-12 object-cover rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm group-hover:opacity-80 transition">
                                    </a>
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-[10px] text-gray-400">No Photo</div>
                                @endif
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                    {{ $presensi->absen_keluar ? substr($presensi->absen_keluar, 0, 5) : '-' }}
                                </span>
                            </div>
                        </td>

                        <!-- Logbook -->
                        <td class="px-6 py-4 text-sm max-w-xs">
                            <span class="block whitespace-normal break-words">{{ $presensi->logBooks->first()?->kegiatan ?? '-' }}</span>
                        </td>

                        <!-- Tombol Aksi Edit -->
                        <td class="px-6 py-4 text-center">
                            <button 
                                type="button" 
                                wire:click="editAbsen({{ $presensi->presensi_id }})"
                                class="px-3 py-1.5 text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition shadow-sm flex items-center gap-1 mx-auto"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada data absensi ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $presensis->links() }}
        </div>
    </div>

    <!-- MODAL EDIT ABSEN -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <!-- Header Modal -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Absensi - {{ $editNamaUser }}
                    </h3>
                    <button type="button" wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form Modal -->
                <form wire:submit.prevent="updateAbsen" class="p-5 space-y-4">
                    <!-- Status Kehadiran -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Status Kehadiran</label>
                        <select wire:model="editStatusKehadiran" class="w-full p-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="alfa">Alfa / Tidak Hadir</option>
                        </select>
                        @error('editStatusKehadiran') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jam Masuk & Jam Keluar -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Jam Masuk</label>
                            <input type="time" step="1" wire:model="editAbsenMasuk" class="w-full p-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('editAbsenMasuk') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Jam Keluar</label>
                            <input type="time" step="1" wire:model="editAbsenKeluar" class="w-full p-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('editAbsenKeluar') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Logbook / Kegiatan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Kegiatan Logbook</label>
                        <textarea wire:model="editLogbook" rows="3" class="w-full p-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Isi catatan logbook..."></textarea>
                        @error('editLogbook') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer / Actions -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closeEditModal" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showMap)
        @include('livewire.components.map')
    @endif

</div>