<div>
    <!-- HEADER TITLE & SUBTITLE -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-wide text-gray-900 dark:text-white uppercase">Dashboard</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan statistik kehadiran dan aktivitas peserta PKL hari ini.</p>
    </div>

    <!-- GRID CARDS METRIK -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

        <!-- Card 1: Peserta PKL -->
        <div class="p-5 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-between transition-all">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Peserta PKL</p>
                <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5">{{ $totalPeserta }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-1.815-4.236 4 4 0 015.815 3.236v3h-4zM5.815 10.764A5.972 5.972 0 004 15v3H0v-3a4 4 0 015.815-3.236z"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: Hadir Hari Ini -->
        <div class="p-5 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-between transition-all">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Hadir Hari Ini</p>
                <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1.5">{{ $hadirHariIni }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: Terlambat Hari Ini -->
        <div class="p-5 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-between transition-all">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Terlambat Hari Ini</p>
                <p class="text-3xl font-extrabold text-amber-500 dark:text-amber-400 mt-1.5">{{ $terlambatHariIni }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 flex items-center justify-center text-amber-500 dark:text-amber-400 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

        <!-- Card 4: Izin / Sakit -->
        <div class="p-5 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-between transition-all">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Izin / Sakit</p>
                <p class="text-3xl font-extrabold text-sky-600 dark:text-sky-400 mt-1.5">{{ $izinSakitHariIni }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

        <!-- Card 5: WFH -->
        <div class="p-5 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-between transition-all">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">WFH</p>
                <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1.5">{{ $wfhHariIni }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l1.293 1.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
            </div>
        </div>

        <!-- Card 6: WFO -->
        <div class="p-5 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-between transition-all">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">WFO</p>
                <p class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mt-1.5">{{ $wfoHariIni }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/20 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1a1 1 0 000 2h1a1 1 0 100-2H7zm0 4a1 1 0 000 2h1a1 1 0 100-2H7zm0 4a1 1 0 000 2h1a1 1 0 100-2H7zm5-8a1 1 0 000 2h1a1 1 0 100-2h-1zm0 4a1 1 0 000 2h1a1 1 0 100-2h-1zm0 4a1 1 0 000 2h1a1 1 0 100-2h-1z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

    </div>

    <!-- SECTION GRAFIK PIE -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl" wire:ignore>
            <h2 class="text-xs font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase mb-1">Grafik Kehadiran Hari Ini</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Persentase status presensi seluruh peserta PKL.</p>
            
            <!-- Container Chart dengan Dynamic Color Detection -->
            <div 
                class="relative h-72 flex justify-center items-center"
                x-data="{
                    chart: null,
                    initChart() {
                        this.$nextTick(() => {
                            const canvas = document.getElementById('kehadiranPieChart');
                            if (!canvas || typeof Chart === 'undefined') return;

                            const isDark = document.documentElement.classList.contains('dark');
                            const textColor = isDark ? '#94a3b8' : '#475569';
                            const borderColor = isDark ? '#111827' : '#ffffff';

                            const ctx = canvas.getContext('2d');
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
                                            '#10B981',
                                            '#F59E0B',
                                            '#0284C7',
                                            '#EF4444'
                                        ],
                                        borderWidth: 2,
                                        borderColor: borderColor
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
                                                padding: 20,
                                                color: textColor,
                                                font: { 
                                                    size: 12,
                                                    weight: '500'
                                                }
                                            }
                                        }
                                    }
                                }
                            });
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
