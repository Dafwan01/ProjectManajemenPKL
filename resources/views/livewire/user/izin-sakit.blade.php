<div>
    <div class="w-full mx-auto max-w-4xl">
        
        <!-- Header Judul Ringkas -->
        <div class="mb-4 border-b border-gray-800 pb-3">
            <h1 class="text-xl font-bold text-white tracking-wide">PENGAJUAN IZIN & SAKIT</h1>
            <p class="text-xs text-gray-400 mt-0.5">Formulir permohonan ketidakhadiran magang untuk ketiadaan sementara karena izin atau sakit.</p>
        </div>

        <!-- Flash Message Notification -->
        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-green-900/40 border border-green-600/60 text-green-300 text-xs rounded-lg flex items-center justify-between shadow">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
                <button type="button" class="text-green-400 hover:text-white text-xs" @click="$el.parentElement.remove()">✕</button>
            </div>
        @endif

        <!-- Card Container Compact Form -->
        <div class="bg-gray-800 p-4 sm:p-5 rounded-xl border border-gray-700/60 shadow-lg">
            <form wire:submit.prevent="kirimPengajuan" class="space-y-4">
                
                <!-- Baris 1: Inline Grid (Tipe Pengajuan, Tanggal Mulai, Tanggal Selesai) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    
                    <!-- Dropdown Tipe Pengajuan -->
                    <div>
                        <label for="tipePengajuan" class="block text-[11px] font-medium text-gray-300 mb-1">Tipe Pengajuan</label>
                        <div class="relative">
                            <select 
                                id="tipePengajuan" 
                                wire:model.live="tipePengajuan"
                                class="w-full bg-gray-900 border border-gray-700 text-white text-xs rounded-lg px-3 py-2 pr-8 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none appearance-none cursor-pointer">
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="absen">Absen</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('tipePengajuan')
                            <span class="text-[10px] text-red-400 mt-0.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="tanggalMulai" class="block text-[11px] font-medium text-gray-300 mb-1">Tanggal Mulai</label>
                        <input 
                            type="date" 
                            id="tanggalMulai" 
                            wire:model="tanggalMulai"
                            class="w-full bg-gray-900 border border-gray-700 text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none" 
                        />
                        @error('tanggalMulai')
                            <span class="text-[10px] text-red-400 mt-0.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label for="tanggalSelesai" class="block text-[11px] font-medium text-gray-300 mb-1">Tanggal Selesai</label>
                        <input 
                            type="date" 
                            id="tanggalSelesai" 
                            wire:model="tanggalSelesai"
                            class="w-full bg-gray-900 border border-gray-700 text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none" 
                        />
                        @error('tanggalSelesai')
                            <span class="text-[10px] text-red-400 mt-0.5 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Baris 2: Textarea Alasan (Compact Rows) -->
                <div>
                    <label for="alasan" class="block text-[11px] font-medium text-gray-300 mb-1">Alasan / Keterangan</label>
                    <textarea 
                        id="alasan" 
                        wire:model="alasan" 
                        rows="2" 
                        placeholder="Tuliskan alasan pengajuan izin atau sakit secara jelas..." 
                        class="w-full bg-gray-900 border border-gray-700 text-white text-xs rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none leading-relaxed resize-none"></textarea>
                    @error('alasan')
                        <span class="text-[10px] text-red-400 mt-0.5 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Baris 3: Tombol Submit Compact -->
                <div class="flex flex-col items-end gap-2 pt-1">
                    <p class="text-[10px] text-gray-400 italic">Silakan pilih jenis pengajuan dan lengkapi alasan keterangan.</p>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto text-white bg-blue-600 hover:bg-blue-500 focus:ring-2 focus:ring-blue-800 font-medium rounded-lg text-xs px-5 py-2.5 transition duration-150 flex items-center justify-center gap-2 shadow-md">
                        <span wire:loading.remove wire:target="kirimPengajuan">
                            {{ $tipePengajuan === 'absen' ? 'Kirim Pengajuan Absen' : 'Kirim Pengajuan ' . ucfirst($tipePengajuan) }}
                        </span>
                        <span wire:loading wire:target="kirimPengajuan">Mengirim Data...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Riwayat Pengajuan -->
        <div class="w-full mx-auto max-w-4xl mt-8">
            <div class="mb-4 border-b border-gray-800 pb-3">
                <h2 class="text-lg font-bold text-white tracking-wide">Riwayat Pengajuan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Daftar pengajuan izin, sakit, dan absen yang sudah pernah dikirim.</p>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-700/60 bg-gray-900 shadow-sm">
                <table class="min-w-full text-left text-xs text-gray-300">
                    <thead class="bg-gray-800 text-gray-400 uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-3 py-3">Nama</th>
                            <th class="px-3 py-3">Tanggal</th>
                            <th class="px-3 py-3">Tipe</th>
                            <th class="px-3 py-3">Alasan</th>
                            <th class="px-3 py-3">Status Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $item)
                            @php
                                $statusPengajuan = $item['status_pengajuan'] ?? 'pending';
                                $statusBadge = match($statusPengajuan) {
                                    'diterima' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                                    'ditolak'  => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    default    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                };
                            @endphp
                            <tr class="border-t border-gray-800 hover:bg-gray-800/70 transition-colors">
                                <td class="px-3 py-3 text-gray-200 w-1/5">{{ $item['nama'] }}</td>
                                <td class="px-3 py-3 text-gray-200 w-1/5">{{ $item['tanggal'] }}</td>
                                <td class="px-3 py-3 capitalize">{{ strtolower($item['status']) }}</td>
                                <td class="px-3 py-3 text-gray-300 truncate max-w-[25rem]">{{ $item['logbook'] }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-semibold rounded-full {{ $statusBadge }} capitalize">{{ $statusPengajuan }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-gray-500">Belum ada pengajuan yang tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>