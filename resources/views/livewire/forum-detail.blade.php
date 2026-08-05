<div class="p-6 max-w-5xl mx-auto text-slate-100 space-y-6">

    <!-- Tombol Kembali -->
    <a href="{{ route('user.forum') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Kembali ke Daftar Forum</span>
    </a>

    <!-- Postingan Utama (Original Post) -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 shadow-xl space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center font-bold text-cyan-400">
                {{ strtoupper(substr($forum->user->nama, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold flex items-center gap-2 text-white">
                    <span>{{ $forum->title }}</span>
                </h1>
                <p class="text-xs text-slate-400">
                    Dibuat oleh <span class="text-cyan-400 font-medium">{{ $forum->user->nama }}</span> 
                    • {{ $forum->created_at->format('d M Y, H:i') }}
                </p>
            </div>
        </div>

        <!-- Isi Konten Utama Forum -->
        <div class="text-sm text-slate-300 leading-relaxed pt-2 border-t border-slate-700/50">
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
                <div class="bg-slate-800/40 border border-slate-700/40 rounded-xl p-4 flex gap-3">
                    <!-- Avatar User -->
                    <div class="w-8 h-8 rounded-full bg-slate-700 flex-shrink-0 flex items-center justify-center font-bold text-slate-300 text-xs">
                        {{ strtoupper(substr($msg->user->nama, 0, 1)) }}
                    </div>

                    <!-- Isi Pesan -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-cyan-400">{{ $msg->user->nama}}</span>
                            <span class="text-[10px] text-slate-500">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-300 leading-normal break-words">
                            {{ $msg->content }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 bg-slate-900/40 border border-dashed border-slate-800 rounded-xl text-slate-500 text-xs">
                    Belum ada pesan. Jadi yang pertama mengirim pesan di forum ini!
                </div>
            @endforelse
        </div>
    </div>

    <!-- Form Input Kirim Pesan -->
    <div class="sticky bottom-4 bg-slate-900/90 backdrop-blur-md border border-slate-700/80 rounded-xl p-3 shadow-2xl">
        <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
            <input 
                type="text" 
                wire:model="message" 
                placeholder="Kirim pesan ke {{ $forum->title }}..." 
                class="w-full bg-slate-800 border border-slate-700 focus:border-blue-500 text-sm text-white placeholder-slate-500 rounded-lg px-4 py-2.5 focus:outline-none transition"
            >
            <button 
                type="submit" 
                class="bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs px-5 py-2.5 rounded-lg transition flex items-center gap-1.5 shrink-0"
            >
                <span>Kirim</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
        @error('message') 
            <span class="text-red-400 text-xs mt-1 block px-1">{{ $message }}</span> 
        @enderror
    </div>

</div>