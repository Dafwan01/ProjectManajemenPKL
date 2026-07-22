<div class="w-full">
    <div class="mb-6 border-b pb-4 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Jadwal Kerja (Senin - Jumat)</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Batas jam masuk paling awal: <strong>07:30</strong> | Batas jam keluar paling lama: <strong>16:30</strong>
        </p>
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($daftarHari as $hari)
                <div class="py-4 first:pt-0 last:pb-0 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    
                    <!-- Label Hari -->
                    <div class="md:col-span-2">
                        <span class="font-semibold text-base text-gray-900 dark:text-white">{{ $hari }}</span>
                    </div>

                    <!-- Input Jam Masuk, Jam Keluar, Status Kerja -->
                    <div class="md:col-span-10 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        
                        <!-- Jam Masuk -->
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Jam Masuk</label>
                            <input 
                                type="time" 
                                min="07:30" 
                                max="16:30" 
                                wire:model="jadwalData.{{ $hari }}.jam_masuk"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('jadwalData.'.$hari.'.jam_masuk') border-red-500 @enderror"
                            >
                            @error("jadwalData.{$hari}.jam_masuk")
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Jam Keluar -->
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Jam Keluar</label>
                            <input 
                                type="time" 
                                min="07:30" 
                                max="16:30" 
                                wire:model="jadwalData.{{ $hari }}.jam_keluar"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('jadwalData.'.$hari.'.jam_keluar') border-red-500 @enderror"
                            >
                            @error("jadwalData.{$hari}.jam_keluar")
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status Kerja -->
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Status Kerja</label>
                            <select 
                                wire:model="jadwalData.{{ $hari }}.status_kerja"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                @foreach(App\Enums\JadwalStatusKerja::cases() as $statusEnum)
                                    <option value="{{ $statusEnum->value }}">{{ $statusEnum->value }}</option>
                                @endforeach
                            </select>
                            @error("jadwalData.{$hari}.status_kerja")
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t dark:border-gray-700">
            <button 
                type="button"
                wire:click="$parent.closeJadwalModal"
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

    </form>
</div>