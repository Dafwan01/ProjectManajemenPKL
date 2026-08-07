<div class="p-6">
    <!-- Header & Filter -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Log Aktivitas Sistem</h1>
            <p class="text-sm text-gray-500">Mencatat riwayat perubahan data, waktu, dan pengubahnya.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Filter Jenis Aksi -->
            <select 
                wire:model.live="eventFilter"
                class="w-full sm:w-40 px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white"
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
                    class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
            </div>
        </div>
    </div>

    <!-- Tabel Activity Log -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-xs text-gray-700 uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Diubah Oleh</th>
                        <th class="px-6 py-3">Aksi</th>
                        <th class="px-6 py-3">Target Data</th>
                        <th class="px-6 py-3">Rincian Perubahan (Sebelum &rarr; Sesudah)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50">
                            <!-- Waktu -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <span class="font-medium text-gray-700 block">{{ $log->created_at->format('d M Y, H:i:s') }}</span>
                                <span class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            <!-- Diubah Oleh Siapa -->
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $log->causer?->nama ?? 'Sistem / Guest' }}
                            </td>

                            <!-- Badge Event/Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($log->event) {
                                        'created' => 'bg-green-100 text-green-700 border-green-200',
                                        'updated' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'deleted' => 'bg-red-100 text-red-700 border-red-200',
                                        default   => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                    {{ ucfirst($log->event ?? $log->description) }}
                                </span>
                            </td>

                            <!-- Target Data (Subject) -->
                            <td class="px-6 py-4 text-xs whitespace-nowrap">
                                <span class="font-semibold text-gray-800">
                                    {{ class_basename($log->subject_type ?? 'N/A') }}
                                </span>
                                @if($log->subject_id)
                                    <span class="text-gray-400 block text-[11px]">ID: {{ $log->subject_id }}</span>
                                @endif
                            </td>

                            <!-- Perubahan Attributes -->
                            <td class="px-6 py-4 text-xs">
                                @if(isset($log->changes()['attributes']))
                                    <div class="space-y-1.5 max-w-md">
                                        @foreach($log->changes()['attributes'] as $key => $newValue)
                                            @php
                                                $oldValue = $log->changes()['old'][$key] ?? '-';
                                                
                                                // Formatting array/object jika kolom berbentuk JSON
                                                $formattedOld = is_array($oldValue) ? json_encode($oldValue) : $oldValue;
                                                $formattedNew = is_array($newValue) ? json_encode($newValue) : $newValue;
                                            @endphp
                                            <div class="bg-gray-50 p-2 rounded border border-gray-200 text-[11px]">
                                                <span class="font-bold text-gray-700 uppercase tracking-wider text-[10px] block mb-1">
                                                    {{ $key }}
                                                </span>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-red-600 line-through bg-red-50 px-1.5 py-0.5 rounded border border-red-100">
                                                        {{ $formattedOld }}
                                                    </span>
                                                    <span class="text-gray-400 font-bold">&rarr;</span>
                                                    <span class="text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded border border-green-100">
                                                        {{ $formattedNew }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Tidak ada perubahan atribut yang dicatat</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                Belum ada riwayat aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $logs->links() }}
        </div>
    </div>
</div>