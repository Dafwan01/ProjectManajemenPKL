<div class="max-w-4xl mx-auto py-6 px-4">
    <!-- Alert Notifikasi (Message / Success) -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/80 border border-green-400 dark:border-green-500 text-green-800 dark:text-green-200 rounded-xl flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" class="text-green-600 dark:text-green-300 hover:text-gray-900 dark:hover:text-white" @click="$el.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Alert Notifikasi (Warning / Lulus) -->
    @if (session()->has('warning'))
        <div class="mb-6 p-4 bg-yellow-100 dark:bg-yellow-900/80 border border-yellow-400 dark:border-yellow-500 text-yellow-800 dark:text-yellow-200 rounded-xl flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500 dark:text-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" class="text-yellow-600 dark:text-yellow-300 hover:text-gray-900 dark:hover:text-white" @click="$el.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- Card Form Project Akhir -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    Form Project Akhir
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Unggah laporan/berkas project akhir dan link repositori GitHub kamu.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if ($isLulus)
                    <span class="px-3 py-1 bg-amber-50 dark:bg-amber-500/10 border border-amber-400 dark:border-amber-500 text-amber-600 dark:text-amber-400 text-xs font-medium rounded-full">
                        🎓 Status: Lulus
                    </span>
                @elseif ($sudahUpload)
                    <span class="px-3 py-1 bg-green-50 dark:bg-green-500/10 border border-green-400 dark:border-green-500 text-green-600 dark:text-green-400 text-xs font-medium rounded-full">
                        ✓ Sudah Mengirim
                    </span>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="simpanProject" class="space-y-5">
            <!-- Nama Project -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nama Project / Laporan <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    wire:model.defer="nama_project" 
                    placeholder="{{ $isLulus ? 'Formulir terkunci karena Anda telah LULUS.' : 'Contoh: Sistem Informasi Presensi berbasis Laravel' }}" 
                    @disabled($isLulus)
                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:border-purple-500 focus:outline-none text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                @error('nama_project') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Link GitHub -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Link Repository GitHub (Opsional)
                </label>
                <input 
                    type="url" 
                    wire:model.defer="link_github" 
                    placeholder="https://github.com/username/repository" 
                    @disabled($isLulus)
                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:border-purple-500 focus:outline-none text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                @error('link_github') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Pilih Kolaborator -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tambahkan Anggota Kolaborator
                </label>
                <select 
                    wire:model.defer="kolaborator_ids"
                    @disabled($isLulus || !$isProjectOwner)
                    multiple
                    class="w-full min-h-[120px] bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:border-purple-500 focus:outline-none text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    @foreach($availableCollaborators as $collaborator)
                        <option value="{{ $collaborator->user_id }}">{{ $collaborator->nama }} @if($collaborator->mentor) (Mentor: {{ $collaborator->mentor }}) @endif</option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Pilih satu atau lebih peserta PKL untuk menjadi kolaborator dalam project ini.</p>
                @error('kolaborator_ids') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                @error('kolaborator_ids.*') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Upload File Project -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    File Project / Laporan (ZIP, RAR, PDF, DOCX - Max 20MB) 
                    @if (!$existing_file) <span class="text-red-500">*</span> @endif
                </label>
                
                <input 
                    type="file" 
                    wire:model="file_project" 
                    @disabled($isLulus)
                    class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-300 dark:border-gray-700 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed file:disabled:opacity-50 file:disabled:cursor-not-allowed">

                <!-- Loading Indicator -->
                <div wire:loading wire:target="file_project" class="text-xs text-purple-600 dark:text-purple-400 mt-2">
                    Mengunggah berkas... Mohon tunggu.
                </div>

                @error('file_project') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror

                <!-- Informasi File Lama -->
                @if ($existing_file)
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/80 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 text-green-500 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>File Ter-upload: <strong class="text-purple-600 dark:text-purple-300">{{ basename($existing_file) }}</strong></span>
                        </div>
                        <a href="{{ Storage::url($existing_file) }}" target="_blank" class="text-xs text-purple-600 dark:text-purple-400 hover:underline font-semibold">
                            Unduh / Lihat File
                        </a>
                    </div>
                @endif
            </div>

            <!-- Tombol Submit -->
            <button 
                type="submit" 
                wire:loading.attr="disabled" 
                        @disabled($isLulus || !$isProjectOwner)
                        class="w-full py-3 text-white font-semibold rounded-xl transition shadow-lg flex items-center justify-center gap-2 {{ $isLulus || !$isProjectOwner ? 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed opacity-60' : 'bg-purple-600 hover:bg-purple-700' }}">
                @else
                    <span wire:loading.remove wire:target="simpanProject">
                        {{ $sudahUpload ? 'Perbarui Project Akhir' : 'Kirim Project Akhir' }}
                    </span>
                    <span wire:loading wire:target="simpanProject">
                        Memproses...
                    </span>
                @endif
            </button>
        </form>
    </div>
</div>