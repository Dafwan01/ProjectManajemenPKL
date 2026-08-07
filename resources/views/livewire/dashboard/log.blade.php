<div class="p-6">
    <!-- Header & Filter -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Log Aktivitas Sistem</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Mencatat riwayat perubahan data, waktu, dan pengubahnya.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Filter Jenis Aksi -->
            <select 
                wire:model.live="eventFilter"
                class="w-full sm:w-40 px-3 py-2 text-sm border border-slate-200 dark:border-slate-800 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200"
            >
                <option value="">Semua Aksi</option>
                <option value="created">Created (Tambah)</option>
                <option value="updated">Updated (Ubah)</option>
                <option value="deleted">Deleted (Hapus)</option>
            </select>

            <!-- Search Input -->
            <div class="w-full sm:w-64">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari pengubah / aktivitas..."
                    class="w-full px-4 py-2 text-sm border border-slate-200 dark:border-slate-800 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500"
                >
            </div>
        </div>
    </div>

    <!-- Tabel Activity Log -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs text-slate-700 dark:text-slate-300 uppercase border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Diubah Oleh</th>
                        <th class="px-6 py-3">Aksi</th>
                        <th class="px-6 py-3">Target Data</th>
                        <th class="px-6 py-3">Rincian Perubahan (Sebelum &rarr; Sesudah)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <!-- Waktu -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 dark:text-slate-400">
                                <span class="font-medium text-slate-700 dark:text-slate-200 block">{{ $log->created_at->format('d M Y, H:i:s') }}</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            <!-- Diubah Oleh Siapa -->
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                {{ $log->causer?->nama ?? 'Sistem / Guest' }}
                            </td>

                            <!-- Badge Event/Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($log->event) {
                                        'created' => 'bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/50',
                                        'updated' => 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800/50',
                                        'deleted' => 'bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800/50',
                                        default   => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                    {{ ucfirst($log->event ?? $log->description) }}
                                </span>
                            </td>

                           
           <!-- Target Data (Subject) -->
<td class="px-6 py-4 text-xs whitespace-nowrap">
    <span class="font-semibold text-slate-800 dark:text-slate-200">
        {{ class_basename($log->subject_type ?? 'N/A') }}
    </span>

    @if($log->subject)
       @php
    $subjectDetail = match($log->subject_type) {
        'App\Models\User' => $log->subject->nama,
        'App\Models\presensi' => ($log->subject->user->nama ?? 'User terhapus') . ' — ' . optional($log->subject->tanggal)->format('d M Y'),
        'App\Models\Jadwal' => $log->subject->jam_masuk . ' - ' . $log->subject->jam_keluar 
            . ($log->subject->detailJadwals->isNotEmpty() 
                ? ' (' . $log->subject->detailJadwals->pluck('hari')->join(', ') . ')' 
                : ''),
        'App\Models\DetailJadwal' => ($log->subject->user->nama ?? 'User terhapus') . ' — ' . $log->subject->hari,
        'App\Models\log_book' => ($log->subject->user->nama ?? 'User terhapus') . ' — ' . \Illuminate\Support\Str::limit($log->subject->kegiatan, 30),
        'App\Models\PermohonanIzin' => ($log->subject->user->nama ?? 'User terhapus') 
    . ' — ' . $log->subject->jenis 
    . ' (' . optional($log->subject->tanggal_awal)->format('d M') . ' - ' . optional($log->subject->tanggal_akhir)->format('d M Y') . ')',
    'App\Models\Nilai' => ($log->subject->user->nama ?? 'User terhapus') . ' — Rata-rata: ' . ($log->subject->rata_rata ?? '-'),
    'App\Models\file' => ($log->subject->user->nama ?? 'User terhapus') . ' — ' . \Illuminate\Support\Str::limit($log->subject->nama_file, 30),
    'App\Models\Forum' => ($log->subject->user->nama ?? 'User terhapus') . ' — ' . \Illuminate\Support\Str::limit($log->subject->title ?? $log->subject->content_preview, 30),
    'App\Models\ForumMessage' => ($log->subject->user->nama ?? 'User terhapus') . ' — ' . \Illuminate\Support\Str::limit(strip_tags($log->subject->content ?? $log->subject->message ?? ''), 30),
        default => null,
    };
@endphp

        @if($subjectDetail)
            <span class="text-slate-500 dark:text-slate-400 block text-[11px]">
                {{ $subjectDetail }}
            </span>
        @endif
    @endif

    @if($log->subject_id)
        <span class="text-slate-400 dark:text-slate-500 block text-[11px]">ID: {{ $log->subject_id }}</span>
    @endif
</td>

 <!-- Perubahan Attributes -->
<td class="px-6 py-4 text-xs">
    @if(isset($log->attribute_changes['attributes']))
        <div class="space-y-1.5 max-w-md">
            @foreach($log->attribute_changes['attributes'] as $key => $newValue)
                @php
                    $oldValue = $log->attribute_changes['old'][$key] ?? '-';

                    if ($log->subject_type === 'App\Models\DetailJadwal' && $key === 'jadwal_id') {
                        $oldJadwal = is_numeric($oldValue) ? \App\Models\Jadwal::find($oldValue) : null;
                        $newJadwal = is_numeric($newValue) ? \App\Models\Jadwal::find($newValue) : null;

                        $formattedOld = $oldJadwal
                            ? $oldJadwal->status_kerja->value . ' (' . $oldJadwal->jam_masuk . '-' . $oldJadwal->jam_keluar . ')'
                            : '-';
                        $formattedNew = $newJadwal
                            ? $newJadwal->status_kerja->value . ' (' . $newJadwal->jam_masuk . '-' . $newJadwal->jam_keluar . ')'
                            : '-';
                        $displayKey = 'status_kerja';
                    } else {
                        $formattedOld = is_array($oldValue) ? json_encode($oldValue) : $oldValue;
                        $formattedNew = is_array($newValue) ? json_encode($newValue) : $newValue;
                        $displayKey = $key;
                    }
                @endphp
                <div class="bg-slate-50 dark:bg-slate-950/50 p-2 rounded border border-slate-200 dark:border-slate-800 text-[11px]">
                    <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px] block mb-1">
                        {{ $displayKey }}
                    </span>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-rose-600 dark:text-rose-400 line-through bg-rose-50 dark:bg-rose-950/40 px-1.5 py-0.5 rounded border border-rose-100 dark:border-rose-900/40">
                            {{ $formattedOld }}
                        </span>
                        <span class="text-slate-400 dark:text-slate-500 font-bold">&rarr;</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded border border-emerald-100 dark:border-emerald-900/40">
                            {{ $formattedNew }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <span class="text-slate-400 dark:text-slate-500 italic">Tidak ada perubahan atribut yang dicatat</span>
    @endif
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                                Belum ada riwayat aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
    </div>
</div>