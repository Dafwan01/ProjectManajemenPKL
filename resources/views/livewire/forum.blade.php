<div class="p-6 max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xs font-bold tracking-wider text-gray-500 dark:text-gray-400 uppercase">Aktivitas Terkini</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Ruang diskusi dan berbagi informasi peserta PKL.</p>
        </div>
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Buat Forum Baru</span>
        </button>
    </div>

   <!-- List Forum -->
    <div class="space-y-3">
        @forelse($forums as $forum)
            <a href="{{ route('forum.show', $forum->forum_id) }}" class="block bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 rounded-2xl p-5 shadow-sm transition duration-150 group">
                <div class="flex gap-4 items-start">
                    
                    @if($forum->image)
                        <img src="{{ asset('storage/' . $forum->image) }}" alt="Forum Image" class="w-20 h-20 object-cover rounded-xl border border-gray-200 dark:border-gray-800 shrink-0">
                    @endif

                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                            {{ $forum->title }}
                        </h3>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate">
                            <span class="text-cyan-600 dark:text-cyan-400 font-semibold">{{ $forum->user->nama }}</span>
                            <span class="text-gray-300 dark:text-gray-700 mx-1">:</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($forum->content, 80) }}</span>
                        </div>
                        <div class="flex items-center gap-3 mt-3 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $forum->messages_count }} Pesan</span>
                            <span class="text-gray-300 dark:text-gray-700">•</span>
                            <span>{{ $forum->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-900 border border-dashed border-gray-200 dark:border-gray-800 rounded-3xl text-gray-400 dark:text-gray-500 text-xs">
                Belum ada diskusi forum terbaru.
            </div>
        @endforelse
    </div>

    <!-- Modal Buat Forum -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click.self="closeModal">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Buat Topik Forum Baru</h2>

                <form wire:submit.prevent="save" class="space-y-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul</label>
                        <input type="text" wire:model="title" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pesan Utama (Content)</label>
                        <textarea wire:model="content" rows="4" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"></textarea>
                        @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Gambar -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Unggah Gambar (Opsional)</label>
                        <input type="file" wire:model="image" accept="image/*" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 dark:file:bg-gray-700 dark:file:text-white hover:file:bg-gray-300 dark:hover:file:bg-gray-600 transition">
                        
                        <!-- Indikator Loading Upload Gambar -->
                        <div wire:loading wire:target="image" class="text-xs text-cyan-600 dark:text-cyan-400 mt-1">Mengunggah gambar...</div>

                        @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                        <!-- Preview Gambar Sebelum Simpan -->
                        @if ($image)
                            <div class="mt-2 relative w-32 h-32 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-gray-900 dark:hover:text-white transition">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="save, image" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-semibold disabled:opacity-60 transition shadow-sm">
                            <span wire:loading.remove wire:target="save">Publish</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, content }) => {
                console.log('=== LIVEWIRE REQUEST FAILED ===');
                console.log('Status:', status);
                console.log('Content:', content);
            });
        });
    });
    </script>
</div>