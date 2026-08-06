<div x-data="{ activeImage: null }">

    <!-- KONTEN UTAMA -->
    <div class="max-w-5xl mx-auto space-y-6 pb-28">

        <!-- Tombol Kembali -->
        <a href="{{ route('forum') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Forum</span>
        </a>

        <!-- Postingan Utama -->
        <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/20 flex items-center justify-center font-bold text-cyan-600 dark:text-cyan-400 shrink-0">
                    {{ strtoupper(substr($forum->user->nama ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                        {{ $forum->title }}
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Dibuat oleh <span class="text-cyan-600 dark:text-cyan-400 font-semibold">{{ $forum->user->nama ?? 'User' }}</span>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        {{ $forum->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            <!-- Isi Konten Utama Forum -->
            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed pt-4 border-t border-gray-100 dark:border-gray-800 space-y-3">
                <p>{!! nl2br(e($forum->content)) !!}</p>

                <!-- Gambar Utama Forum -->
                @if($forum->gambar)
                    <div class="pt-2">
                        <img 
                            src="{{ asset('storage/' . $forum->gambar) }}" 
                            alt="Gambar Forum" 
                            @click="activeImage = '{{ asset('storage/' . $forum->gambar) }}'"
                            class="max-h-96 rounded-2xl border border-gray-200 dark:border-gray-800 object-cover cursor-pointer hover:opacity-90 hover:scale-[1.005] transition duration-200 shadow-sm"
                        >
                    </div>
                @endif
            </div>
        </div>

        <!-- Section Pesan / Balasan -->
        <div class="space-y-4">
            <div class="flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Pesan ({{ $forum->messages->count() }})</span>
            </div>

            <!-- Daftar Messages -->
            <div class="space-y-3">
                @forelse($forum->messages as $msg)
                    <div class="p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl flex gap-3 hover:border-gray-300 dark:hover:border-gray-700 transition-colors shadow-sm">
                        <div class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex-shrink-0 flex items-center justify-center font-bold text-gray-600 dark:text-gray-300 text-xs">
                            {{ strtoupper(substr($msg->user->nama ?? 'U', 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400 truncate">{{ $msg->user->nama ?? 'User' }}</span>
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 shrink-0">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>

                            <!-- Gambar Lampiran Pesan -->
                            @if($msg->gambar)
                                <div class="pt-1">
                                    <img 
                                        src="{{ asset('storage/' . $msg->gambar) }}" 
                                        alt="Lampiran Pesan" 
                                        @click="activeImage = '{{ asset('storage/' . $msg->gambar) }}'"
                                        class="max-h-60 rounded-xl border border-gray-200 dark:border-gray-800 object-cover cursor-pointer hover:opacity-90 hover:scale-[1.005] transition duration-200 shadow-sm"
                                    >
                                </div>
                            @endif

                            @if($msg->content)
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-normal break-words">
                                    {{ $msg->content }}
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 bg-white dark:bg-gray-900 border border-dashed border-gray-200 dark:border-gray-800 rounded-3xl text-gray-400 dark:text-gray-500 text-xs">
                        Belum ada pesan. Jadi yang pertama mengirim pesan di forum ini!
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- FORM INPUT KIRIM PESAN (STICKY BOTTOM) -->
    <div class="fixed bottom-0 left-0 sm:left-72 right-0 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] dark:shadow-[0_-4px_20px_rgba(0,0,0,0.3)]">
        <div class="max-w-5xl mx-auto px-4 py-3">

            @if ($gambar)
                <div class="mb-3 relative inline-block">
                    <img src="{{ $gambar->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-xl border border-gray-300 dark:border-gray-700 shadow-md">
                    <button type="button" wire:click="$set('gambar', null)" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center transition shadow">
                        ✕
                    </button>
                </div>
            @endif

            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                <label class="cursor-pointer bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-2.5 rounded-xl transition shrink-0 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input type="file" wire:model="gambar" accept="image/*" class="hidden">
                </label>

                <input
                    type="text"
                    wire:model="message"
                    placeholder="Kirim pesan ke {{ $forum->title }}..."
                    autocomplete="off"
                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:border-blue-500 dark:focus:border-blue-500 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                >

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage, gambar"
                    class="bg-blue-600 hover:bg-blue-500 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold text-xs px-5 py-2.5 rounded-xl transition flex items-center gap-1.5 shrink-0 shadow-sm"
                >
                    <span wire:loading.remove wire:target="sendMessage, gambar" class="flex items-center gap-1.5">
                        Kirim
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="sendMessage">Mengirim...</span>
                    <span wire:loading wire:target="gambar">Uploading...</span>
                </button>
            </form>

            @error('message') <span class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
            @error('gambar') <span class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- MODAL LIGHTBOX PREVIEW GAMBAR -->
    <template x-if="activeImage">
        <div 
            x-transition.opacity.duration.200ms
            @click="activeImage = null"
            @keydown.escape.window="activeImage = null"
            class="fixed inset-0 z-50 bg-black/70 dark:bg-black/80 backdrop-blur-md flex items-center justify-center p-4 cursor-zoom-out"
        >
            <div class="relative max-w-5xl max-h-[90vh] flex flex-col items-center justify-center" @click.stop>
                <img 
                    :src="activeImage" 
                    class="max-w-full max-h-[85vh] rounded-2xl object-contain border border-gray-200 dark:border-gray-800 shadow-2xl"
                >
                <button 
                    @click="activeImage = null" 
                    class="absolute -top-3 -right-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-white rounded-full p-2 border border-gray-200 dark:border-gray-700 shadow-xl transition hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>

</div>