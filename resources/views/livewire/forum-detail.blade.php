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

        <!-- Pesan Flash Notifikasi -->
        @if (session()->has('message'))
            <div class="p-4 text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-2xl">
                {{ session('message') }}
            </div>
        @endif

        <!-- Postingan Utama -->
        <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl space-y-4">
            @php
                $authUser = auth()->user();
                $authId = auth()->id();
                
                $roleValue = $authUser?->role instanceof \UnitEnum 
                    ? $authUser->role->value 
                    : (string) $authUser?->role;

                // Pembuat forum ATAU Admin/Mentor/Non-PKL
                $isOwner = (string) $forum->user_id === (string) $authId;
                $isAdmin = ($roleValue === \App\Enums\UserRole::ADMIN->value);
                
                $canModify = $isOwner || $isAdmin;
            @endphp

            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/20 flex items-center justify-center font-bold text-cyan-600 dark:text-cyan-400 shrink-0">
                        {{ strtoupper(substr($forum->user->nama ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                            {{ $forum->title }}
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Dibuat oleh <span class="text-cyan-600 dark:text-cyan-400 font-semibold">{{ $forum->user->nama ?? 'Pengguna' }}</span>
                            <span class="text-gray-300 dark:text-gray-700">•</span>
                            {{ $forum->created_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                <!-- Tombol Edit & Hapus (Hanya muncul untuk Pembuat atau Admin) -->
                @if($canModify)
                    <div class="flex items-center gap-1 shrink-0">
                        <button 
                            type="button"
                            wire:click="openEditModal"
                            class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-xl transition"
                            title="Sunting Forum"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button 
                            type="button"
                            wire:click="confirmForumDelete"
                            class="p-2 text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition"
                            title="Hapus Forum"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Isi Konten Utama Forum -->
            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed pt-4 border-t border-gray-100 dark:border-gray-800 space-y-3">
                <p>{!! nl2br(e($forum->content)) !!}</p>

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

        <!-- Bagian Pesan / Balasan -->
        <div class="space-y-4">
            <div class="flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Pesan ({{ $forum->messages->count() }})</span>
            </div>

            <!-- Daftar Pesan Balasan -->
            <div class="space-y-3">
                @forelse($forum->messages as $msg)
                    <div class="p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl flex gap-3 hover:border-gray-300 dark:hover:border-gray-700 transition-colors shadow-sm">
                        <div class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex-shrink-0 flex items-center justify-center font-bold text-gray-600 dark:text-gray-300 text-xs">
                            {{ strtoupper(substr($msg->user->nama ?? 'U', 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400 truncate">{{ $msg->user->nama ?? 'Pengguna' }}</span>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $msg->created_at->diffForHumans() }}</span>

                                    {{-- Tombol Hapus Pesan: pengupload pesan itu sendiri ATAU admin --}}
                                    @if((string) $msg->user_id === (string) $authId || $isAdmin)
                                        <button
                                            type="button"
                                            wire:click="confirmMessageDelete({{ $msg->message_id }})"
                                            class="p-1 text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition"
                                            title="Hapus / Tarik Kembali Pesan"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>

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
                        Belum ada pesan. Jadilah yang pertama mengirim pesan di forum ini!
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- MODAL EDIT FORUM DETAIL -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click.self="closeEditModal">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Sunting Topik Forum</h2>

                <form wire:submit.prevent="updateForum" class="space-y-4">

                    <!-- Judul Forum -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul Forum <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="editTitle" placeholder="Masukkan judul diskusi..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        @error('editTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Isi Pesan Utama -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pesan Utama / Konten <span class="text-red-500">*</span></label>
                        <textarea wire:model="editContent" rows="4" placeholder="Tuliskan isi topik diskusi Anda di sini..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"></textarea>
                        @error('editContent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Gambar Lampiran -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Ganti / Unggah Gambar (Opsional)</label>
                        <input type="file" wire:model="editImage" accept="image/*" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 dark:file:bg-gray-700 dark:file:text-white hover:file:bg-gray-300 dark:hover:file:bg-gray-600 transition">
                        
                        <div wire:loading wire:target="editImage" class="text-xs text-cyan-600 dark:text-cyan-400 mt-1">Mengunggah gambar... Mohon tunggu.</div>
                        @error('editImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                        @if ($existingImage && !$editImage)
                            <div class="mt-2 relative w-32 h-32 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                <img src="{{ asset('storage/' . $existingImage) }}" class="w-full h-full object-cover">
                                <button type="button" wire:click="removeExistingImage" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-500 transition" title="Hapus Gambar saat ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endif

                        @if ($editImage)
                            <div class="mt-2 relative w-32 h-32 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                <img src="{{ $editImage->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeEditModal" class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-gray-900 dark:hover:text-white transition">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="updateForum, editImage" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-semibold disabled:opacity-60 transition shadow-sm">
                            <span wire:loading.remove wire:target="updateForum">Simpan Perubahan</span>
                            <span wire:loading wire:target="updateForum">Menyimpan...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS FORUM -->
    @if($confirmingForumDelete)
        <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click.self="cancelForumDelete">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl w-full max-w-sm p-6 shadow-2xl text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1.5">Hapus Topik Forum?</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">
                    Tindakan ini tidak dapat dibatalkan. Seluruh pesan di dalam topik ini juga akan ikut terhapus.
                </p>
                <div class="flex justify-center gap-2">
                    <button type="button" wire:click="cancelForumDelete" class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm font-semibold hover:text-gray-900 dark:hover:text-white transition">
                        Batal
                    </button>
                    <button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete" class="bg-rose-600 hover:bg-rose-500 text-white px-4 py-2 rounded-xl text-sm font-semibold disabled:opacity-60 transition shadow-sm">
                        <span wire:loading.remove wire:target="delete">Ya, Hapus</span>
                        <span wire:loading wire:target="delete">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS PESAN -->
    @if($confirmingMessageDeleteId)
        <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click.self="cancelMessageDelete">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl w-full max-w-sm p-6 shadow-2xl text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1.5">Tarik Kembali Pesan?</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">
                    Pesan yang sudah dihapus tidak dapat dikembalikan.
                </p>
                <div class="flex justify-center gap-2">
                    <button type="button" wire:click="cancelMessageDelete" class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm font-semibold hover:text-gray-900 dark:hover:text-white transition">
                        Batal
                    </button>
                    <button type="button" wire:click="deleteMessage({{ $confirmingMessageDeleteId }})" wire:loading.attr="disabled" wire:target="deleteMessage" class="bg-rose-600 hover:bg-rose-500 text-white px-4 py-2 rounded-xl text-sm font-semibold disabled:opacity-60 transition shadow-sm">
                        <span wire:loading.remove wire:target="deleteMessage">Ya, Hapus</span>
                        <span wire:loading wire:target="deleteMessage">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- FORM INPUT KIRIM PESAN (STICKY BOTTOM) -->
    <div class="fixed bottom-0 left-0 sm:left-72 right-0 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] dark:shadow-[0_-4px_20px_rgba(0,0,0,0.3)]">
        <div class="max-w-5xl mx-auto px-4 py-3">

            @if ($gambar)
                <div class="mb-3 relative inline-block">
                    <img src="{{ $gambar->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-xl border border-gray-300 dark:border-gray-700 shadow-md">
                    <button type="button" wire:click="$set('gambar', null)" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center transition shadow" title="Hapus Gambar">
                        ✕
                    </button>
                </div>
            @endif

            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                <label class="cursor-pointer bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-2.5 rounded-xl transition shrink-0 flex items-center justify-center" title="Lampirkan Gambar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input type="file" wire:model="gambar" accept="image/*" class="hidden">
                </label>

                <input
                    type="text"
                    wire:model="message"
                    placeholder="Tulis pesan untuk {{ $forum->title }}..."
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
                    <span wire:loading wire:target="gambar">Mengunggah...</span>
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
                    title="Tutup Pratinjau"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>

</div>
