<div>
    <!-- Include Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Dashboard</h1>

    <!-- GRID CARDS METRIK -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <!-- Card 1: Peserta PKL -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wide">Peserta PKL</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white mt-1">{{ $totalPeserta }}</p>
            </div>
            <div>
                <img src="/images/profile-placeholder.png" alt="Icon Peserta" class="w-12 h-12 object-contain">
            </div>
        </div>

        <!-- Card 2: Hadir Hari Ini -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wide">Hadir Hari Ini</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white mt-1">{{ $hadirHariIni }}</p>
            </div>
            <div>
                <img src="/images/profile-placeholder.png" alt="Icon Hadir" class="w-12 h-12 object-contain">
            </div>
        </div>

        <!-- Card 3: Terlambat Hari Ini -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wide">Terlambat Hari Ini</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white mt-1">{{ $terlambatHariIni }}</p>
            </div>
            <div>
                <img src="/images/profile-placeholder.png" alt="Icon Terlambat" class="w-12 h-12 object-contain">
            </div>
        </div>

        <!-- Card 4: Izin / Sakit -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wide">Izin / Sakit</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white mt-1">{{ $izinSakitHariIni }}</p>
            </div>
            <div>
                <img src="/images/profile-placeholder.png" alt="Icon Izin" class="w-12 h-12 object-contain">
            </div>
        </div>

        <!-- Card 5: WFH -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wide">WFH</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white mt-1">{{ $wfhHariIni }}</p>
            </div>
            <div>
                <img src="/images/profile-placeholder.png" alt="Icon WFH" class="w-12 h-12 object-contain">
            </div>
        </div>

        <!-- Card 6: WFO -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wide">WFO</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white mt-1">{{ $wfoHariIni }}</p>
            </div>
            <div>
                <img src="/images/profile-placeholder.png" alt="Icon WFO" class="w-12 h-12 object-contain">
            </div>
        </div>

    </div>

    <!-- SECTION GRAFIK PIE -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Grafik Kehadiran Hari Ini</h2>
            
            <!-- Container Alpine.js untuk Chart -->
            <div 
                class="relative h-72 flex justify-center items-center"
                x-data="{
                    chart: null,
                    initChart() {
                        const ctx = document.getElementById('kehadiranPieChart').getContext('2d');
                        if (this.chart) {
                            this.chart.destroy();
                        }
                        this.chart = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ['Hadir', 'Terlambat', 'Izin / Sakit', 'Belum Absen / Alpa'],
                                datasets: [{
                                    data: [
                                        {{ $hadirHariIni }},
                                        {{ $terlambatHariIni }},
                                        {{ $izinSakitHariIni }},
                                        {{ $alpaHariIni }}
                                    ],
                                    backgroundColor: [
                                        '#10B981', // Hijau (Hadir)
                                        '#F59E0B', // Kuning/Oranye (Terlambat)
                                        '#3B82F6', // Biru (Izin/Sakit)
                                        '#EF4444'  // Merah (Alpa/Belum Absen)
                                    ],
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 16,
                                            font: { size: 12 }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }"
                x-init="initChart()"
            >
                <canvas id="kehadiranPieChart"></canvas>
            </div>
        </div>
    </div>
</div>