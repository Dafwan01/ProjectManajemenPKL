<div>
    <!-- Konten utama, dikasih padding bawah biar tidak ketutupan bar fixed -->
    <div class="max-w-5xl mx-auto text-slate-100 space-y-6 pb-24">

        <!-- Tombol Kembali -->
        <a href="{{ route('forum') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Forum</span>
        </a>

        <!-- Postingan Utama (Original Post) -->
        <div class="bg-gradient-to-br from-slate-800/90 to-slate-800/60 border border-slate-700/60 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 border border-cyan-500/30 flex items-center justify-center font-bold text-cyan-400 shrink-0">
                    {{ strtoupper(substr($forum->user->nama, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-white truncate">
                        {{ $forum->title }}
                    </h1>
                    <p class="text-xs text-slate-400">
                        Dibuat oleh <span class="text-cyan-400 font-medium">{{ $forum->user->nama }}</span>
                        <span class="text-slate-600">•</span>
                        {{ $forum->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            <!-- Isi Konten Utama Forum -->
            <div class="text-sm text-slate-300 leading-relaxed pt-4 border-t border-slate-700/50">
                {!! nl2br(e($forum->content)) !!}
            </div>
        </div>

        <!-- Section Pesan / Balasan -->
        <div class="space-y-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Pesan ({{ $forum->messages->count() }})</span>
            </div>

            <!-- Daftar Messages -->
            <div class="space-y-3">
                @forelse($forum->messages as $msg)
                    <div class="bg-slate-800/40 border border-slate-700/40 rounded-xl p-4 flex gap-3 hover:border-slate-600/60 transition-colors">
                        <!-- Avatar User -->
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-700 to-slate-700/50 flex-shrink-0 flex items-center justify-center font-bold text-slate-300 text-xs">
                            {{ strtoupper(substr($msg->user->nama, 0, 1)) }}
                        </div>

                        <!-- Isi Pesan -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1 gap-2">
                                <span class="text-xs font-bold text-cyan-400 truncate">{{ $msg->user->nama }}</span>
                                <span class="text-[10px] text-slate-500 shrink-0">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-300 leading-normal break-words">
                                {{ $msg->content }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 bg-slate-900/40 border border-dashed border-slate-800 rounded-xl text-slate-500 text-xs">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Belum ada pesan. Jadi yang pertama mengirim pesan di forum ini!
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Form Input Kirim Pesan — fixed ke viewport, offset kiri disamakan
         dengan lebar sidebar desktop (w-72 = 18rem) di layouts.user.
         Di mobile (di bawah breakpoint sm) sidebar tidak tampil sebagai
         kolom permanen, jadi offset kiri 0. -->
    <div class="fixed bottom-0 left-0 sm:left-72 right-0 z-40 bg-slate-900/95 backdrop-blur-md border-t border-slate-700/80 shadow-[0_-4px_20px_rgba(0,0,0,0.25)]">
        <div class="max-w-5xl mx-auto px-4 py-3">
            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                <input
                    type="text"
                    wire:model="message"
                    placeholder="Kirim pesan ke {{ $forum->title }}..."
                    autocomplete="off"
                    class="w-full bg-slate-800 border border-slate-700 focus:border-blue-500 text-sm text-white placeholder-slate-500 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition"
                >
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                    class="bg-blue-600 hover:bg-blue-500 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold text-xs px-5 py-2.5 rounded-xl transition flex items-center gap-1.5 shrink-0"
                >
                    <span wire:loading.remove wire:target="sendMessage" class="flex items-center gap-1.5">
                        Kirim
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="sendMessage">Mengirim...</span>
                </button>
            </form>
            @error('message')
                <span class="text-red-400 text-xs mt-1.5 block px-1">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>