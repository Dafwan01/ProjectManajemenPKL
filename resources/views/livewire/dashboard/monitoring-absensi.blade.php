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
                    <th scope="col" class="px-6 py-3">Hari</th>
                    <th scope="col" class="px-6 py-3">Kehadiran</th>
                    <th scope="col" class="px-6 py-3">Foto</th>
                    <th scope="col" class="px-6 py-3">Logbook</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensis as $presensi)
                    @foreach($presensi->logBooks as $logBook)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $logBook->user->nama }}
                            </th>
                            <td class="px-6 py-4">{{ $logBook->user->asal_sekolah ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $presensi->tanggal?->format('d-m-Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="capitalize px-2 py-1 text-xs font-semibold rounded 
                                    @if($presensi->status_kehadiran?->value === 'hadir')
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($presensi->status_kehadiran?->value === 'izin')
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @endif
                                ">
                                    {{ $presensi->status_kehadiran?->value ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($presensi->foto_masuk)
                                    <img src="{{ asset('storage/' . $presensi->foto_masuk) }}" alt="Foto Masuk" class="w-12 h-12 rounded">
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm max-w-xs">
                                <span class="truncate block" title="{{ $logBook->kegiatan ?? '-' }}">{{ $logBook->kegiatan ?? '-' }}</span>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data absensi ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $presensis->links() }}
        </div>
    </div>

    @if($showMap)
        @include('livewire.components.map')
    @endif

</div>