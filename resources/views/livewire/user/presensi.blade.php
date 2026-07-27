<div>
    <div class="w-full mx-auto max-w-7xl" 
     x-data="{ 
        openModal: false, 
        isCameraOn: false, 
        hasPhoto: false,
        lat: @entangle('latitude'),
        long: @entangle('longitude'),
        locationError: '',

        // State Face Detection
        faceDetector: null,
        faceDetected: false,
        isModelLoading: false,
        detectionInterval: null,

        async loadFaceModel() {
            if (this.faceDetector) return; // sudah ke-load, tidak perlu ulang

            this.isModelLoading = true;
            try {
                await tf.setBackend('webgl');
                await tf.ready();

                const model = faceDetection.SupportedModels.MediaPipeFaceDetector;
                this.faceDetector = await faceDetection.createDetector(model, {
                    runtime: 'mediapipe',
                    solutionPath: 'https://cdn.jsdelivr.net/npm/@mediapipe/face_detection',
                    maxFaces: 1,
                });
            } catch (err) {
                console.error('Gagal load model face detection:', err);
                alert('Gagal memuat model deteksi wajah. Coba refresh halaman.');
            }
            this.isModelLoading = false;
        },

        async initCamera() {
            this.isCameraOn = true;
            this.hasPhoto = false;
            this.faceDetected = false;

            await this.loadFaceModel();

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } } })
                .then(stream => {
                    this.$refs.video.srcObject = stream;
                    this.startFaceDetectionLoop();
                })
                .catch(err => {
                    alert('Kamera tidak dapat diakses atau diizinkan browser!');
                    this.isCameraOn = false;
                });
        },

        startFaceDetectionLoop() {
            if (this.detectionInterval) clearInterval(this.detectionInterval);

            this.detectionInterval = setInterval(async () => {
                if (!this.faceDetector || !this.$refs.video || !this.isCameraOn) return;

                try {
                    const faces = await this.faceDetector.estimateFaces(this.$refs.video);
                    this.faceDetected = faces.length > 0;
                } catch (err) {
                    // diamkan, kadang video belum siap di frame pertama
                }
            }, 500); // cek tiap 0.5 detik
        },
        
        takeSnap() {
            if (!this.faceDetected) {
                alert('Wajah belum terdeteksi! Pastikan wajah Anda terlihat jelas di kamera.');
                return;
            }

            let video = this.$refs.video;
            let canvas = this.$refs.canvas;
            
            let maxWidth = 800;
            let width = video.videoWidth || 640;
            let height = video.videoHeight || 480;

            if (width > maxWidth) {
                height = Math.round((height * maxWidth) / width);
                width = maxWidth;
            }

            canvas.width = width;
            canvas.height = height;

            let context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, width, height);
            
            let compressedImageData = canvas.toDataURL('image/jpeg', 0.7);
            
            @this.set('fotoCaptured', compressedImageData);
            
            this.stopCamera();
            this.hasPhoto = true;
        },

        stopCamera() {
            if (this.detectionInterval) {
                clearInterval(this.detectionInterval);
                this.detectionInterval = null;
            }
            if (this.$refs.video && this.$refs.video.srcObject) {
                this.$refs.video.srcObject.getTracks().forEach(track => track.stop());
            }
            this.isCameraOn = false;
            this.faceDetected = false;
        },
        
        getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.lat = position.coords.latitude;
                        this.long = position.coords.longitude;
                    },
                    (error) => {
                        this.locationError = 'Gagal mengambil lokasi. Pastikan GPS aktif.';
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                this.locationError = 'Browser Anda tidak mendukung Geolocation.';
            }
        },
        
        resetVisualState() {
            this.stopCamera();
            this.hasPhoto = false;
        }
     }"
     x-init="getLocation()">
        <h1 class="text-2xl font-bold mb-6 text-white tracking-wide">FORM PRESENSI HARI INI</h1>

        <!-- Notifikasi Berhasil -->
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-900/80 border border-green-500 text-green-200 rounded-xl flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
                <button type="button" class="text-green-300 hover:text-white" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        <!-- Card Utama Form Presensi -->
        <form wire:submit.prevent="simpanPresensi(); resetVisualState();" class="bg-gray-800 p-6 rounded-xl border border-gray-700/60 shadow-xl mb-8 w-full transition-all duration-300">
            
            <!-- Tab Pilih Tipe Presensi (Masuk / Pulang) -->
            <div class="mb-6 flex gap-2 border-b border-gray-700 pb-2">
                <button type="button" wire:click="$set('tipePresensi', 'masuk')" class="px-4 py-2 rounded-t-lg text-sm font-medium flex items-center gap-2 transition {{ $tipePresensi === 'masuk' ? 'bg-gray-700 text-blue-400 border-b-2 border-blue-500' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Presensi MASUK
                </button>
                <button type="button" wire:click="$set('tipePresensi', 'pulang')" class="px-4 py-2 rounded-t-lg text-sm font-medium flex items-center gap-2 transition {{ $tipePresensi === 'pulang' ? 'bg-gray-700 text-orange-400 border-b-2 border-orange-500' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Presensi PULANG
                </button>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Kamera Box -->
                <div class="flex flex-col gap-3 w-full lg:w-80 flex-shrink-0">
                    <div class="w-full h-64 bg-gray-900 border-2 border-dashed border-gray-700 rounded-xl flex flex-col items-center justify-center text-gray-400 relative overflow-hidden shadow-inner group">
                        <video x-show="isCameraOn" x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                        
                        <template x-if="hasPhoto && !isCameraOn">
                            <img src="{{ $fotoCaptured }}" class="w-full h-full object-cover transition-opacity duration-300">
                        </template>

                        <div x-show="!isCameraOn && !hasPhoto" class="flex flex-col items-center p-4 text-center">
                            <svg class="w-12 h-12 mb-3 text-gray-600 group-hover:text-emerald-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold group-hover:text-gray-300">KAMERA STANDBY</span>
                            <p class="text-[10px] text-gray-600 mt-1">Klik tombol di bawah untuk membuka kamera perangkat Anda</p>
                        </div>

                        <!-- Canvas Hidden (Proses Kompresi) -->
                        <canvas x-ref="canvas" class="hidden"></canvas>
                    </div>

                    @error('fotoCaptured') 
                        <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror

                    <!-- Control Tombol Kamera -->
                    <div class="grid grid-cols-1 gap-2">
                        <!-- Tombol Buka Kamera (Saat Kamera Belum Aktif & Belum Ada Foto) -->
                        <button type="button" x-show="!isCameraOn && !hasPhoto" @click="initCamera()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span>BUKA KAMERA</span>
                        </button>

                        <!-- Tombol Ambil Foto (Pengganti Tombol Baru Anda Saat Kamera Aktif) -->
                        <button type="button" x-show="isCameraOn" @click="takeSnap()" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-blue-600/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h0.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Ambil Foto</span>
                        </button>

                        <!-- Tombol Ambil Ulang Foto (Saat Foto Sudah Berhasil Diambil) -->
                        <button type="button" x-show="hasPhoto && !isCameraOn" @click="initCamera()" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-2">
                            <span>Ambil Ulang Foto</span>
                        </button>
                    </div>
                </div>

                <!-- Detail & Input Data -->
                <div class="flex flex-col space-y-4 w-full flex-1">
                    
                    <!-- Indicator Status GPS / Geolocation -->
                    <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg text-xs shadow-inner">
                        <template x-if="lat && long">
                            <div class="flex items-center gap-2 text-green-400 font-mono">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>Lokasi Terdeteksi: <span x-text="lat"></span>, <span x-text="long"></span></span>
                            </div>
                        </template>
                        <template x-if="!lat || !long">
                            <div class="flex items-center gap-2 text-yellow-400 font-mono">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>⏳ Mendeteksi koordinat GPS Anda...</span>
                            </div>
                        </template>
                        @error('latitude') 
                            <span class="text-red-400 text-xs block mt-2 p-1.5 bg-red-950 border border-red-800 rounded">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Input Logbook (Khusus Presensi PULANG) -->
                    <div x-show="$wire.tipePresensi === 'pulang'" x-collapse x-cloak>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">LOGBOOK HARIAN <span class="text-red-500">*</span></label>
                        <textarea 
                            wire:model="logbook" 
                            rows="5" 
                            placeholder="Tuliskan catatan detail mengenai hasil kegiatan atau tugas magang Anda hari ini (minimal 10 karakter)..." 
                            class="bg-gray-900 border @error('logbook') border-red-500 @else border-gray-700 @enderror text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-3 resize-none shadow-inner transition"></textarea>
                        @error('logbook') 
                            <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Info Box Presensi Masuk -->
                    <div x-show="$wire.tipePresensi === 'masuk'" x-collapse class="p-3.5 bg-gray-900/50 border border-gray-700/50 rounded-lg text-xs text-gray-400 italic flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Untuk presensi masuk, Anda hanya perlu mengambil foto jepretan kamera terbaru. Logbook harian tidak perlu diisi saat presensi masuk.</span>
                    </div>

                    <!-- Tombol Submit Presensi (Tombol Baru Anda) -->
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-4 rounded-xl transition shadow-lg shadow-green-600/30 flex items-center justify-center gap-2">
                        <span>Kirim Presensi</span>
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>