<div> <!-- ELEMEN ROOT TUNGGAL LIVEWIRE -->

    <div class="w-full mx-auto max-w-7xl" 
         x-data="{ 
            openModal: false, 
            isCameraOn: false, 
            hasPhoto: false,
            initCamera() {
                this.isCameraOn = true;
                this.hasPhoto = false;
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } })
                    .then(stream => {
                        this.$refs.video.srcObject = stream;
                    })
                    .catch(err => {
                        alert('Kamera tidak dapat diakses atau diizinkan browser!');
                        this.isCameraOn = false;
                    });
            },
            takeSnap() {
                let video = this.$refs.video;
                let canvas = this.$refs.canvas;
                
                // Set resolusi maksimum agar file tidak terlalu besar (Max width 640px)
                let maxWidth = 640;
                let scale = maxWidth / (video.videoWidth || 640);
                canvas.width = maxWidth;
                canvas.height = (video.videoHeight || 480) * scale;

                let context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Kompres gambar ke format JPEG dengan kualitas 0.65 (Ukuran file turun ke ~60KB)
                let imageData = canvas.toDataURL('image/jpeg', 0.65);
                
                @this.set('fotoCaptured', imageData);
                
                this.stopCamera();
                this.hasPhoto = true;
            },
            stopCamera() {
                if (this.$refs.video && this.$refs.video.srcObject) {
                    this.$refs.video.srcObject.getTracks().forEach(track => track.stop());
                }
                this.isCameraOn = false;
            },
            resetForm() {
                this.stopCamera();
                this.hasPhoto = false;
            }
         }">
        
        <!-- Header Judul -->
        <h1 class="text-2xl font-bold mb-6 text-white tracking-wide">PRESENSI</h1>

        <!-- Alert Notifikasi Flash Message -->
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-900/80 border border-green-500 text-green-200 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
                <button type="button" class="text-green-300 hover:text-white" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        <!-- Section Info Profil User -->
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 mb-8 bg-gray-800 p-6 rounded-xl border border-gray-700/60 shadow-lg w-full">
            <div class="w-28 h-28 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-3xl text-white shadow-inner flex-shrink-0">
                JO
            </div>

            <div class="space-y-2 text-sm md:text-base w-full">
                <div class="flex"><span class="w-24 text-gray-400 font-medium">Nama</span><span class="mr-2">:</span><span class="font-semibold text-white">{{ $nama }}</span></div>
                <div class="flex"><span class="w-24 text-gray-400 font-medium">Jabatan</span><span class="mr-2">:</span><span class="text-gray-200">{{ $jabatan }}</span></div>
                <div class="flex"><span class="w-24 text-gray-400 font-medium">OPD</span><span class="mr-2">:</span><span class="text-gray-200">{{ $opd }}</span></div>
                <div class="flex"><span class="w-24 text-gray-400 font-medium">Bidang</span><span class="mr-2">:</span><span class="text-gray-200">{{ $bidang }}</span></div>
            </div>
        </div>

        <!-- Section Kamera Real-time & Form Presensi -->
        <form wire:submit.prevent="simpanPresensi(); resetForm();" class="bg-gray-800 p-6 rounded-xl border border-gray-700/60 shadow-lg mb-8 w-full">
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Box Kamera / Preview Gambar -->
                <div class="flex flex-col gap-3 w-full lg:w-80">
                    <div class="w-full h-64 bg-gray-900 border-2 border-dashed border-gray-700 rounded-xl flex flex-col items-center justify-center text-gray-400 shadow-inner relative overflow-hidden">
                        
                        <!-- Video Stream saat Kamera Aktif -->
                        <video x-show="isCameraOn" x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                        
                        <!-- Preview Gambar Hasil Jepretan -->
                        <template x-if="hasPhoto && !isCameraOn">
                            <img src="{{ $fotoCaptured }}" class="w-full h-full object-cover">
                        </template>

                        <!-- Standby State saat Kamera Belum Aktif / Setelah Form Di-reset -->
                        <div x-show="!isCameraOn && !hasPhoto" class="flex flex-col items-center">
                            <svg class="w-12 h-12 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold">KAMERA STANDBY</span>
                        </div>

                        <!-- Hidden Canvas untuk memproses capture gambar -->
                        <canvas x-ref="canvas" class="hidden"></canvas>
                    </div>

                    <!-- Tombol Kontrol Kamera -->
                    <div>
                        <button type="button" x-show="!isCameraOn && !hasPhoto" @click="initCamera()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 shadow-md transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 002-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            BUKA KAMERA
                        </button>

                        <button type="button" x-show="isCameraOn" @click="takeSnap()" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 shadow-md animate-pulse transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            AMBIL FOTO / JEPRET
                        </button>

                        <button type="button" x-show="hasPhoto && !isCameraOn" @click="initCamera()" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center justify-center gap-2 transition">
                            AMBIL ULANG FOTO
                        </button>
                    </div>
                </div>

                <!-- Control Waktu, Status & Logbook -->
                <div class="flex flex-col space-y-4 w-full flex-1">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div x-data="{ time: '' }" x-init="setInterval(() => { time = new Date().toLocaleTimeString('id-ID') + ' WIB' }, 1000)" class="bg-gray-900 border border-gray-700 text-center py-2.5 px-6 rounded-lg font-mono text-lg font-bold text-blue-400 shadow-inner flex-1" x-text="time || '00:00:00 WIB'">
                        </div>

                        <div class="bg-gray-900 border border-gray-700 text-center py-2.5 px-6 rounded-lg font-mono text-base text-gray-300 shadow-inner flex-1">
                            {{ date('d/m/Y') }}
                        </div>
                    </div>

                    <select wire:model="status" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="Hadir">Hadir</option>
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                    </select>

                    <!-- Input Logbook Harian -->
                    <div>
                        <textarea 
                            wire:model.live="logbook" 
                            rows="2" 
                            placeholder="Tuliskan catatan logbook harian Anda (wajib diisi)..." 
                            class="bg-gray-900 border @error('logbook') border-red-500 @else border-gray-700 @enderror text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 resize-none">
                        </textarea>
                        @error('logbook') 
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        @if(empty(trim($logbook))) disabled @endif
                        class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition duration-200 
                               @if(empty(trim($logbook))) 
                                   bg-gray-600 cursor-not-allowed opacity-60 
                               @else 
                                   bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-800 shadow-md cursor-pointer 
                               @endif">
                        KIRIM PRESENSI
                    </button>
                </div>
            </div>
        </form>

        <!-- Section Tabel Riwayat Presensi Interaktif -->
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700/60 shadow-lg w-full">
            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">RIWAYAT PRESENSI</h2>
            
            <div class="relative overflow-x-auto shadow-md rounded-lg">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs text-gray-200 uppercase bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3.5">NAMA PESERTA</th>
                            <th scope="col" class="px-6 py-3.5">ASAL SEKOLAH</th>
                            <th scope="col" class="px-6 py-3.5">HARI / TANGGAL</th>
                            <th scope="col" class="px-6 py-3.5">KEHADIRAN</th>
                            <th scope="col" class="px-6 py-3.5">FOTO</th>
                            <th scope="col" class="px-6 py-3.5">LOG BOOK HARIAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/60">
                        @foreach ($riwayat as $index => $item)
                            <tr class="bg-gray-800 hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4 font-medium text-white whitespace-nowrap">{{ $item['nama'] }}</td>
                                <td class="px-6 py-4">{{ $item['sekolah'] }}</td>
                                <td class="px-6 py-4">{{ $item['tanggal'] }}</td>
                                <td class="px-6 py-4">
                                    @if($item['status'] == 'HADIR')
                                        <span class="bg-green-950 text-green-400 text-xs font-semibold px-3 py-1 rounded-md border border-green-800">HADIR</span>
                                    @elseif($item['status'] == 'IZIN')
                                        <span class="bg-yellow-950 text-yellow-400 text-xs font-semibold px-3 py-1 rounded-md border border-yellow-800">IZIN</span>
                                    @else
                                        <span class="bg-red-950 text-red-400 text-xs font-semibold px-3 py-1 rounded-md border border-red-800">SAKIT</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($item['foto'])
                                        <button type="button" wire:click="lihatFoto({{ $index }})" @click="openModal = true" class="text-blue-400 hover:text-blue-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </button>
                                    @else
                                        <span class="text-gray-500 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $item['logbook'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Visual Preview Foto (Pop-Up) -->
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" x-cloak>
            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 max-w-md w-full text-center">
                <h3 class="text-lg font-bold text-white mb-4">Bukti Foto Presensi</h3>
                <div class="w-full h-64 bg-gray-900 rounded-lg flex items-center justify-center border border-gray-700 mb-4 overflow-hidden">
                    @if ($selectedFoto)
                        <img src="{{ $selectedFoto }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-gray-500 text-sm">Foto tidak tersedia</span>
                    @endif
                </div>
                <button type="button" @click="openModal = false" class="bg-gray-700 hover:bg-gray-600 text-white px-5 py-2 rounded-lg text-sm">Tutup</button>
            </div>
        </div>

    </div>

</div>