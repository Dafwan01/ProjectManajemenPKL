<div>
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">

    <!-- Card 1: Peserta PKL -->
    <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-100">
        <div>
            <p class="text-xs font-medium text-gray-500 tracking-wide">Peserta PKL</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $totalPeserta }}</p>
        </div>
        <div>
            <img src="/images/profile-placeholder.png" alt="Icon Peserta" class="w-12 h-12 object-contain">
        </div>
    </div>

    <!-- Card 2: Hadir Hari Ini -->
    <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-100">
        <div>
            <p class="text-xs font-medium text-gray-500 tracking-wide">Hadir Hari Ini</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $hadirHariIni }}</p>
        </div>
        <div>
            <img src="/images/profile-placeholder.png" alt="Icon Hadir" class="w-12 h-12 object-contain">
        </div>
    </div>

    <!-- Card 3: Terlambat Hari Ini -->
    <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-100">
        <div>
            <p class="text-xs font-medium text-gray-500 tracking-wide">Terlambat Hari Ini</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $terlambatHariIni }}</p>
        </div>
        <div>
            <img src="/images/profile-placeholder.png" alt="Icon Terlambat" class="w-12 h-12 object-contain">
        </div>
    </div>

    <!-- Card 4: Izin / Sakit -->
    <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-100">
        <div>
            <p class="text-xs font-medium text-gray-500 tracking-wide">Izin / Sakit</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $izinSakitHariIni }}</p>
        </div>
        <div>
            <img src="/images/profile-placeholder.png" alt="Icon Izin" class="w-12 h-12 object-contain">
        </div>
    </div>

   <!-- Card 5: WFH -->
<div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-100">
    <div>
        <p class="text-xs font-medium text-gray-500 tracking-wide">WFH</p>
        <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $wfhHariIni }}</p>
    </div>
    <div>
        <img src="/images/profile-placeholder.png" alt="Icon WFH" class="w-12 h-12 object-contain">
    </div>
</div>

    <!-- Card 6: WFO -->
    <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-100">
        <div>
            <p class="text-xs font-medium text-gray-500 tracking-wide">WFO</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $wfoHariIni }}</p>
        </div>
        <div>
            <img src="/images/profile-placeholder.png" alt="Icon WFO" class="w-12 h-12 object-contain">
        </div>
    </div>
</div>
<div>       
    <h1>Grafik</h1>
</div>
</div>