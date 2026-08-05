<div class="p-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <span class="text-slate-400 text-sm font-semibold tracking-wide">Recent Activity</span>
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
            + Buat Forum Baru
        </button>
    </div>

    <!-- List Forum -->
    <div class="space-y-3">
        @forelse($forums as $forum)
            <a href="{{ route('user.forum.show',$forum->forum_id) }}" class="block bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 hover:border-slate-600 rounded-xl p-4 transition duration-150 group">
                <h3 class="text-base font-bold text-slate-100 group-hover:text-blue-400 transition">
                    {{ $forum->title }}
                </h3>
                <div class="text-sm text-slate-400 mt-1 truncate">
                    <span class="text-cyan-400 font-medium">{{ $forum->user->nama }}</span>
                    <span class="text-slate-500 mx-1">:</span>
                    <span class="text-slate-300">{{ \Illuminate\Support\Str::limit($forum->content, 80) }}</span>
                </div>
                <div class="flex items-center gap-3 mt-3 text-xs text-slate-400">
                    <span class="font-medium text-slate-300">{{ $forum->messages_count }} Messages</span>
                    <span>•</span>
                    <span>{{ $forum->created_at->format('M d, Y') }}</span>
                </div>
            </a>
        @empty
            <div class="text-center py-12 text-slate-500">Belum ada diskusi forum terbaru.</div>
        @endforelse
    </div>

    <!-- Modal Buat Forum -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click.self="closeModal">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6">
                <h2 class="text-lg font-bold text-white mb-4">Buat Topik Forum Baru</h2>

                <form wire:submit.prevent="save" class="space-y-4">

                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Judul</label>
                        <input type="text" wire:model="title" class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg p-2.5">
                        @error('title') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Pesan Utama (Content)</label>
                        <textarea wire:model="content" rows="4" class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg p-2.5"></textarea>
                        @error('content') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-semibold disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">Publish</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>