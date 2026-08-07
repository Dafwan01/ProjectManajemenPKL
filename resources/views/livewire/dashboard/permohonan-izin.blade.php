<div>
    <!-- JUDUL HEADER & INDIKATOR PENDING -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white uppercase">Permohonan Izin / Sakit / Absen</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola persetujuan pengajuan izin, sakit, dan ketidakhadiran peserta PKL.</p>
        </div>
        @if($totalPending > 0)
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-semibold rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 shadow-sm self-start sm:self-auto">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                {{ $totalPending }} Menunggu Persetujuan
            </span>
        @endif
    </div>

    <!-- Pesan Notifikasi Flash -->
    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-700 dark:text-emerald-400 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="p-4 mb-6 text-sm text-amber-700 dark:text-amber-400 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('warning') }}</span>
        </div>
    @endif

    <div class="relative overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl">
        <!-- Bar Atas / Filter & Pencarian -->
        <div class="flex items-center justify-between p-5 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 gap-4 overflow-x-auto no-scrollbar">
            
            <div class="flex items-center flex-nowrap shrink-0 gap-3">
                <!-- Pencarian Nama -->
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block p-2.5 ps-10 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 rounded-2xl w-56 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Cari nama peserta...">
                </div>

                <!-- Pemilih Tanggal -->
                <div class="relative shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3.5 pointer-events-none z-10">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input 
                        type="date" 
                        wire:model.live="tanggal"
                        class="block p-2.5 ps-10 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 rounded-2xl w-48 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    >
                </div>

                @if($tanggal)
                    <button 
                        type="button"
                        wire:click="resetFilterTanggal"
                        class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500 hover:underline shrink-0"
                    >
                        Tampilkan Semua Tanggal
                    </button>
                @endif

                <!-- Filter Status -->
                <select wire:model.live="filterStatus" class="bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 text-gray-900 dark:text-gray-100 text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 shrink-0 transition">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Persetujuan</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <!-- Tabel Data Permohonan -->
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-5 py-4 w-[18%] font-bold">Nama Lengkap</th>
                        <th scope="col" class="px-5 py-4 w-[18%] font-bold">Tanggal Pengajuan</th>
                        <th scope="col" class="px-5 py-4 w-[12%] font-bold">Jenis</th>
                        <th scope="col" class="px-5 py-4 w-[24%] font-bold">Alasan</th>
                        <th scope="col" class="px-5 py-4 w-[14%] font-bold">Status</th>
                        <th scope="col" class="px-5 py-4 w-[14%] font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60">
                    @forelse($permohonans as $permohonan)
                        @php
                            $jenisColor = match($permohonan->jenis) {
                                'sakit' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                                'absen' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
                                default => 'bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-500/20',
                            };

                            $absenLabel = null;
                            if ($permohonan->jenis === 'absen') {
                                $bagian = [];
                                if ($permohonan->absen_masuk) $bagian[] = 'Masuk';
                                if ($permohonan->absen_pulang) $bagian[] = 'Pulang';
                                $absenLabel = implode(' & ', $bagian);
                            }
                        @endphp
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition" wire:key="permohonan-{{ $permohonan->permohonan_id }}">
                            <th scope="row" class="px-5 py-4 font-semibold text-gray-900 dark:text-white truncate">
                                {{ $permohonan->user->nama ?? $permohonan->user->name ?? '-' }}
                            </th>
                            <!-- Format Tanggal Baku Indonesia -->
                            <td class="px-5 py-4 text-xs font-medium text-gray-600 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($permohonan->tanggal_permohonan)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center justify-center w-full capitalize px-2.5 py-1 text-xs font-semibold rounded-full border {{ $jenisColor }}">
                                    {{ $permohonan->jenis }}
                                </span>
                                @if($absenLabel)
                                    <div class="text-center text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">({{ $absenLabel }})</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="block truncate text-gray-600 dark:text-gray-300" title="{{ $permohonan->alasan }}">{{ $permohonan->alasan }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $statusText = match($permohonan->status) {
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        default => 'Menunggu',
                                    };

                                    $statusColor = match($permohonan->status) {
                                        'disetujui' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                                        'ditolak' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
                                        default => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                                    };
                                @endphp
                                <span class="inline-flex items-center justify-center w-full capitalize px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button 
                                    type="button"
                                    wire:click="openDetail({{ $permohonan->permohonan_id }})"
                                    class="p-2 rounded-xl text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 border border-transparent hover:border-blue-200 dark:hover:border-blue-500/20 transition"
                                    title="Lihat Detail"
                                >
                                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">Tidak ada permohonan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            {{ $permohonans->links() }}
        </div>
    </div>

    <!-- Modal Detail Permohonan -->
    @if($showDetailModal)
        @php
            $permohonan = \App\Models\PermohonanIzin::with('user')->find($selectedId);
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm px-4 py-6" @click="$event.target === $el && $wire.closeDetail()" wire:key="detail-modal-{{ $selectedId }}">
            <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 shadow-2xl" @click.stop>
                @if($permohonan)
                    @php
                        $tglAwalObj = $permohonan->tanggal_awal ? \Carbon\Carbon::parse($permohonan->tanggal_awal) : \Carbon\Carbon::parse($permohonan->tanggal_permohonan);
                        $tglAkhirObj = $permohonan->tanggal_akhir ? \Carbon\Carbon::parse($permohonan->tanggal_akhir) : $tglAwalObj;
                        
                        $tglMulaiFormatted = $tglAwalObj->translatedFormat('d F Y');
                        $tglAkhirFormatted = $permohonan->tanggal_akhir ? $tglAkhirObj->translatedFormat('d F Y') : null;

                        $jumlahHari = $permohonan->jumlah_hari ?? ($tglAwalObj->diffInDays($tglAkhirObj) + 1);

                        $absenLabelModal = null;
                        if ($permohonan->jenis === 'absen') {
                            $bagian = [];
                            if ($permohonan->absen_masuk) $bagian[] = 'Absen Masuk';
                            if ($permohonan->absen_pulang) $bagian[] = 'Absen Pulang';
                            $absenLabelModal = implode(' & ', $bagian);
                        }
                    @endphp

                    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Detail Permohonan</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $permohonan->user->nama ?? $permohonan->user->name ?? 'Pemohon' }} 
                            &bull; 
                            {{ $permohonan->user->sekolah->nama_sekolah ?? $permohonan->user->sekolah?->nama_sekolah ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Grid 4 Kolom: Jenis, Tanggal Pengajuan, Rentang Waktu, & Durasi -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <span class="block mb-1 text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</span>
                                <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white capitalize">{{ $permohonan->jenis }}</p>
                            </div>
                            <div>
                                <span class="block mb-1 text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</span>
                                <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($permohonan->tanggal_permohonan)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                            <div>
                                <span class="block mb-1 text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal / Rentang</span>
                                <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                    @if($tglAkhirFormatted && $tglMulaiFormatted !== $tglAkhirFormatted)
                                        {{ $tglMulaiFormatted }} s.d. {{ $tglAkhirFormatted }}
                                    @else
                                        {{ $tglMulaiFormatted }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="block mb-1 text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durasi</span>
                                <p class="text-xs sm:text-sm font-semibold text-blue-600 dark:text-blue-400">
                                    {{ $jumlahHari }} hari
                                </p>
                            </div>
                        </div>

                        <!-- Info Absen Masuk/Pulang (Hanya muncul jika Jenis = Absen) -->
                        @if($absenLabelModal)
                            <div>
                                <span class="block mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Diajukan Untuk</span>
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 bg-rose-50 dark:bg-rose-500/10 p-3 rounded-2xl border border-rose-200 dark:border-rose-500/20">
                                    {{ $absenLabelModal }}
                                </p>
                            </div>
                        @endif

                        <!-- Alamat Selama Izin -->
                        @if(!empty($permohonan->alamat_izin))
                            <div>
                                <span class="block mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alamat Selama Izin</span>
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-800/60 p-3 rounded-2xl border border-gray-200 dark:border-gray-800 flex items-start gap-1.5">
                                    <span class="shrink-0">📍</span>
                                    <span>{{ $permohonan->alamat_izin }}</span>
                                </p>
                            </div>
                        @endif

                        <div>
                            <span class="block mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</span>
                            <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-800/60 p-3.5 rounded-2xl border border-gray-200 dark:border-gray-800">{{ $permohonan->alasan }}</p>
                        </div>

                        @if($permohonan->lampiran)
                            <div>
                                <span class="block mb-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lampiran</span>
                                <a href="{{ asset('storage/' . $permohonan->lampiran) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-2xl hover:bg-blue-100 dark:hover:bg-blue-500/20 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                                    </svg>
                                    Lihat Lampiran
                                </a>
                            </div>
                        @endif

                        <div>
                            <span class="block mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Saat Ini</span>
                            @php
                                $statusModalText = match($permohonan->status) {
                                    'disetujui' => 'Disetujui',
                                    'ditolak' => 'Ditolak',
                                    default => 'Menunggu Persetujuan',
                                };

                                $statusColor = match($permohonan->status) {
                                    'disetujui' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                                    'ditolak' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
                                    default => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                                };
                            @endphp
                            <span class="inline-flex items-center capitalize px-3 py-1 text-xs font-semibold rounded-full border {{ $statusColor }}">
                                {{ $statusModalText }}
                            </span>
                        </div>

                        <div>
                            <label class="block mb-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Catatan Admin (Opsional)</label>
                            <textarea 
                                wire:model="catatanAdmin" rows="3"
                                class="bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 text-gray-900 dark:text-gray-100 text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition"
                                placeholder="Tuliskan catatan tambahan untuk pemohon..."
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-5 mt-6 border-t border-gray-200 dark:border-gray-800">
                        <button type="button" wire:click="closeDetail" class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition">
                            Tutup
                        </button>

                        @if($permohonan->status === 'pending')
                            <button type="button" wire:click="tolak({{ $permohonan->permohonan_id }})" class="px-4 py-2.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-2xl shadow-md shadow-rose-600/20 transition">
                                Tolak
                            </button>
                            <button type="button" wire:click="setujui({{ $permohonan->permohonan_id }})" class="px-4 py-2.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-2xl shadow-md shadow-emerald-600/20 transition">
                                Setujui
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
