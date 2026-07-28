<div>
    <div class="w-full mx-auto max-w-7xl">

        <!-- Header Judul -->
       <!-- Section Filter -->
<div class="bg-gray-800 p-5 rounded-xl border border-gray-700/60 shadow-lg mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between transition-all duration-300">
    
    <!-- Filter Range Tanggal -->
    <div class="flex items-center gap-2 w-full sm:w-auto">
        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        <input 
            type="date" 
            wire:model.live="tanggalMulai"
            class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 shadow-inner"
        >
        <span class="text-gray-500 text-xs">s/d</span>
        <input 
            type="date" 
            wire:model.live="tanggalSelesai"
            class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 shadow-inner"
        >

        @if($tanggalMulai || $tanggalSelesai)
            <button 
                type="button"
                wire:click="resetFilterTanggal"
                class="text-xs text-blue-400 hover:text-blue-300 whitespace-nowrap ml-1"
            >
                Reset
            </button>
        @endif
    </div>

    <!-- Filter Status -->
    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
        <label class="text-xs text-gray-400 font-medium whitespace-nowrap">Filter Status:</label>
        <select wire:model.live="filterStatus" class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 shadow-inner transition cursor-pointer">
            <option value="semua">Semua Status Kehadiran</option>
            <option value="hadir">Status: HADIR</option>
            <option value="izin">Status: IZIN</option>
            <option value="sakit">Status: SAKIT</option>
        </select>
    </div>
</div>

        <!-- Section Tabel Riwayat -->
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700/60 shadow-xl w-full mb-8 min-h-[400px]">
            <div class="relative overflow-x-auto shadow-md rounded-lg">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs text-gray-200 uppercase bg-gray-700 border-b border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">HARI / TANGGAL</th>
                            <th scope="col" class="px-6 py-4">JAM MASUK / PULANG</th>
                            <th scope="col" class="px-6 py-4">KEHADIRAN</th>
                            <th scope="col" class="px-6 py-4">LOGBOOK HARIAN</th>
                            <th scope="col" class="px-6 py-4 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/60">
                        @forelse ($dataRiwayat as $item)
                            <tr class="bg-gray-800 hover:bg-gray-750/50 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-white whitespace-nowrap">{{ $item['tanggal'] }}</td>

                                <!-- Kolom Jam Masuk & Jam Pulang -->
                                <td class="px-6 py-4 text-xs font-mono whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-green-400 mb-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                        <span>In: {{ $item['jam_masuk'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-orange-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                        <span>Out: {{ $item['jam_pulang'] ?? '-' }}</span>
                                    </div>
                                </td>

                                <!-- Kolom Kehadiran -->
                                <td class="px-6 py-4">
                                    @if(strtoupper($item['status']) == 'HADIR')
                                        <span class="bg-green-950 text-green-400 text-[11px] font-semibold px-2.5 py-1 rounded-md border border-green-800 flex items-center gap-1.5 w-fit">
                                            <span class="h-2 w-2 rounded-full bg-green-500 inline-block"></span>
                                            HADIR
                                        </span>
                                    @elseif(strtoupper($item['status']) == 'IZIN')
                                        <span class="bg-yellow-950 text-yellow-400 text-[11px] font-semibold px-2.5 py-1 rounded-md border border-yellow-800 flex items-center gap-1.5 w-fit">
                                            <span class="h-2 w-2 rounded-full bg-yellow-500 inline-block"></span>
                                            IZIN
                                        </span>
                                    @elseif(strtoupper($item['status']) == 'SAKIT')
                                        <span class="bg-red-950 text-red-400 text-[11px] font-semibold px-2.5 py-1 rounded-md border border-red-800 flex items-center gap-1.5 w-fit">
                                            <span class="h-2 w-2 rounded-full bg-red-500 inline-block"></span>
                                            SAKIT
                                        </span>
                                    @else
                                        <span class="bg-gray-700 text-gray-300 text-[11px] font-semibold px-2.5 py-1 rounded-md border border-gray-600 flex items-center gap-1.5 w-fit">
                                            <span class="h-2 w-2 rounded-full bg-gray-500 inline-block"></span>
                                            {{ $item['status'] }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Kolom Logbook Harian -->
                                <td class="px-6 py-4 max-w-sm text-xs text-gray-300 leading-relaxed">
                                    {{ $item['logbook'] ?? 'Tidak mengisi logbook (Presensi Masuk)' }}
                                </td>

                                <!-- Kolom Aksi Edit -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <button 
                                        type="button" 
                                        wire:click="editLogbook({{ $item['id'] }})"
                                        class="text-blue-400 hover:text-blue-300 bg-gray-700/50 hover:bg-blue-600/20 border border-gray-600 hover:border-blue-500 p-2 rounded-lg transition inline-flex items-center gap-1 text-xs"
                                        title="Edit Logbook">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Edit</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-xs">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Data riwayat presensi tidak ditemukan atau masih kosong.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $dataRiwayat->links() }}
            </div>
        </div>

        <!-- Modal Edit Logbook -->
        @if ($isEditModalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 transition-opacity">
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 max-w-lg w-full text-left shadow-2xl">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-3">
                        <h3 class="text-lg font-bold text-white">Edit Logbook Harian</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-white transition">✕</button>
                    </div>

                    <form wire:submit.prevent="updateLogbook">
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-300 mb-2">Isi Logbook Kegiatan:</label>
                            <textarea 
                                wire:model="editingLogbook" 
                                rows="4" 
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-xs text-white focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                                placeholder="Tuliskan catatan kegiatan harian..."></textarea>
                            @error('editingLogbook')
                                <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                                <span wire:loading.remove wire:target="updateLogbook">Simpan Perubahan</span>
                                <span wire:loading wire:target="updateLogbook">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>