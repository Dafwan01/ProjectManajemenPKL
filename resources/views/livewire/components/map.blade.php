<!-- Modal Overlay -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-4 overflow-y-auto">
    
    <div 
        x-data="{
            map: null,
            renderMap(locations) {
                this.$nextTick(() => {
                    const container = document.getElementById('map-container');
                    if (!container) return;

                    // Hapus instance peta lama jika ada
                    if (this.map) {
                        this.map.remove();
                        this.map = null;
                    }
                    // KOORDINAT DEFAULT BALAI KOTA BOGOR
                    const defaultLat = -6.5952;
                    const defaultLng = 106.7937;

                    // Inisialisasi Peta
                    this.map = L.map('map-container', {
                        zoomControl: false // custom placement atau zoom standar
                    }).setView([defaultLat, defaultLng], 14);

                    L.control.zoom({ position: 'bottomright' }).addTo(this.map);

                    // Tile CartoDB (Voyager / Fast)
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19,
                        subdomains: 'abcd',
                        attribution: '&copy; OpenStreetMap &copy; CARTO'
                    }).addTo(this.map);

                    // Penyesuaian ukuran modal
                    setTimeout(() => {
                        this.map.invalidateSize();
                    }, 100);

                    // Plot Markers
                    if (locations && locations.length > 0) {
                        const bounds = [];
                        locations.forEach(loc => {
                            if (loc.lat && loc.lng) {
                                const marker = L.marker([loc.lat, loc.lng]).addTo(this.map);
                                
                                const popupContent = `
                                    <div class='p-1 font-sans'>
                                        <div class='font-bold text-sm text-gray-900 border-b border-gray-100 pb-1.5 mb-1.5'>
                                            ${loc.nama}
                                        </div>
                                        <div class='text-xs text-gray-600 mb-1 flex items-center gap-1'>
                                            <span>🏫</span> <span>${loc.sekolah}</span>
                                        </div>
                                        <div class='mt-2 pt-1 border-t border-gray-100 space-y-0.5 text-xs'>
                                            <div class='text-blue-600 font-medium'>
                                                ⏰ Jam Masuk: <span class='font-semibold'>${loc.jam_masuk || '-'} WIB</span>
                                            </div>
                                            <div class='text-emerald-600 font-medium'>
                                                ⏰ Jam Keluar: <span class='font-semibold'>${loc.jam_keluar || '-'} WIB</span>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                marker.bindPopup(popupContent, {
                                    className: 'custom-leaflet-popup'
                                });
                                bounds.push([loc.lat, loc.lng]);
                            }
                        });

                        if (bounds.length > 0) {
                            this.map.fitBounds(bounds, { padding: [50, 50] });
                        }
                    }
                });
            }
        }"
        x-init="renderMap($wire.locations)"
        @init-leaflet-map.window="renderMap($event.detail.locations)"
        class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700/80 p-6 space-y-5"
    >
        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700/80 pb-4">
            <div>
                <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Peta Lokasi Absensi</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Sebaran lokasi absensi tanggal: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</span>
                </p>
            </div>
            
            <button 
                type="button" 
                wire:click="closeMap" 
                class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/60 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Container Tempat Peta Digambar -->
        <div wire:ignore class="relative overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-inner">
            <div id="map-container" class="w-full h-[460px] z-10 bg-gray-100 dark:bg-gray-900/50"></div>
        </div>

        <!-- Tombol Tutup -->
        <div class="flex items-center justify-end pt-2 border-t border-gray-200 dark:border-gray-700/80">
            <button 
                type="button" 
                wire:click="closeMap" 
                class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition"
            >
                Tutup Peta
            </button>
        </div>

    </div>
</div>