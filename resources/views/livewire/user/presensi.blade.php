<div>
 
    <!-- Global Instance agar tidak dibungkus Alpine Proxy -->
    <script>
        let faceDetectorInstance = null;
    </script>

    <div class="w-full mx-auto max-w-7xl" 
         wire:ignore.self
         x-data="{ 
            tipePresensi: $wire.entangle('tipePresensi'),
            
            // 1. SINKRONISASI VARIABLE DARI BACKEND LIVEWIRE (PENTING!)
            statusKerja: $wire.entangle('statusKerja'),
            isWfa: $wire.entangle('isWfa'),
            maxRadiusMeters: $wire.entangle('maxRadiusMeters'),

            isCameraOn: false, 
            hasPhoto: false,
            photoPreview: null,
            locationError: '',
            locationAccuracy: null,
            watchId: null,
            _visibilityHandler: null,

            // Konfigurasi Geofencing Kantor
            targetLat: -6.595181,
            targetLng: 106.793836,
            distance: null,
            isWithinRadius: true,

            // MediaPipe State
            faceDetected: false,
            isModelLoading: false,
            modelsLoaded: false,
            animFrameId: null,
            isDetecting: false,

            // Formula Haversine (Hitung Jarak GPS)
            calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371000; 
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = 
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return Math.round(R * c);
            },

            // Pengambilan Lokasi Akurat Menggunakan watchPosition
            getLocation() {
                console.log('[DEBUG] getLocation() dipanggil');
                this.locationError = '';
                
                if (!navigator.geolocation) {
                    this.locationError = 'Browser Anda tidak mendukung Geolocation.';
                    return;
                }

                if (this.watchId) {
                    navigator.geolocation.clearWatch(this.watchId);
                    this.watchId = null;
                }

                this.watchId = navigator.geolocation.watchPosition(
                    (position) => {
                        console.log('[DEBUG] Posisi GPS diterima:', position.coords.latitude, position.coords.longitude);
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        this.locationAccuracy = Math.round(position.coords.accuracy);

                        this.$wire.latitude = lat;
                        this.$wire.longitude = lng;

                        this.distance = this.calculateDistance(lat, lng, this.targetLat, this.targetLng);
                        
                        // Validasi Radius Dinamis (Jika WFA selalu true, jika WFO cek <= maxRadiusMeters)
                        if (this.isWfa || this.statusKerja === 'wfh') {
                            this.isWithinRadius = true;
                            this.locationError = '';
                        } else {
                            this.isWithinRadius = this.distance <= this.maxRadiusMeters;
                            if (!this.isWithinRadius) {
                                this.locationError = `Di luar radius kantor (${this.distance}m / Maksimal ${this.maxRadiusMeters}m).`;
                            } else {
                                this.locationError = '';
                            }
                        }
                    },
                    (error) => {
                        console.log('[DEBUG] Error GPS:', error.code, error.message);
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                this.locationError = 'Izin lokasi ditolak. Harap izinkan akses lokasi pada browser.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                this.locationError = 'Informasi lokasi tidak tersedia. Coba aktifkan GPS HP/Browser.';
                                break;
                            case error.TIMEOUT:
                                this.locationError = 'Waktu pengambilan lokasi habis (timeout). Sinyal GPS lemah.';
                                break;
                            default:
                                this.locationError = 'Gagal mendapatkan lokasi GPS.';
                                break;
                        }
                    },
                    { 
                        enableHighAccuracy: true,
                        timeout: 20000,
                        maximumAge: 0
                    }
                );
            },

            /**
             * Pasang listener visibilitychange: saat tab disembunyikan, watch GPS lama dihentikan
             * (browser sering membekukan watchPosition di background sehingga jadi zombie).
             * Saat tab kembali terlihat, watch GPS di-restart dari nol supaya tidak macet
             * dan tidak perlu refresh manual.
             */
            initVisibilityListener() {
                this._visibilityHandler = () => this.handleVisibilityChange();
                document.addEventListener('visibilitychange', this._visibilityHandler);
            },

            /**
             * Bersihkan watch GPS & kamera SEBELUM Livewire berpindah halaman (wire:navigate).
             * PENTING: karena wire:navigate tidak melakukan full page reload, JS context browser
             * tetap hidup dan watchPosition() lama TIDAK otomatis berhenti kalau tidak dibersihkan
             * secara eksplisit. Event 'unmount' BUKAN event bawaan window, jadi tidak pernah terpicu -
             * itu sebabnya watch lama menumpuk terus setiap kali pindah menu lalu balik lagi,
             * sampai browser akhirnya mengabaikan watch yang baru.
             */
            cleanupSebelumNavigasi() {
                console.log('[DEBUG] Cleanup sebelum navigasi, watchId:', this.watchId);
                this.stopCamera();
                if (this.watchId) {
                    navigator.geolocation.clearWatch(this.watchId);
                    this.watchId = null;
                }
                if (this._visibilityHandler) {
                    document.removeEventListener('visibilitychange', this._visibilityHandler);
                    this._visibilityHandler = null;
                }
            },

            handleVisibilityChange() {
                console.log('[DEBUG] visibilitychange terpicu, state:', document.visibilityState, 'watchId lama:', this.watchId);
                if (document.visibilityState === 'visible') {
                    console.log('[DEBUG] Tab aktif kembali, restart getLocation()');
                    this.getLocation();
                } else {
                    console.log('[DEBUG] Tab disembunyikan, clearWatch dulu');
                    if (this.watchId) {
                        navigator.geolocation.clearWatch(this.watchId);
                        this.watchId = null;
                    }
                }
            },

            async loadFaceModel() {
                if (this.modelsLoaded) return;

                this.isModelLoading = true;
                try {
                  faceDetectorInstance = new FaceDetection({
    locateFile: (file) => `/vendor/mediapipe/${file}`
});

                    faceDetectorInstance.setOptions({
                        model: 'short',
                        minDetectionConfidence: 0.6,
                    });

                    faceDetectorInstance.onResults((results) => {
                        this.faceDetected = !!(results.detections && results.detections.length > 0);
                    });

                    await faceDetectorInstance.initialize();
                    this.modelsLoaded = true;
                } catch (err) {
                    console.error('Gagal memuat model MediaPipe:', err);
                    alert('Gagal memuat model deteksi wajah. Silakan refresh halaman.');
                } finally {
                    this.isModelLoading = false;
                }
            },

            async initCamera() {
                this.isCameraOn = true;
                this.hasPhoto = false;
                this.photoPreview = null;
                this.faceDetected = false;

                await this.loadFaceModel();

                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } } 
                    });
                    
                    this.$refs.video.srcObject = stream;
                    this.$refs.video.onloadedmetadata = () => {
                        this.$refs.video.play();
                        this.detectLoop();
                    };
                } catch (err) {
                    alert('Kamera tidak dapat diakses atau izin ditolak!');
                    this.isCameraOn = false;
                }
            },

            async detectLoop() {
                if (!this.isCameraOn || !faceDetectorInstance || !this.$refs.video) return;

                if (!this.isDetecting && this.$refs.video.readyState >= 2) {
                    this.isDetecting = true;
                    try {
                        await faceDetectorInstance.send({ image: this.$refs.video });
                    } catch (e) {
                        console.error('Error MediaPipe Frame:', e);
                    }
                    this.isDetecting = false;
                }

                setTimeout(() => {
                    if (this.isCameraOn) {
                        this.animFrameId = requestAnimationFrame(() => this.detectLoop());
                    }
                }, 200); 
            },

            takeSnap() {
                if (!this.$refs.video || !this.$refs.canvas) return;
                
                const video = this.$refs.video;
                const canvas = this.$refs.canvas;
                
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const dataUrl = canvas.toDataURL('image/jpeg');

                this.photoPreview = dataUrl;
                this.$wire.fotoCaptured = dataUrl;
                
                this.hasPhoto = true;
                this.isCameraOn = false;
                this.stopCamera();
            },

            stopCamera() {
                this.isCameraOn = false;
                this.isDetecting = false;
                
                if (this.$refs.video && this.$refs.video.srcObject) {
                    const tracks = this.$refs.video.srcObject.getTracks();
                    tracks.forEach(track => track.stop());
                }
                
                if (this.animFrameId) {
                    cancelAnimationFrame(this.animFrameId);
                }
            },

            resetVisualState() {
                this.stopCamera();
                this.hasPhoto = false;
                this.photoPreview = null;
            }
         }"
         x-init="
            getLocation();
            initVisibilityListener();
            window.addEventListener('livewire:navigating', () => cleanupSebelumNavigasi(), { once: true });
         ">
         
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white tracking-wide">FORM PRESENSI HARI INI</h1>

        <!-- Notifikasi Success -->
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/80 border border-green-400 dark:border-green-500 text-green-800 dark:text-green-200 rounded-xl flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('message') }}</span>
                </div>
                <button type="button" class="text-green-600 dark:text-green-300 hover:text-gray-900 dark:hover:text-white" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        <!-- Notifikasi Warning / Alert -->
        @if (session()->has('warning'))
            <div class="mb-6 p-4 bg-amber-100 dark:bg-amber-900/80 border border-amber-400 dark:border-amber-500 text-amber-800 dark:text-amber-200 rounded-xl flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('warning') }}</span>
                </div>
                <button type="button" class="text-amber-600 dark:text-amber-300 hover:text-gray-900 dark:hover:text-white" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        <!-- Card Utama Form Presensi -->
        <form wire:submit.prevent="simpanPresensi(); resetVisualState();" class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xl mb-8 w-full transition-all duration-300">
            
            <!-- Tab Pilih Tipe Presensi (Masuk / Pulang) -->
            <div class="mb-6 flex gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                <button type="button" 
                    @click="tipePresensi = 'masuk'; $wire.setTipePresensi('masuk')" 
                    :class="tipePresensi === 'masuk' ? 'bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400 border-b-2 border-blue-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/50'"
                    class="px-4 py-2 rounded-t-lg text-sm font-medium flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Presensi MASUK
                </button>

                <button type="button" 
                    @click="tipePresensi = 'pulang'; $wire.setTipePresensi('pulang')" 
                    :class="tipePresensi === 'pulang' ? 'bg-gray-100 dark:bg-gray-700 text-orange-600 dark:text-orange-400 border-b-2 border-orange-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/50'"
                    class="px-4 py-2 rounded-t-lg text-sm font-medium flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Presensi PULANG
                </button>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Kamera Box -->
                <div class="flex flex-col gap-3 w-full lg:w-80 flex-shrink-0" wire:ignore>
                    <div class="w-full h-64 bg-gray-100 dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 relative overflow-hidden shadow-inner group">
                        
                        <video x-show="isCameraOn" x-ref="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        
                        <template x-if="hasPhoto && !isCameraOn">
                            <img :src="photoPreview" class="w-full h-full object-cover transition-opacity duration-300">
                        </template>

                        <div x-show="!isCameraOn && !hasPhoto" class="flex flex-col items-center p-4 text-center">
                            <svg class="w-12 h-12 mb-3 text-gray-400 dark:text-gray-600 group-hover:text-emerald-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500 font-semibold group-hover:text-gray-700 dark:group-hover:text-gray-300">KAMERA STANDBY</span>
                            <p class="text-[10px] text-gray-400 dark:text-gray-600 mt-1">Klik tombol di bawah untuk membuka kamera perangkat Anda</p>
                        </div>

                        <!-- Indikator Loading Model -->
                        <div x-show="isCameraOn && isModelLoading" class="absolute inset-0 bg-black/70 flex items-center justify-center z-10">
                            <div class="flex flex-col items-center gap-2 text-white text-xs">
                                <svg class="w-6 h-6 animate-spin text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Memuat model deteksi wajah...</span>
                            </div>
                        </div>

                        <!-- Indikator Status Wajah -->
                        <div x-show="isCameraOn && !isModelLoading" class="absolute top-2 left-2 right-2 flex justify-center z-10">
                            <span 
                                x-show="faceDetected"
                                class="px-3 py-1 bg-green-600/90 text-white text-xs font-semibold rounded-full flex items-center gap-1.5 shadow"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Wajah Terdeteksi
                            </span>
                            <span 
                                x-show="!faceDetected"
                                class="px-3 py-1 bg-red-600/90 text-white text-xs font-semibold rounded-full flex items-center gap-1.5 shadow"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Wajah Tidak Terdeteksi
                            </span>
                        </div>

                        <!-- Canvas Hidden -->
                        <canvas x-ref="canvas" class="hidden"></canvas>
                    </div>

                    @error('fotoCaptured') 
                        <span class="text-red-500 dark:text-red-400 text-xs block">{{ $message }}</span> 
                    @enderror

                    <!-- Control Tombol Kamera -->
                    <div class="grid grid-cols-1 gap-2">
                        <button type="button" x-show="!isCameraOn && !hasPhoto" @click="initCamera()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span>BUKA KAMERA</span>
                        </button>

                        <button 
                            type="button" 
                            x-show="isCameraOn" 
                            @click="takeSnap()" 
                            :disabled="!faceDetected"
                            :class="faceDetected ? 'bg-blue-600 hover:bg-blue-500 cursor-pointer' : 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed opacity-60'"
                            class="w-full text-white font-semibold py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h0.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-text="faceDetected ? 'Ambil Foto' : 'Menunggu Wajah...'"></span>
                        </button>

                        <button type="button" x-show="hasPhoto && !isCameraOn" @click="initCamera()" class="w-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Ambil Ulang Foto</span>
                        </button>
                    </div>
                </div>

                <!-- Detail & Input Data -->
                <div class="flex flex-col space-y-4 w-full flex-1">
                    
                    <!-- 2. PERBAIKAN: INDICATOR STATUS GPS / GEOFENCING DINAMIS -->
                    <div class="p-4 bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-xs shadow-inner">
                        <template x-if="$wire.latitude && $wire.longitude">
                            <div class="flex flex-col gap-1 font-mono">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2" :class="isWithinRadius ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        
                                        <!-- Teks Status Dinamis Berdasarkan Mode WFH / WFO -->
                                        <template x-if="isWfa || statusKerja === 'wfh'">
                                            <span>Mode Kerja: WFH / WFA (Bebas Radius)</span>
                                        </template>
                                        <template x-if="!isWfa && statusKerja === 'wfo'">
                                            <span x-text="isWithinRadius ? `Mode WFO: Dalam Radius Kantor` : `Mode WFO: Di Luar Radius (Max ${maxRadiusMeters}m)`"></span>
                                        </template>
                                    </div>

                                    <!-- Jarak meter dari kantor -->
                                    <span class="text-gray-500 dark:text-gray-400" x-text="`${distance}m dari pusat`"></span>
                                </div>

                                <div class="text-[10px] text-gray-500 dark:text-gray-400 flex justify-between mt-1 pt-1 border-t border-gray-200 dark:border-gray-800">
                                    <span>Akurasi GPS: ±<span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="locationAccuracy"></span> meter</span>
                                    <button type="button" @click="getLocation()" class="text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">Refresh GPS</button>
                                </div>
                            </div>
                        </template>

                        <template x-if="!$wire.latitude || !$wire.longitude">
                            <div class="flex items-center justify-between text-yellow-600 dark:text-yellow-400 font-mono">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span>⏳ Mengunci GPS presisi tinggi...</span>
                                </div>
                            </div>
                        </template>

                        <div x-show="locationError" class="text-red-600 dark:text-red-400 mt-2 font-sans font-medium" x-text="locationError"></div>

                        @error('latitude') 
                            <span class="text-red-600 dark:text-red-400 text-xs block mt-2 p-1.5 bg-red-50 dark:bg-red-950 border border-red-300 dark:border-red-800 rounded">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Input Logbook (Khusus Presensi PULANG) -->
                    <div x-show="$wire.tipePresensi === 'pulang'" x-collapse x-cloak>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">LOGBOOK HARIAN <span class="text-red-500">*</span></label>
                        <textarea 
                            wire:model="logbook" 
                            rows="5" 
                            placeholder="Tuliskan catatan detail mengenai hasil kegiatan atau tugas Anda hari ini (minimal 10 karakter)..." 
                            class="bg-gray-100 dark:bg-gray-900 border @error('logbook') border-red-500 @else border-gray-300 dark:border-gray-700 @enderror text-gray-900 dark:text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-3 resize-none shadow-inner transition"></textarea>
                        @error('logbook') 
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1.5 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Info Box Presensi Masuk -->
                    <div x-show="$wire.tipePresensi === 'masuk'" x-collapse class="p-3.5 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700/50 rounded-lg text-xs text-gray-500 dark:text-gray-400 italic flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Untuk presensi masuk, Anda hanya perlu mengambil foto jepretan kamera terbaru. Logbook harian tidak perlu diisi saat presensi masuk.</span>
                    </div>

                    <!-- Pengecekan Logika Disabled Sisi Backend -->
                    @php
                        $isDisabledByBackend = $isLulus 
                            || ($tipePresensi === 'masuk' && $sudahAbsenMasuk) 
                            || ($tipePresensi === 'pulang' && $sudahAbsenKeluar);
                    @endphp

                    <!-- 3. PERBAIKAN: TOMBOL SUBMIT DENGAN NAMA LABEL DINAMIS -->
                    <button 
                        type="submit" 
                        :disabled="!isWithinRadius || {{ $isDisabledByBackend ? 'true' : 'false' }}"
                        :class="(isWithinRadius && !{{ $isDisabledByBackend ? 'true' : 'false' }}) 
                                ? 'bg-green-600 hover:bg-green-500 cursor-pointer' 
                                : 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed opacity-50'"
                        class="w-full text-white font-bold py-3 px-4 rounded-xl transition shadow-lg flex items-center justify-center gap-2">
                        
                        @if ($isLulus)
                            <span>Status Akun Lulus (Nonaktif)</span>
                        @elseif ($tipePresensi === 'masuk' && $sudahAbsenMasuk)
                            <span>Sudah Absen Masuk Hari Ini</span>
                        @elseif ($tipePresensi === 'pulang' && $sudahAbsenKeluar)
                            <span>Sudah Absen Pulang Hari Ini</span>
                        @else
                            <span x-text="isWithinRadius ? 'Kirim Presensi' : `Di Luar Radius Kantor (Max ${maxRadiusMeters}m)`"></span>
                        @endif
                    </button>

                </div>
            </div>
        </form>

    </div>
</div>