<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Monitoring Absensi</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau dan kelola data kehadiran serta logbook harian peserta PKL.</p>
    </div>

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/40 border border-green-400 dark:border-green-600/60 text-green-700 dark:text-green-300 text-sm rounded-xl flex items-center gap-2 shadow">
            <svg class="w-4 h-4 text-green-500 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Disclaimer Status Kerja Default WFO -->
    <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50 text-blue-700 dark:text-blue-300 text-xs rounded-xl flex items-start gap-2 shadow-sm">
        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>
            <strong>Catatan:</strong> Status magang secara default diset <strong>WFO</strong>. Disarankan untuk melakukan pengaturan ulang status kerja (WFO/WFA) setiap awal minggu agar data monitoring tetap akurat.
        </span>
    </div>

    <div class="relative overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl">
        <!-- Top Bar -->
        <div class="flex items-center justify-between p-5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 gap-4 overflow-x-auto no-scrollbar">
            
            <!-- Date Picker -->
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none z-10">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input 
                        type="date" 
                        wire:model.live="tanggal"
                        class="block p-2.5 ps-10 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 rounded-2xl w-52 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    >
                </div>
            </div>

            <!-- Tombol Lihat Lokasi -->
            <button 
                type="button"
                wire:click="openMap"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md shadow-blue-600/20 transition shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Lihat Lokasi
            </button>
        </div>
        
        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Nama</th>
                        <th scope="col" class="px-6 py-4 font-bold">Asal Sekolah</th>
                        <th scope="col" class="px-6 py-4 font-bold">Kehadiran</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Absen Masuk</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Absen Keluar</th>
                        <th scope="col" class="px-6 py-4 font-bold">Logbook</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60">
                    @forelse($presensis as $presensi)
                        @php
                            $user = $presensi->user ?? $presensi->logBooks->first()?->user;
                            $status = strtolower($presensi->status_kehadiran?->value ?? $presensi->status_kehadiran ?? '');
                            
                            // Mapping hari Inggris ke Indonesia
                            $hariMapping = [
                                'monday' => 'senin',
                                'tuesday' => 'selasa',
                                'wednesday' => 'rabu',
                                'thursday' => 'kamis',
                                'friday' => 'jumat',
                                'saturday' => 'sabtu',
                                'sunday' => 'minggu',
                            ];
                            
                            // Cari jadwal user untuk hari presensi
                            $statusKerja = '-';
                            if ($user && $presensi->tanggal) {
                                // Ambil hari dari tanggal presensi dalam format Inggris
                                $hariInggris = strtolower(\Carbon\Carbon::parse($presensi->tanggal)->format('l'));
                                $hariIndonesia = $hariMapping[$hariInggris] ?? null;
                                
                                // Cek apakah user memiliki detail jadwal
                                if ($hariIndonesia && $user->detailJadwals) {
                                    foreach ($user->detailJadwals as $detail) {
                                        if ($detail->hari && strtolower($detail->hari) === $hariIndonesia) {
                                            $statusKerja = $detail->jadwal?->status_kerja?->value ?? '-';
                                            break;
                                        }
                                    }
                                }
                            }

                            // Cek panjang logbook untuk truncate
                            $kegiatan = $presensi->logBooks->first()?->kegiatan ?? '-';
                            $isLogbookPanjang = strlen($kegiatan) > 60;
                        @endphp
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">
                            <th scope="row" class="px-6 py-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $user->nama ?? $user->name ?? '-' }}
                            </th>
                           <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $user->sekolah?->nama_sekolah ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <!-- Status Kehadiran -->
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border
                                        @if($status === 'hadir')
                                            bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20
                                        @elseif($status === 'izin' || $status === 'sakit')
                                            bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20
                                        @else
                                            bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20
                                        @endif
                                    ">
                                        <span class="w-1.5 h-1.5 rounded-full 
                                            @if($status === 'hadir') bg-emerald-500 dark:bg-emerald-400
                                            @elseif($status === 'izin' || $status === 'sakit') bg-amber-500 dark:bg-amber-400
                                            @else bg-rose-500 dark:bg-rose-400 @endif">
                                        </span>
                                        {{ $presensi->status_kehadiran?->value ?? $presensi->status_kehadiran ?? '-' }}
                                    </span>
                                    
                                    <!-- Status Kerja (WFA/WFO) -->
                                    @if($statusKerja !== '-')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase border
                                            @if($statusKerja === 'WFH' || $statusKerja === 'WFA')
                                                bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20
                                            @else
                                                bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-500/20
                                            @endif
                                        ">
                                            <span class="w-1.5 h-1.5 rounded-full 
                                                @if($statusKerja === 'WFH' || $statusKerja === 'WFA') bg-blue-500 dark:bg-blue-400
                                                @else bg-purple-500 dark:bg-purple-400 @endif">
                                            </span>
                                            {{ $statusKerja }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    @if($presensi->foto_masuk)
                                        <a href="{{ asset('storage/' . $presensi->foto_masuk) }}" target="_blank" class="block group relative">
                                            <img src="{{ asset('storage/' . $presensi->foto_masuk) }}" alt="Foto Masuk" class="w-12 h-12 object-cover rounded-xl border border-gray-200 dark:border-gray-700/80 shadow-sm group-hover:border-blue-500 transition">
                                        </a>
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-dashed border-gray-300 dark:border-gray-800 flex items-center justify-center text-[10px] text-gray-400 dark:text-gray-500">No Photo</div>
                                    @endif
                                    <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-500/20">
                                        In: {{ $presensi->absen_masuk ? substr($presensi->absen_masuk, 0, 5) : '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Foto + Jam Keluar -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    @if($presensi->foto_keluar)
                                        <a href="{{ asset('storage/' . $presensi->foto_keluar) }}" target="_blank" class="block group relative">
                                            <img src="{{ asset('storage/' . $presensi->foto_keluar) }}" alt="Foto Keluar" class="w-12 h-12 object-cover rounded-xl border border-gray-200 dark:border-gray-700/80 shadow-sm group-hover:border-blue-500 transition">
                                        </a>
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-dashed border-gray-300 dark:border-gray-800 flex items-center justify-center text-[10px] text-gray-400 dark:text-gray-500">No Photo</div>
                                    @endif
                                    <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2.5 py-0.5 rounded-lg border border-amber-200 dark:border-amber-500/20">
                                        Out: {{ $presensi->absen_keluar ? substr($presensi->absen_keluar, 0, 5) : '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Logbook -->
                            <td class="px-6 py-4 text-sm max-w-xs text-gray-600 dark:text-gray-300">
                                @if($isLogbookPanjang)
                                    <button 
                                        type="button"
                                        wire:click="openLogbookModal(@js($kegiatan), @js($user->nama ?? $user->name ?? '-'))"
                                        class="text-left w-full hover:text-blue-600 dark:hover:text-blue-400 transition group"
                                    >
                                        <span class="block truncate leading-relaxed group-hover:underline">{{ Str::limit($kegiatan, 60) }}</span>
                                        <span class="text-[10px] text-blue-500 dark:text-blue-400 font-medium">Lihat selengkapnya</span>
                                    </button>
                                @else
                                    <span class="block whitespace-normal break-words leading-relaxed">{{ $kegiatan }}</span>
                                @endif
                            </td>

                            <!-- Tombol Aksi Edit -->
                            <td class="px-6 py-4 text-center">
                                <button 
                                    type="button" 
                                    wire:click="editAbsen({{ $presensi->presensi_id }})"
                                    class="px-3.5 py-2 text-xs font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30 rounded-xl transition shadow-sm flex items-center gap-1.5 mx-auto"
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
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">Tidak ada data absensi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            {{ $presensis->links() }}
        </div>
    </div>

    <!-- MODAL EDIT ABSEN -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                <!-- Header Modal -->
                <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-800/50">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Absensi - {{ $editNamaUser }}
                    </h3>
                    <button type="button" wire:click="closeEditModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-white p-1 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Warning di dalam modal (muncul sebelum submit jika ada error) -->
                @if (session()->has('warning'))
                    <div class="mx-6 mt-5 p-3 bg-amber-100 dark:bg-amber-900/40 border border-amber-400 dark:border-amber-600/60 text-amber-700 dark:text-amber-300 text-xs rounded-xl flex items-center gap-2 shadow">
                        <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                <!-- Form Modal -->
                <form wire:submit.prevent="updateAbsen" class="p-6 space-y-4">
                    <!-- Status Kehadiran -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Status Kehadiran</label>
                        <select wire:model="editStatusKehadiran" class="w-full p-3 text-sm rounded-2xl border border-gray-300 dark:border-gray-700/80 bg-gray-50 dark:bg-gray-800/60 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="alfa">Alfa / Tidak Hadir</option>
                        </select>
                        @error('editStatusKehadiran') <span class="text-xs text-rose-500 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jam Masuk & Jam Keluar -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Jam Masuk</label>
                            <input type="time" step="1" wire:model="editAbsenMasuk" class="w-full p-3 text-sm rounded-2xl border border-gray-300 dark:border-gray-700/80 bg-gray-50 dark:bg-gray-800/60 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @error('editAbsenMasuk') <span class="text-xs text-rose-500 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Jam Keluar</label>
                            <input type="time" step="1" wire:model="editAbsenKeluar" class="w-full p-3 text-sm rounded-2xl border border-gray-300 dark:border-gray-700/80 bg-gray-50 dark:bg-gray-800/60 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @error('editAbsenKeluar') <span class="text-xs text-rose-500 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Logbook / Kegiatan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Kegiatan Logbook</label>
                        <textarea wire:model="editLogbook" rows="3" class="w-full p-3 text-sm rounded-2xl border border-gray-300 dark:border-gray-700/80 bg-gray-50 dark:bg-gray-800/60 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Isi catatan logbook..."></textarea>
                        @error('editLogbook') <span class="text-xs text-rose-500 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer / Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button type="button" wire:click="closeEditModal" class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2.5 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-500 rounded-2xl shadow-md shadow-amber-600/20 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL DETAIL LOGBOOK -->
    @if($showLogbookModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                <!-- Header Modal -->
                <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-800/50">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Logbook - {{ $selectedLogbookUser }}
                    </h3>
                    <button type="button" wire:click="closeLogbookModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-white p-1 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Isi Logbook -->
                <div class="p-6">
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $selectedLogbookText }}</p>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 dark:border-gray-800">
                    <button type="button" wire:click="closeLogbookModal" class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showMap)
        @include('livewire.components.map')
    @endif

</div>