<!-- Modal Overlay -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-md p-4 overflow-y-auto">
    
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
                    this.map = L.map('map-container').setView([defaultLat, defaultLng], 14);

                    // Tile CartoDB (Cepat)
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19,
                        subdomains: 'abcd',
                        attribution: '&copy; OpenStreetMap &copy; CARTO'
                    }).addTo(this.map);

                    // Penyesuaian ukuran modal
                    setTimeout(() => {
                        this.map.invalidateSize();
                    }, 50);

                    // Plot Markers
                    if (locations && locations.length > 0) {
                        const bounds = [];
                        locations.forEach(loc => {
                            if (loc.lat && loc.lng) {
                                const marker = L.marker([loc.lat, loc.lng]).addTo(this.map);
                                marker.bindPopup(`
                                    <div class='p-1'>
                                        <b class='text-sm text-gray-900'>${loc.nama}</b><br>
                                        <span class='text-xs text-gray-600'>🏫 ${loc.sekolah}</span><br>
                                        <span class='text-xs text-blue-600 font-semibold'>⏰ Jam Masuk: ${loc.jam_masuk} WIB</span> <br>
                                        <span class='text-xs text-blue-600 font-semibold'>⏰ Jam Keluar: ${loc.jam_keluar} WIB</span>
                                    </div>
                                `);
                                bounds.push([loc.lat, loc.lng]);
                            }
                        });

                        if (bounds.length > 0) {
                            this.map.fitBounds(bounds, { padding: [40, 40] });
                        }
                    }
                });
            }
        }"
        x-init="renderMap($wire.locations)"
        @init-leaflet-map.window="renderMap($event.detail.locations)"
        class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-4"
    >
        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b dark:border-gray-700 pb-3">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Peta Lokasi Absensi</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Sebaran lokasi absensi tanggal: <strong>{{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</strong>
                </p>
            </div>
            
            <button type="button" wire:click="closeMap" class="text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-lg p-1.5 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Container Tempat Peta Digambar -->
        <div wire:ignore>
            <div id="map-container" class="w-full h-[450px] rounded-lg border border-gray-200 dark:border-gray-700 z-10 bg-gray-100 dark:bg-gray-700"></div>
        </div>

        <!-- Tombol Tutup -->
        <div class="flex justify-end pt-2 border-t dark:border-gray-700">
            <button type="button" wire:click="closeMap" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                Tutup Peta
            </button>
        </div>

    </div>
</div>