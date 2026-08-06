<div class="w-full">
    <!-- Header Section -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Edit Jadwal Kerja (Senin - Jumat)</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Batas jam masuk paling awal: <strong class="text-gray-700 dark:text-gray-300">07:30</strong> | Batas jam keluar paling lama: <strong class="text-gray-700 dark:text-gray-300">16:30</strong>
        </p>
    </div>

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-700 dark:text-emerald-400 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif
    <div class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50 text-blue-700 dark:text-blue-300 text-xs rounded-xl flex items-start gap-2 shadow-sm">
    <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span>
        <strong>Catatan:</strong> Jadwal kerja yang diatur di sini akan <strong>berlaku sepanjang masa magang</strong>  Perubahan jadwal akan otomatis diterapkan untuk seluruh periode PKL.
    </span>
</div>

    <form wire:submit.prevent="save" class="space-y-6">
        
        <div class="divide-y divide-gray-200 dark:divide-gray-800">
            @foreach($daftarHari as $hari)
                <div class="py-4 first:pt-0 last:pb-0 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    
                    <!-- Label Hari -->
                    <div class="md:col-span-2">
                        <span class="font-bold text-sm text-gray-900 dark:text-white uppercase tracking-wider">{{ $hari }}</span>
                    </div>

                    <!-- Input Jam Masuk, Jam Keluar, Status Kerja -->
                    <div class="md:col-span-10 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        
                        <!-- Jam Masuk -->
                        <div>
                            <label class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jam Masuk</label>
                            <input 
                                type="time" 
                                min="07:30" 
                                max="16:30" 
                                wire:model="jadwalData.{{ $hari }}.jam_masuk"
                                class="bg-gray-50 dark:bg-gray-800/60 border @error('jadwalData.'.$hari.'.jam_masuk') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                            >
                            @error("jadwalData.{$hari}.jam_masuk")
                                <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Jam Keluar -->
                        <div>
                            <label class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jam Keluar</label>
                            <input 
                                type="time" 
                                min="07:30" 
                                max="16:30" 
                                wire:model="jadwalData.{{ $hari }}.jam_keluar"
                                class="bg-gray-50 dark:bg-gray-800/60 border @error('jadwalData.'.$hari.'.jam_keluar') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                            >
                            @error("jadwalData.{$hari}.jam_keluar")
                                <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status Kerja -->
                        <div>
                            <label class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status Kerja</label>
                            <select 
                                wire:model="jadwalData.{{ $hari }}.status_kerja"
                                class="bg-gray-50 dark:bg-gray-800/60 border @error('jadwalData.'.$hari.'.status_kerja') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700/80 @enderror text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition"
                            >
                                @foreach(App\Enums\JadwalStatusKerja::cases() as $statusEnum)
                                    <option value="{{ $statusEnum->value }}">{{ $statusEnum->value }}</option>
                                @endforeach
                            </select>
                            @error("jadwalData.{$hari}.status_kerja")
                                <span class="text-red-500 dark:text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200 dark:border-gray-800">
            <button 
                type="button"
                wire:click="$parent.closeJadwalModal"
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

    </form>
</div>