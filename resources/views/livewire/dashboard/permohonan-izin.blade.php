<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permohonan Izin / Sakit</h1>
        @if($totalPending > 0)
            <span class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ $totalPending }} Menunggu Persetujuan
            </span>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <!-- Top Bar -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 gap-4 overflow-x-auto no-scrollbar">
            
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <!-- Search -->
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-56 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Cari nama...">
                </div>

                <!-- Date Picker -->
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none z-10">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input 
                        type="date" 
                        wire:model.live="tanggal"
                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-48 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                </div>

                @if($tanggal)
                    <button 
                        type="button"
                        wire:click="resetFilterTanggal"
                        class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline shrink-0"
                    >
                        Tampilkan Semua Tanggal
                    </button>
                @endif

                <!-- Filter Status -->
                <select wire:model.live="filterStatus" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white shrink-0">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <table class="w-full table-fixed text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3 w-[14%]">Nama</th>
            <th scope="col" class="px-4 py-3 w-[16%]">Asal Sekolah</th>
            <th scope="col" class="px-4 py-3 w-[10%]">Jenis</th>
            <th scope="col" class="px-4 py-3 w-[26%]">Alasan</th>
            <th scope="col" class="px-4 py-3 w-[12%]">Status</th>
            <th scope="col" class="px-4 py-3 w-[10%] text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($permohonans as $permohonan)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" wire:key="permohonan-{{ $permohonan->permohonan_id }}">
                <th scope="row" class="px-4 py-4 font-medium text-gray-900 dark:text-white truncate">
                    {{ $permohonan->user->nama }}
                </th>
                <td class="px-4 py-4 truncate">{{ $permohonan->user->asal_sekolah ?? '-' }}</td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center justify-center w-full capitalize px-2 py-1 text-xs font-semibold rounded-full {{ $permohonan->jenis === 'sakit' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                        {{ $permohonan->jenis }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <span class="block truncate" title="{{ $permohonan->alasan }}">{{ $permohonan->alasan }}</span>
                </td>
                <td class="px-4 py-4">
                    @php
                        $statusColor = match($permohonan->status) {
                            'disetujui' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            'ditolak' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        };
                    @endphp
                    <span class="inline-flex items-center justify-center w-full capitalize px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                        {{ $permohonan->status }}
                    </span>
                </td>
                <td class="px-4 py-4 text-center">
                    <button 
                        type="button"
                        wire:click="openDetail({{ $permohonan->permohonan_id }})"
                        class="p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-400 dark:hover:text-blue-400 dark:hover:bg-gray-700 transition-colors"
                        title="Lihat Detail"
                    >
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada permohonan ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

        <div class="p-4">
            {{ $permohonans->links() }}
        </div>
    </div>

    @if($showDetailModal)
        @php
            $permohonan = \App\Models\PermohonanIzin::with('user')->find($selectedId);
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 py-6" @click="$event.target === $el && $wire.closeDetail()" wire:key="detail-modal-{{ $selectedId }}">
            <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800" @click.stop>
                @if($permohonan)
                    <div class="mb-6 border-b pb-4 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detail Permohonan</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $permohonan->user->nama }} &bull; {{ $permohonan->user->asal_sekolah }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Jenis</span>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white capitalize">{{ $permohonan->jenis }}</p>
                            </div>
                            <div>
                                <span class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Tanggal</span>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $permohonan->tanggal->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div>
                            <span class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Alasan</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $permohonan->alasan }}</p>
                        </div>

                        @if($permohonan->lampiran)
                            <div>
                                <span class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Lampiran</span>
                                <a href="{{ asset('storage/' . $permohonan->lampiran) }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                                    </svg>
                                    Lihat Lampiran
                                </a>
                            </div>
                        @endif

                        <div>
                            <span class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Status Saat Ini</span>
                            @php
                                $statusColor = match($permohonan->status) {
                                    'disetujui' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'ditolak' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp
                            <span class="inline-flex items-center capitalize px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                {{ $permohonan->status }}
                            </span>
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Catatan Admin (Opsional)</label>
                            <textarea 
                                wire:model="catatanAdmin" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Catatan untuk pemohon..."
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t dark:border-gray-700">
                        <button type="button" wire:click="closeDetail" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Tutup
                        </button>

                        @if($permohonan->status === 'pending')
                            <button type="button" wire:click="tolak({{ $permohonan->permohonan_id }})" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                Tolak
                            </button>
                            <button type="button" wire:click="setujui({{ $permohonan->permohonan_id }})" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                Setujui
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>