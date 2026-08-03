<div class="w-full">
    <!-- Header Modal -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Input Nilai Magang</h2>
        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400 mt-1">
            {{ $user->nama ?? '-' }}
        </p>
    </div>

    <!-- Form Input Nilai -->
    <form wire:submit.prevent="simpan" class="space-y-4">

        <!-- Kriteria 1 -->
        <!-- Kriteria 1 -->
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label for="kedisiplinan" class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            1. Kedisiplinan & Profesionalisme (Integritas Work Ethic)
        </label>
        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full flex-shrink-0 ml-2">0-100</span>
    </div>
    <input 
        type="number" min="0" max="100" id="kedisiplinan"
        wire:model="kedisiplinan"
        placeholder="0 - 100"
        class="bg-gray-50 dark:bg-gray-800/60 border @error('kedisiplinan') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
    >
    @error('kedisiplinan') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

<!-- Kriteria 2 -->
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label for="kemampuan_teknis" class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            2. Kemampuan Teknis & Implementasi Tugas (Hard Skills)
        </label>
        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full flex-shrink-0 ml-2">0-100</span>
    </div>
    <input 
        type="number" min="0" max="100" id="kemampuan_teknis"
        wire:model="kemampuan_teknis"
        placeholder="0 - 100"
        class="bg-gray-50 dark:bg-gray-800/60 border @error('kemampuan_teknis') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
    >
    @error('kemampuan_teknis') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

<!-- Kriteria 3 -->
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label for="problem_solving" class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            3. Kemampuan Logika Pemecahan Masalah (Problem Solving)
        </label>
        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full flex-shrink-0 ml-2">0-100</span>
    </div>
    <input 
        type="number" min="0" max="100" id="problem_solving"
        wire:model="problem_solving"
        placeholder="0 - 100"
        class="bg-gray-50 dark:bg-gray-800/60 border @error('problem_solving') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
    >
    @error('problem_solving') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

<!-- Kriteria 4 -->
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label for="komunikasi_kerjasama" class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            4. Komunikasi & Kerja Sama Tim (Soft Skills)
        </label>
        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full flex-shrink-0 ml-2">0-100</span>
    </div>
    <input 
        type="number" min="0" max="100" id="komunikasi_kerjasama"
        wire:model="komunikasi_kerjasama"
        placeholder="0 - 100"
        class="bg-gray-50 dark:bg-gray-800/60 border @error('komunikasi_kerjasama') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
    >
    @error('komunikasi_kerjasama') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

<!-- Kriteria 5 -->
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label for="kualitas_ketepatan" class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            5. Kualitas & Ketepatan Waktu Output Kerja (Deliverables)
        </label>
        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full flex-shrink-0 ml-2">0-100</span>
    </div>
    <input 
        type="number" min="0" max="100" id="kualitas_ketepatan"
        wire:model="kualitas_ketepatan"
        placeholder="0 - 100"
        class="bg-gray-50 dark:bg-gray-800/60 border @error('kualitas_ketepatan') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
    >
    @error('kualitas_ketepatan') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

        <!-- Catatan Tambahan -->
        <div>
            <label for="catatan" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                Catatan Tambahan (Opsional)
            </label>
            <textarea 
                id="catatan"
                wire:model="catatan" rows="3"
                class="bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700/80 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition placeholder-gray-400 dark:placeholder-gray-500"
                placeholder="Tulis catatan evaluasi atau masukan tambahan..."
            ></textarea>
        </div>

        <!-- Footer / Action Buttons -->
        <div class="flex items-center justify-between gap-3 pt-5 border-t border-gray-200 dark:border-gray-800">
            <!-- Tombol Cetak PDF (Sisi Kiri) -->
            <div>
                @if($sudahAdaNilai)
                    <a 
                        href="{{ route('cetak.nilai', ['userId' => $userId]) }}" 
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 border border-red-200 dark:border-red-500/20 rounded-2xl transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                    class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="px-5 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md shadow-blue-600/20 transition"
                >
                    Simpan
                </button>
            </div>
        </div>

    </form>
</div>