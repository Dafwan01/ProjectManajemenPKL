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

        <!-- Card 7: Rata-rata Jam Masuk Hari Ini -->
        <div class="p-5 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-between transition-all">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Rata-rata Jam Masuk</p>
                <p class="text-3xl font-extrabold text-teal-600 dark:text-teal-400 mt-1.5">
                    {{ $rataRataJamMasukHariIni ?? '-' }}
                </p>
                @if(!$rataRataJamMasukHariIni)
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Belum ada yang absen hari ini</p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-500/10 border border-teal-200 dark:border-teal-500/20 flex items-center justify-center text-teal-600 dark:text-teal-400 shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v5a1 1 0 00.5.866l4 2.3a1 1 0 001-1.732L11 9.42V6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- SECTION TREN KEHADIRAN 30 HARI (LINE CHART) -->
    <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl mb-8" wire:ignore>
        <h2 class="text-xs font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase mb-1">Tren Kehadiran 30 Hari Terakhir</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Pola harian jumlah peserta Hadir, Terlambat, dan Izin/Sakit.</p>

        <div class="relative h-80"
             data-labels="{{ json_encode($trenLabels) }}"
             data-hadir="{{ json_encode($trenHadir) }}"
             data-terlambat="{{ json_encode($trenTerlambat) }}"
             data-izin-sakit="{{ json_encode($trenIzinSakit) }}"
             x-data="{
                initChart() {
                    this.$nextTick(() => {
                        const canvas = document.getElementById('trenKehadiranChart');
                        if (!canvas || typeof Chart === 'undefined') return;

                        const isDark = document.documentElement.classList.contains('dark');
                        const textColor = isDark ? '#94a3b8' : '#475569';
                        const gridColor = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(71,85,105,0.08)';

                        const labels = JSON.parse(this.$el.dataset.labels || '[]');
                        const hadir = JSON.parse(this.$el.dataset.hadir || '[]');
                        const terlambat = JSON.parse(this.$el.dataset.terlambat || '[]');
                        const izinSakit = JSON.parse(this.$el.dataset.izinSakit || '[]');

                        new Chart(canvas.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Hadir',
                                        data: hadir,
                                        borderColor: '#10B981',
                                        backgroundColor: 'rgba(16,185,129,0.1)',
                                        tension: 0.3,
                                        fill: true,
                                        pointRadius: 2
                                    },
                                    {
                                        label: 'Terlambat',
                                        data: terlambat,
                                        borderColor: '#F59E0B',
                                        backgroundColor: 'rgba(245,158,11,0.1)',
                                        tension: 0.3,
                                        fill: true,
                                        pointRadius: 2
                                    },
                                    {
                                        label: 'Izin / Sakit',
                                        data: izinSakit,
                                        borderColor: '#0284C7',
                                        backgroundColor: 'rgba(2,132,199,0.1)',
                                        tension: 0.3,
                                        fill: true,
                                        pointRadius: 2
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: { mode: 'index', intersect: false },
                                scales: {
                                    x: {
                                        ticks: { color: textColor, maxRotation: 0, autoSkip: true, maxTicksLimit: 10 },
                                        grid: { color: gridColor }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        ticks: { color: textColor, precision: 0 },
                                        grid: { color: gridColor }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { color: textColor, font: { size: 12 } }
                                    }
                                }
                            }
                        });
                    });
                }
             }" x-init="initChart()">
            <canvas id="trenKehadiranChart"></canvas>
        </div>
    </div>

    <!-- SECTION GRAFIK (DUA PIE CHART) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Grafik Pie Kehadiran -->
        <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl" wire:ignore>
            <h2 class="text-xs font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase mb-1">Grafik Kehadiran Hari Ini</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Persentase status presensi seluruh peserta PKL.</p>
            
            <div class="relative h-72 flex justify-center items-center" x-data="{
                initChart() {
                    this.$nextTick(() => {
                        const canvas = document.getElementById('kehadiranPieChart');
                        if (!canvas || typeof Chart === 'undefined') return;

                        const isDark = document.documentElement.classList.contains('dark');
                        const textColor = isDark ? '#94a3b8' : '#475569';
                        const borderColor = isDark ? '#111827' : '#ffffff';

                        new Chart(canvas.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: ['Hadir', 'Terlambat', 'Izin / Sakit', 'Belum Absen / Alpa'],
                                datasets: [{
                                    data: [{{ $hadirHariIni }}, {{ $terlambatHariIni }}, {{ $izinSakitHariIni }}, {{ $alpaHariIni }}],
                                    backgroundColor: ['#10B981', '#F59E0B', '#0284C7', '#EF4444'],
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
                                        labels: { color: textColor, font: { size: 12 } }
                                    }
                                }
                            }
                        });
                    });
                }
            }" x-init="initChart()">
                <canvas id="kehadiranPieChart"></canvas>
            </div>
        </div>

        <!-- Grafik Pie Sekolah Aktif -->
        <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl" wire:ignore>
            <h2 class="text-xs font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase mb-1">Status Anak PKL Aktif per Sekolah</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Sebaran persentase jumlah siswa PKL aktif berdasarkan asal sekolah.</p>
            
            <!-- Menggunakan dataset HTML untuk parsing JSON dengan aman -->
            <div class="relative h-72 flex justify-center items-center" 
                 data-labels="{{ json_encode($chartSekolahLabels ?? []) }}"
                 data-totals="{{ json_encode($chartSekolahTotals ?? []) }}"
                 x-data="{
                initChart() {
                    this.$nextTick(() => {
                        const canvas = document.getElementById('sekolahAktifChart');
                        if (!canvas || typeof Chart === 'undefined') return;

                        const isDark = document.documentElement.classList.contains('dark');
                        const textColor = isDark ? '#94a3b8' : '#475569';
                        const borderColor = isDark ? '#111827' : '#ffffff';

                        const labels = JSON.parse(this.$el.dataset.labels || '[]');
                        const data = JSON.parse(this.$el.dataset.totals || '[]');

                        // Palet warna variatif dinamis untuk potongan pie
                        const colors = [
                            '#6366F1', '#8B5CF6', '#EC4899', '#3B82F6', '#10B981', 
                            '#F59E0B', '#06B6D4', '#84CC16', '#E11D48', '#14B8A6'
                        ];

                        new Chart(canvas.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: data,
                                    backgroundColor: colors.slice(0, labels.length),
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
                                        labels: { color: textColor, font: { size: 12 } }
                                    },
                                    tooltip: {
                                        backgroundColor: isDark ? '#1F2937' : '#ffffff',
                                        titleColor: isDark ? '#ffffff' : '#111827',
                                        bodyColor: isDark ? '#94a3b8' : '#475569',
                                        borderColor: isDark ? '#374151' : '#e5e7eb',
                                        borderWidth: 1
                                    }
                                }
                            }
                        });
                    });
                }
            }" x-init="initChart()">
                <canvas id="sekolahAktifChart"></canvas>
            </div>
        </div>

    </div>

    <!-- SECTION LEADERBOARD KETERLAMBATAN & TOP SEKOLAH AKTIF -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Leaderboard Keterlambatan Bulan Ini -->
        <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">
            <h2 class="text-xs font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase mb-1">Peserta dengan Keterlambatan Terbanyak</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Bulan {{ now()->translatedFormat('F Y') }} — bahan evaluasi mentor.</p>

            @if($leaderboardTerlambat->isEmpty())
                <div class="text-center py-8 text-sm text-gray-400 dark:text-gray-500">
                    Belum ada catatan keterlambatan bulan ini.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($leaderboardTerlambat as $index => $item)
                        <div class="flex items-center gap-4 p-3 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0
                                {{ $index === 0 ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $item->user->nama ?? 'Pengguna tidak ditemukan' }}
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                    {{ $item->total_terlambat }}x Terlambat
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Top 5 Sekolah dengan Peserta Aktif Terbanyak -->
        <div class="p-6 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">
            <h2 class="text-xs font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase mb-1">Sekolah dengan Peserta Aktif Terbanyak</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">Top 5 asal sekolah / kampus peserta PKL yang masih aktif.</p>

            @if($topSekolahAktif->isEmpty())
                <div class="text-center py-8 text-sm text-gray-400 dark:text-gray-500">
                    Belum ada data peserta aktif dengan sekolah terisi.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($topSekolahAktif as $index => $item)
                        <div class="flex items-center gap-4 p-3 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0
                                {{ $index === 0 ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $item->sekolah->nama_sekolah ?? 'Tidak Diketahui' }}
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20">
                                    {{ $item->total }} Peserta
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>