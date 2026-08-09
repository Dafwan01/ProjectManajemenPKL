<div>
    <div class="w-full mx-auto max-w-4xl">
        
        <!-- Header Judul Ringkas -->
        <div class="mb-4 border-b border-gray-200 dark:border-gray-800 pb-3">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-wide">PENGAJUAN IZIN, SAKIT & ABSEN</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Formulir permohonan ketidakhadiran magang untuk ketiadaan sementara karena izin, sakit, atau absen.</p>
        </div>

        <!-- Flash Message Notification -->
        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/40 border border-green-400 dark:border-green-600/60 text-green-700 dark:text-green-300 text-xs rounded-lg flex items-center justify-between shadow">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
                <button type="button" class="text-green-600 dark:text-green-400 hover:text-gray-900 dark:hover:text-white text-xs" @click="$el.parentElement.remove()">✕</button>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="mb-4 p-3 bg-amber-100 dark:bg-amber-900/40 border border-amber-400 dark:border-amber-600/60 text-amber-700 dark:text-amber-300 text-xs rounded-lg flex items-center justify-between shadow">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('warning') }}</span>
                </div>
                <button type="button" class="text-amber-600 dark:text-amber-400 hover:text-gray-900 dark:hover:text-white text-xs" @click="$el.parentElement.remove()">✕</button>
            </div>
        @endif
        
        @if($sudahAdaPengajuan)
    <div class="flex items-center gap-2 text-xs bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700/50 text-red-600 dark:text-red-400 px-3 py-2 rounded-lg">
        ⚠️ Kamu sudah memiliki pengajuan untuk tanggal ini. Tidak bisa mengirim pengajuan baru.
    </div>
