<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-white uppercase">Monitoring Absensi</h1>
        <p class="text-sm text-slate-400 mt-1">Pantau dan kelola data kehadiran serta logbook harian peserta PKL.</p>
    </div>

    <div class="relative overflow-hidden bg-[#0d1322] border border-slate-800/80 rounded-2xl shadow-xl">
        <!-- Top Bar -->
        <div class="flex items-center justify-between p-4 bg-[#0d1322] border-b border-slate-800/80 gap-4 overflow-x-auto no-scrollbar">
            
            <!-- Date Picker -->
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none z-10">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input 
                        type="date" 
                        wire:model.live="tanggal"
                        class="block p-2.5 ps-10 text-sm text-slate-200 border border-slate-700/80 rounded-xl w-52 bg-[#0b0f19] focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    >
                </div>
            </div>

            <!-- Tombol Lihat Lokasi -->
            <button 
                type="button"
                wire:click="openMap"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-500 shadow-md shadow-blue-600/20 transition shrink-0"
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
            <table class="w-full text-sm text-left text-slate-300">
                <thead class="text-xs uppercase bg-[#080c14] text-slate-400 border-b border-slate-800/80">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama</th>
                        <th scope="col" class="px-6 py-4">Asal Sekolah</th>
                        <th scope="col" class="px-6 py-4">Kehadiran</th>
                        <th scope="col" class="px-6 py-4 text-center">Absen Masuk</th>
                        <th scope="col" class="px-6 py-4 text-center">Absen Keluar</th>
                        <th scope="col" class="px-6 py-4">Logbook</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($presensis as $presensi)
                        @php
                            $user = $presensi->user ?? $presensi->logBooks->first()?->user;
                            $status = strtolower($presensi->status_kehadiran?->value ?? $presensi->status_kehadiran ?? '');
                        @endphp
                        <tr class="bg-[#0d1322] hover:bg-slate-800/40 transition">
                            <th scope="row" class="px-6 py-4 font-semibold text-white whitespace-nowrap">
                                {{ $user->nama ?? $user->name ?? '-' }}
                            </th>
                            <td class="px-6 py-4 text-slate-400">{{ $user->asal_sekolah ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium tracking-wide uppercase border
                                    @if($status === 'hadir')
                                        bg-emerald-500/10 text-emerald-400 border-emerald-500/20
                                    @elseif($status === 'izin' || $status === 'sakit')
                                        bg-amber-500/10 text-amber-400 border-amber-500/20
                                    @else
                                        bg-rose-500/10 text-rose-400 border-rose-500/20
                                    @endif
                                ">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        @if($status === 'hadir') bg-emerald-400
                                        @elseif($status === 'izin' || $status === 'sakit') bg-amber-400
                                        @else bg-rose-400 @endif">
                                    </span>
                                    {{ $presensi->status_kehadiran?->value ?? $presensi->status_kehadiran ?? '-' }}
                                </span>
                            </td>
                            
                            <!-- Foto + Jam Masuk -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    @if($presensi->foto_masuk)
                                        <a href="{{ asset('storage/' . $presensi->foto_masuk) }}" target="_blank" class="block group relative">
                                            <img src="{{ asset('storage/' . $presensi->foto_masuk) }}" alt="Foto Masuk" class="w-12 h-12 object-cover rounded-xl border border-slate-700/80 shadow-md group-hover:border-blue-500 transition">
                                        </a>
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-[#0b0f19] border border-dashed border-slate-800 flex items-center justify-center text-[10px] text-slate-500">No Photo</div>
                                    @endif
                                    <span class="text-xs font-medium text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-lg border border-emerald-500/20">
                                        In: {{ $presensi->absen_masuk ? substr($presensi->absen_masuk, 0, 5) : '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Foto + Jam Keluar -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    @if($presensi->foto_keluar)
                                        <a href="{{ asset('storage/' . $presensi->foto_keluar) }}" target="_blank" class="block group relative">
                                            <img src="{{ asset('storage/' . $presensi->foto_keluar) }}" alt="Foto Keluar" class="w-12 h-12 object-cover rounded-xl border border-slate-700/80 shadow-md group-hover:border-blue-500 transition">
                                        </a>
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-[#0b0f19] border border-dashed border-slate-800 flex items-center justify-center text-[10px] text-slate-500">No Photo</div>
                                    @endif
                                    <span class="text-xs font-medium text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-lg border border-amber-500/20">
                                        Out: {{ $presensi->absen_keluar ? substr($presensi->absen_keluar, 0, 5) : '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Logbook -->
                            <td class="px-6 py-4 text-sm max-w-xs text-slate-300">
                                <span class="block whitespace-normal break-words leading-relaxed">{{ $presensi->logBooks->first()?->kegiatan ?? '-' }}</span>
                            </td>

                            <!-- Tombol Aksi Edit -->
                            <td class="px-6 py-4 text-center">
                                <button 
                                    type="button" 
                                    wire:click="editAbsen({{ $presensi->presensi_id }})"
                                    class="px-3.5 py-2 text-xs font-medium text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 rounded-xl transition shadow-sm flex items-center gap-1.5 mx-auto"
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
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500">Tidak ada data absensi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-800/80 bg-[#0d1322]">
            {{ $presensis->links() }}
        </div>
    </div>

    <!-- MODAL EDIT ABSEN -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="relative w-full max-w-md bg-[#0d1322] rounded-2xl shadow-2xl border border-slate-800/80 overflow-hidden">
                <!-- Header Modal -->
                <div class="flex items-center justify-between p-5 border-b border-slate-800/80 bg-[#080c14]">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Absensi - {{ $editNamaUser }}
                    </h3>
                    <button type="button" wire:click="closeEditModal" class="text-slate-400 hover:text-white p-1 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form Modal -->
                <form wire:submit.prevent="updateAbsen" class="p-6 space-y-4">
                    <!-- Status Kehadiran -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Status Kehadiran</label>
                        <select wire:model="editStatusKehadiran" class="w-full p-3 text-sm rounded-xl border border-slate-700/80 bg-[#0b0f19] text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="alfa">Alfa / Tidak Hadir</option>
                        </select>
                        @error('editStatusKehadiran') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jam Masuk & Jam Keluar -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Jam Masuk</label>
                            <input type="time" step="1" wire:model="editAbsenMasuk" class="w-full p-3 text-sm rounded-xl border border-slate-700/80 bg-[#0b0f19] text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @error('editAbsenMasuk') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Jam Keluar</label>
                            <input type="time" step="1" wire:model="editAbsenKeluar" class="w-full p-3 text-sm rounded-xl border border-slate-700/80 bg-[#0b0f19] text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @error('editAbsenKeluar') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Logbook / Kegiatan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Kegiatan Logbook</label>
                        <textarea wire:model="editLogbook" rows="3" class="w-full p-3 text-sm rounded-xl border border-slate-700/80 bg-[#0b0f19] text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Isi catatan logbook..."></textarea>
                        @error('editLogbook') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer / Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                        <button type="button" wire:click="closeEditModal" class="px-4 py-2.5 text-xs font-medium text-slate-300 bg-slate-800/60 hover:bg-slate-700/60 border border-slate-700/50 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2.5 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-500 rounded-xl shadow-md shadow-amber-600/20 transition">
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