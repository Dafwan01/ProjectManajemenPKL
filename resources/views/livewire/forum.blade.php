<div class="p-6 max-w-5xl mx-auto space-y-6">
    <!-- Header Utama -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xs font-bold tracking-wider text-gray-500 dark:text-gray-400 uppercase">Aktivitas Terkini</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Ruang diskusi dan berbagi informasi antarpeserta PKL.</p>
        </div>
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Buat Forum Baru</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="relative">
        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" wire:model.live.debounce.400ms="search"
               placeholder="Cari judul, isi, atau nama penulis forum..."
               class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white rounded-xl pl-10 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">

        <!-- Indikator loading saat searching -->
        <div wire:loading wire:target="search" class="absolute right-3.5 top-1/2 -translate-y-1/2">
            <svg class="w-4 h-4 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        </div>

        <!-- Tombol clear search -->
        @if($search)
            <button type="button" wire:click="$set('search', '')" wire:loading.remove wire:target="search"
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>

   <!-- List Forum -->
    <div class="space-y-3">
        @php
            $user = auth()->user();
            $authId = auth()->id();
            $roleValue = $user?->role instanceof \UnitEnum ? $user->role->value : (string) $user?->role;
        @endphp

        @forelse($forums as $forum)
            @php
                $isOwner = (string) $forum->user_id === (string) $authId;
                $isAdmin = ($roleValue === \App\Enums\UserRole::ADMIN->value);
                $canModify = $isOwner || $isAdmin;
            @endphp

            <div class="relative group">
                <a href="{{ route('forum.show', $forum->forum_id) }}" class="block bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 rounded-2xl p-5 shadow-sm transition duration-150">
                    <div class="flex gap-4 items-start">
                        @if($forum->gambar)
                            <img src="{{ asset('storage/' . $forum->gambar) }}" alt="Gambar Forum" class="w-20 h-20 object-cover rounded-xl border border-gray-200 dark:border-gray-800 shrink-0">
                        @endif

                        <div class="flex-1 min-w-0 pr-16">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                {{ $forum->title }}
                            </h3>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate">
                                <span class="text-cyan-600 dark:text-cyan-400 font-semibold">{{ $forum->user->nama ?? 'Pengguna' }}</span>
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

                <!-- Tombol Aksi Edit & Hapus (Hanya untuk Pembuat / Admin) -->
                @if($canModify)
                    <div class="absolute top-4 right-4 flex items-center gap-1 z-10">
                        <!-- Edit -->
                        <button 
                            type="button"
                            wire:click.stop.prevent="edit({{ $forum->forum_id }})"
                            class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-xl transition"
                            title="Edit Forum"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <!-- Hapus -->
                        <button 
                            type="button"
                            wire:click.stop.prevent="delete({{ $forum->forum_id }})"
                            wire:confirm="Apakah Anda yakin ingin menghapus topik forum ini?"
                            class="p-2 text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition"
                            title="Hapus Forum"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-900 border border-dashed border-gray-200 dark:border-gray-800 rounded-3xl text-gray-400 dark:text-gray-500 text-xs">
                Belum ada topik diskusi di forum.
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    @if($forums->hasPages())
        <div class="pt-2">
            {{ $forums->links() }}
        </div>
    @endif

    <!-- Modal Buat / Sunting Forum -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click.self="closeModal">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    {{ $editingId ? 'Sunting Topik Forum' : 'Buat Topik Forum Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-4">

                    <!-- Judul Forum -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul Forum <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="title" placeholder="Masukkan judul diskusi..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Isi Pesan Utama -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pesan Utama / Konten <span class="text-red-500">*</span></label>
                        <textarea wire:model="content" rows="4" placeholder="Tuliskan isi topik diskusi Anda di sini..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"></textarea>
                        @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Gambar Lampiran -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Unggah Gambar (Opsional)</label>
                        <input type="file" wire:model="image" accept="image/*" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 dark:file:bg-gray-700 dark:file:text-white hover:file:bg-gray-300 dark:hover:file:bg-gray-600 transition">
                        
                        <div wire:loading wire:target="image" class="text-xs text-cyan-600 dark:text-cyan-400 mt-1">Mengunggah gambar... Mohon tunggu.</div>
                        @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                        <!-- Gambar Saat Ini (Saat Mode Edit) -->
                        @if ($existingGambar && !$image)
                            <div class="mt-2 relative w-32 h-32 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 group">
                                <img src="{{ asset('storage/' . $existingGambar) }}" class="w-full h-full object-cover">
                                <button type="button" wire:click="removeExistingGambar" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-500 transition" title="Hapus Gambar Saat Ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endif

                        <!-- Pratinjau Gambar Baru Sebelum Disimpan -->
                        @if ($image)
                            <div class="mt-2 relative w-32 h-32 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-gray-900 dark:hover:text-white transition">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="save, image" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-semibold disabled:opacity-60 transition shadow-sm">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Publikasikan' }}</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>
