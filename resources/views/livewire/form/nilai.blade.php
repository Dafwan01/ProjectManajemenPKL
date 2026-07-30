<div class="w-full">
    <div class="mb-6 border-b pb-4 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Input Nilai Magang</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->nama ?? '-' }}</p>
    </div>

    <form wire:submit.prevent="simpan" class="space-y-5">

        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
                1. Kedisiplinan dan Profesionalisme (Integritas Work Ethic)
            </label>
            <input 
                type="number" min="0" max="100"
                wire:model="kedisiplinan"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('kedisiplinan') border-red-500 @enderror"
            >
            @error('kedisiplinan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
                2. Kemampuan Teknis dan Implementasi Tugas (Hard Skills)
            </label>
            <input 
                type="number" min="0" max="100"
                wire:model="kemampuan_teknis"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('kemampuan_teknis') border-red-500 @enderror"
            >
            @error('kemampuan_teknis') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
                3. Kemampuan Logika Pemecahan Masalah (Problem Solving)
            </label>
            <input 
                type="number" min="0" max="100"
                wire:model="problem_solving"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('problem_solving') border-red-500 @enderror"
            >
            @error('problem_solving') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
                4. Komunikasi dan Kerja Sama Tim (Soft Skills)
            </label>
            <input 
                type="number" min="0" max="100"
                wire:model="komunikasi_kerjasama"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('komunikasi_kerjasama') border-red-500 @enderror"
            >
            @error('komunikasi_kerjasama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
                5. Kualitas dan Ketepatan Waktu Output Kerja (Deliverables)
            </label>
            <input 
                type="number" min="0" max="100"
                wire:model="kualitas_ketepatan"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('kualitas_ketepatan') border-red-500 @enderror"
            >
            @error('kualitas_ketepatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
                Catatan Tambahan (Opsional)
            </label>
            <textarea 
                wire:model="catatan" rows="3"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                placeholder="Catatan evaluasi tambahan..."
            ></textarea>
        </div>

        <!-- Upload File Pendukung -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
                Upload File Pendukung (Opsional)
            </label>

            @if($fileNilaiLama)
                <div class="mb-2 p-2.5 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-300">File sudah ada.</span>
                    <a href="{{ asset('storage/' . $fileNilaiLama->file) }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                        Lihat File
                    </a>
                </div>
            @endif

            <input 
                type="file" 
                wire:model="file"
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 @error('file') border-red-500 @enderror"
            >
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: PDF, JPG, JPEG, PNG. Maks 5MB.</p>

            @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

            <div wire:loading wire:target="file" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Mengunggah pratinjau...
            </div>

            @if ($file)
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    File dipilih: {{ $file->getClientOriginalName() }}
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between gap-3 pt-4 border-t dark:border-gray-700">
            <!-- Tombol Cetak PDF (Sisi Kiri) -->
            <div>
                @if($sudahAdaNilai)
                    <a 
                        href="{{ route('cetak.nilai', ['userId' => $userId]) }}" 
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 transition"
                    >
                        <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7.414A2 2 0 0 0 16.414 6L13 2.586A2 2 0 0 0 11.586 2H5Zm0 2h6v3a1 1 0 0 0 1 1h3v8H5V4Z"/>
                        </svg>
                        Cetak PDF
                    </a>
                @endif
            </div>

            <!-- Tombol Batal & Simpan (Sisi Kanan) -->
            <div class="flex items-center gap-3">
                <button 
                    type="button"
                    wire:click="tutup"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700"
                >
                    Simpan
                </button>
            </div>
        </div>

    </form>
</div>