@endif

        <!-- Card Container Compact Form -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-lg">
            <form wire:submit.prevent="kirimPengajuan" class="space-y-4">

                @php
                    $isTipeAbsen = in_array($tipePengajuan, ['absen', 'absen_pulang']);
                @endphp

                <!-- Baris 1: Dynamic Grid bergantung tipe pengajuan -->
                <div class="grid grid-cols-1 {{ $isTipeAbsen ? 'md:grid-cols-2' : 'md:grid-cols-3' }} gap-3">
                    
                    <!-- Dropdown Tipe Pengajuan -->
                    <div>
                        <label for="tipePengajuan" class="block text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Pengajuan</label>
                        <div class="relative">
                            <select 
                                id="tipePengajuan" 
                                wire:model.live="tipePengajuan"
                                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg px-3 py-2 pr-8 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none appearance-none cursor-pointer">
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="absen">Absen</option>
                                <option value="absen_pulang">Absen Pulang</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('tipePengajuan')
                            <span class="text-[10px] text-red-500 dark:text-red-400 mt-0.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tanggal Mulai / Tanggal Absen -->
                    <div>
                        <label for="tanggalMulai" class="block text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $isTipeAbsen ? 'Tanggal' : 'Tanggal Mulai' }}
                        </label>
                        <input 
                            type="date" 
                            id="tanggalMulai" 
                            wire:model.live="tanggalMulai"
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none" 
                        />
                        @error('tanggalMulai')
                            <span class="text-[10px] text-red-500 dark:text-red-400 mt-0.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai (Hanya muncul jika Izin atau Sakit) -->
                    @if (!$isTipeAbsen)
                        <div>
                            <label for="tanggalSelesai" class="block text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai</label>
                            <input 
                                type="date" 
                                id="tanggalSelesai" 
                                wire:model.live="tanggalSelesai"
                                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none" 
                            />
                            @error('tanggalSelesai')
                                <span class="text-[10px] text-red-500 dark:text-red-400 mt-0.5 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>

                <!-- Indikator Total Durasi Hari -->
                <div class="flex items-center gap-2 text-xs bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-lg w-fit">
                    <span class="text-[11px] font-medium">Total Durasi:</span>
                    <span class="font-bold">{{ $jumlahHari }} Hari</span>
                </div>

                <!-- Field Tambahan: Alamat Izin (Hanya Muncul Jika Tipe Pengajuan = Izin) -->
                @if ($tipePengajuan === 'izin')
                    <div>
                        <label for="alamatIzin" class="block text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Selama Izin</label>
                        <input 
                            type="text" 
                            id="alamatIzin" 
                            wire:model="alamatIzin" 
                            placeholder="Tuliskan alamat/lokasi keberadaan selama izin..." 
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none" 
                        />
                        @error('alamatIzin')
                            <span class="text-[10px] text-red-500 dark:text-red-400 mt-0.5 block">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Baris 2: Textarea Alasan -->
                <div>
                    <label for="alasan" class="block text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">Alasan / Keterangan</label>
                    <textarea 
                        id="alasan" 
                        wire:model="alasan" 
                        rows="2" 
                        placeholder="Tuliskan alasan pengajuan secara jelas..." 
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none leading-relaxed resize-none"></textarea>
                    @error('alasan')
                        <span class="text-[10px] text-red-500 dark:text-red-400 mt-0.5 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Baris 3: Tombol Submit Compact -->
                <div class="flex flex-col items-end gap-2 pt-1">
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 italic">Silakan lengkapi tanggal dan alasan keterangan sebelum mengirim.</p>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto text-white bg-blue-600 hover:bg-blue-500 focus:ring-2 focus:ring-blue-800 font-medium rounded-lg text-xs px-5 py-2.5 transition duration-150 flex items-center justify-center gap-2 shadow-md disabled:opacity-50"
                        @disabled($isLulus || $sudahAdaPengajuan )>
                        <span wire:loading.remove wire:target="kirimPengajuan">
                            Kirim Pengajuan {{ $tipePengajuan === 'absen_pulang' ? 'Absen Pulang' : ucfirst($tipePengajuan) }}
                        </span>
                        <span wire:loading wire:target="kirimPengajuan">Mengirim Data...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Riwayat Pengajuan -->
        <div class="w-full mx-auto max-w-4xl mt-8">
            <div class="mb-4 border-b border-gray-200 dark:border-gray-800 pb-3">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-wide">Riwayat Pengajuan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar pengajuan izin, sakit, dan absen yang sudah pernah dikirim.</p>
            </div>

            <!-- MOBILE & TABLET VIEW: Card Grid (Tampil di Layar < lg) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:hidden">
                @forelse($riwayat as $item)
                    @php
                        $tglAwal = \Carbon\Carbon::parse($item->tanggal_awal ?? $item->tanggal_permohonan)->format('d/m/Y');
                        $tglAkhir = $item->tanggal_akhir ? \Carbon\Carbon::parse($item->tanggal_akhir)->format('d/m/Y') : null;
                        $rangeTanggal = ($tglAkhir && $tglAwal !== $tglAkhir) ? $tglAwal . ' - ' . $tglAkhir : $tglAwal;

                        $rawStatus = strtolower($item->status);
                        $statusLabel = match($rawStatus) {
                            'disetujui', 'diterima', 'approved' => 'Disetujui',
                            'ditolak', 'rejected'               => 'Ditolak',
                            default                             => ucfirst($item->status),
                        };

                        $statusBadge = match($rawStatus) {
                            'disetujui', 'diterima', 'approved' => 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-400 dark:border-emerald-600/50',
                            'ditolak', 'rejected'               => 'bg-red-100 dark:bg-red-900/60 text-red-700 dark:text-red-300 border border-red-400 dark:border-red-600/50',
                            default                             => 'bg-yellow-100 dark:bg-yellow-900/60 text-yellow-700 dark:text-yellow-300 border border-yellow-400 dark:border-yellow-600/50',
                        };

                        $labelJenis = match(strtolower($item->jenis)) {
                            'absen'        => 'Absen',
                            'absen pulang' => 'Absen Pulang',
                            default        => ucfirst($item->jenis),
                        };
                    @endphp

                    <div wire:key="card-pengajuan-{{ $item->id ?? $loop->index }}" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-xl p-4 shadow-sm flex flex-col justify-between gap-3">
                        
                        <!-- Header Card: Tipe & Status Badge -->
                        <div class="flex items-center justify-between gap-2 border-b border-gray-100 dark:border-gray-700/60 pb-3">
                            <div>
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide block">
                                    {{ $labelJenis }}
                                </span>
                                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">
                                    {{ $nama }}
                                </span>
                            </div>

                            <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-semibold rounded-full capitalize {{ $statusBadge }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <!-- Mid Section: Tanggal & Durasi -->
                        <div class="grid grid-cols-2 gap-2 bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg text-xs">
                            <div class="flex items-center gap-1.5 text-gray-700 dark:text-gray-300">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $rangeTanggal }}</span>
                            </div>
                            <div class="text-right font-semibold text-gray-900 dark:text-white">
                                {{ $item->jumlah_hari ?? 1 }} Hari
                            </div>
                        </div>

                        <!-- Alasan & Alamat -->
                        <div class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50/50 dark:bg-gray-800/30 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700">
                            <span class="font-semibold text-gray-400 text-[10px] uppercase block mb-0.5">Alasan:</span>
                            <p class="leading-relaxed">{{ $item->alasan }}</p>
                            @if(!empty($item->alamat_izin))
                                <div class="text-[10px] text-gray-400 dark:text-gray-500 italic mt-1.5 pt-1 border-t border-gray-200/60 dark:border-gray-700">
                                    📍 {{ $item->alamat_izin }}
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-xl p-8 text-center text-gray-400 dark:text-gray-500 text-xs">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Belum ada pengajuan yang tersimpan di database.
                    </div>
                @endforelse
            </div>

            <!-- DESKTOP VIEW: Table (Tampil di Layar >= lg) -->
            <div class="hidden lg:block overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-900 shadow-sm">
                <table class="min-w-full text-left text-xs text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-3 py-3">Nama</th>
                            <th class="px-3 py-3">Tanggal</th>
                            <th class="px-3 py-3">Durasi</th>
                            <th class="px-3 py-3">Tipe</th>
                            <th class="px-3 py-3">Alasan</th>
                            <th class="px-3 py-3">Status Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $item)
                            @php
                                $tglAwal = \Carbon\Carbon::parse($item->tanggal_awal ?? $item->tanggal_permohonan)->format('d/m/Y');
                                $tglAkhir = $item->tanggal_akhir ? \Carbon\Carbon::parse($item->tanggal_akhir)->format('d/m/Y') : null;
                                
                                $rangeTanggal = ($tglAkhir && $tglAwal !== $tglAkhir) 
                                    ? $tglAwal . ' - ' . $tglAkhir 
                                    : $tglAwal;

                                $statusBadge = match(strtolower($item->status)) {
                                    'disetujui', 'diterima', 'approved' => 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-400 dark:border-emerald-600/50',
                                    'ditolak', 'rejected'               => 'bg-red-100 dark:bg-red-900/60 text-red-700 dark:text-red-300 border border-red-400 dark:border-red-600/50',
                                    default                             => 'bg-yellow-100 dark:bg-yellow-900/60 text-yellow-700 dark:text-yellow-300 border border-yellow-400 dark:border-yellow-600/50',
                                };

                                $labelJenis = match(strtolower($item->jenis)) {
                                    'absen'        => 'Absen',
                                    'absen pulang' => 'Absen Pulang',
                                    default        => ucfirst($item->jenis),
                                };
                            @endphp
                            <tr class="border-t border-gray-200 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800/70 transition-colors">
                                <td class="px-3 py-3 text-gray-800 dark:text-gray-200 font-medium whitespace-nowrap">{{ $nama }}</td>
                                <td class="px-3 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $rangeTanggal }}</td>
                                <td class="px-3 py-3 text-gray-800 dark:text-gray-200 font-medium whitespace-nowrap">{{ $item->jumlah_hari ?? 1 }} Hari</td>
                                <td class="px-3 py-3 uppercase font-semibold text-blue-600 dark:text-blue-400">
                                    {{ $labelJenis }}
                                </td>
                                <td class="px-3 py-3 text-gray-600 dark:text-gray-300 max-w-[20rem]" title="{{ $item->alasan }}">
                                    <div>{{ $item->alasan }}</div>
                                    @if(!empty($item->alamat_izin))
                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 italic mt-0.5">
                                            📍 {{ $item->alamat_izin }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-semibold rounded-full capitalize {{ $statusBadge }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-gray-400 dark:text-gray-500">Belum ada pengajuan yang tersimpan di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
               <div class="mt-4">
                {{ $riwayat->links() }}
            </div>
        </div>
    </div>
    <!-- MODAL KONFIRMASI TIMPA PRESENSI -->
</div>