<div class="w-full">
    <div class="mb-6 border-b pb-4 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Upload Surat Penerimaan Magang</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->nama ?? '-' }}</p>
    </div>

    <form wire:submit.prevent="simpan" class="space-y-5">

        @if($user && $user->surat_penerimaan_magang)
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-between">
                <span class="text-sm text-gray-700 dark:text-gray-300">File saat ini sudah ada.</span>
                <a href="{{ asset('storage/' . $user->surat_penerimaan_magang) }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Lihat File
                </a>
            </div>
        @endif

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Pilih File Baru
            </label>
            <input 
                type="file" 
                wire:model="file"
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 @error('file') border-red-500 @enderror"
            >
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: PDF, JPG, JPEG, PNG. Maks 5MB.</p>
            @error('file')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror

            <div wire:loading wire:target="file" class="text-xs text-gray-500 mt-1">Mengunggah pratinjau...</div>

            @if ($file)
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    File dipilih: {{ $file->getClientOriginalName() }}
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t dark:border-gray-700">
            <button 
                type="button"
                wire:click="tutup"
                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500"
            >
                Batal
            </button>
            <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:target="simpan"
                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="simpan">Simpan</span>
                <span wire:loading wire:target="simpan">Menyimpan...</span>
            </button>
        </div>

    </form>
</div>