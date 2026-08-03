<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'SIMPATI (Sistem Informasi Magang dan Presensi dan Aktivitas)') }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Leaflet GIS Maps -->
    <link rel="preconnect" href="https://basemaps.cartocdn.com">
    <link rel="preconnect" href="https://tile.openstreetmap.org">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- MediaPipe Vision (Camera & Face Detection) -->
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/face_detection.js" crossorigin="anonymous"></script>

    <!-- Script Anti-Flicker Theme: jalan SEBELUM body dirender, cegah flash saat first load -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .livewire-progress-bar {
            background-color: #2563eb !important;
            height: 3px !important;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased min-h-screen transition-colors duration-200">

    {{--
        PENTING: x-data dark mode ada di LAYOUT ini (bukan di dalam komponen Livewire).
        Karena bagian ini TIDAK pernah di-morph oleh wire:navigate (yang di-morph cuma
        {{ $slot }} di dalam <main>), Alpine state darkMode tidak akan pernah ke-reset
        atau flicker saat navigasi antar halaman, walaupun di-spam klik.
    --}}
    <div x-data="{
            darkMode: localStorage.getItem('theme') === 'dark'
                || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggleTheme() {
                this.darkMode = !this.darkMode;
                document.documentElement.classList.toggle('dark', this.darkMode);
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            }
         }"
         x-init="document.documentElement.classList.toggle('dark', darkMode)"
         class="min-h-screen">

        <!-- SIDEBAR (HTML biasa, BUKAN <livewire:...>) -->
        <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white dark:bg-slate-900 border-e border-slate-200 dark:border-slate-800/80" aria-label="Sidebar">
            <div class="h-full px-3 py-4 overflow-y-auto bg-white dark:bg-slate-900 flex flex-col justify-between">
                <div>
                    <!-- Brand / Logo -->
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center ps-2.5 mb-5 gap-3">
                        <img src="{{ asset('images/logoEpresensiPKL.png') }}" alt="Logo SIMPATI" class="w-16 h-16 rounded-2xl object-cover">
                        <span class="self-center brand-name whitespace-nowrap">SIMPATI (Sistem Informasi Magang dan Presensi dan Aktivitas)</span>
                    </a>

                    <!-- Navigation Links -->
                    <ul class="space-y-1.5 font-medium">
                        <li>
                            <a href="{{ route('dashboard') }}" wire:navigate
                               class="flex items-center p-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }} group">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white' }}" fill="currentColor" viewBox="0 0 22 21">
                                    <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                                    <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                                </svg>
                                <span class="ms-3 text-sm">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('monitoring-absensi') }}" wire:navigate
                               class="flex items-center p-2.5 rounded-xl transition-all {{ request()->routeIs('monitoring-absensi') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }} group">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('monitoring-absensi') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5H4v10h12V7H6zm2 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="flex-1 ms-3 text-sm whitespace-nowrap">Melihat Absensi</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('manajemen-pkl') }}" wire:navigate
                               class="flex items-center p-2.5 rounded-xl transition-all {{ request()->routeIs('manajemen-pkl') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }} group">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('manajemen-pkl') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-1.815-4.236 4 4 0 015.815 3.236v3h-4zM5.815 10.764A5.972 5.972 0 004 15v3H0v-3a4 4 0 015.815-3.236z"/>
                                </svg>
                                <span class="flex-1 ms-3 text-sm whitespace-nowrap">Manajemen Anak PKL</span>
                            </a>
                        </li>

                        <!-- Upload File Dropdown -->
                        <li x-data="{ open: {{ request()->routeIs('sertifikat', 'nilai', 'surat-penerimaan-magang') ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open"
                                    class="flex items-center w-full p-2.5 text-slate-600 dark:text-slate-400 transition duration-75 rounded-xl group hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white {{ request()->routeIs('sertifikat', 'nilai', 'surat-penerimaan-magang') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800/50' : '' }}">
                                <svg class="w-5 h-5 shrink-0 text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white {{ request()->routeIs('sertifikat', 'nilai', 'surat-penerimaan-magang') ? 'text-slate-900 dark:text-white' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm8 3.5a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5zm2.22.72a.75.75 0 10-1.06-1.06L9.5 10.81 7.84 9.16a.75.75 0 00-1.06 1.06l2.2 2.2a.75.75 0 001.06 0l2.2-2.2z"/>
                                </svg>
                                <span class="flex-1 ms-3 text-sm text-left rtl:text-right whitespace-nowrap">Upload File</span>
                                <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>
                            <ul x-show="open" x-cloak class="py-2 space-y-1">
                                <li><a href="{{ route('sertifikat') }}" wire:navigate class="flex items-center w-full p-2 text-sm rounded-lg pl-11 group {{ request()->routeIs('sertifikat') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }}">Sertifikat</a></li>
                                <li><a href="{{ route('nilai') }}" wire:navigate class="flex items-center w-full p-2 text-sm rounded-lg pl-11 group {{ request()->routeIs('nilai') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }}">Nilai</a></li>
                                <li><a href="{{ route('surat-penerimaan-magang') }}" wire:navigate class="flex items-center w-full p-2 text-sm rounded-lg pl-11 group {{ request()->routeIs('surat-penerimaan-magang') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }}">Surat Penerimaan Magang</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('manajemen-akun') }}" wire:navigate
                               class="flex items-center p-2.5 rounded-xl transition-all {{ request()->routeIs('manajemen-akun') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }} group">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('manajemen-akun') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 10a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                                <span class="flex-1 ms-3 text-sm whitespace-nowrap">Manajemen Akun</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('rekap-absensi') }}" wire:navigate
                               class="flex items-center p-2.5 rounded-xl transition-all {{ request()->routeIs('rekap-absensi') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }} group">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('rekap-absensi') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 10a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                                <span class="flex-1 ms-3 text-sm whitespace-nowrap">Rekap Absensi</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('permohonan-izin') }}" wire:navigate
                               class="flex items-center p-2.5 rounded-xl transition-all {{ request()->routeIs('permohonan-izin') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }} group">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('permohonan-izin') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <span class="flex-1 ms-3 text-sm whitespace-nowrap">Permohonan Izin/Sakit</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Log Out Button -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800/80">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center p-2.5 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/30 group transition">
                            <svg class="w-5 h-5 shrink-0 group-hover:translate-x-0.5 transition" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h7a1 1 0 100-2H4V5h6a1 1 0 100-2H3zm8.707 3.293a1 1 0 00-1.414 1.414L12.586 10l-2.293 2.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414l-3-3z" clip-rule="evenodd"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left whitespace-nowrap text-sm font-medium">Log Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- AREA KONTEN UTAMA -->
        <div class="sm:ml-64 min-h-screen transition-colors duration-200">

            <!-- HEADER / NAVBAR (HTML biasa, ikut layout, bukan livewire component) -->
            <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md flex justify-between items-center w-full h-16 px-6 border-b border-slate-200 dark:border-slate-800/80 sticky top-0 z-30 transition-colors duration-200">
                <div>
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button"
                            class="inline-flex items-center p-2 text-sm text-slate-500 dark:text-slate-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-700 sm:hidden">
                        <span class="sr-only">Toggle sidebar</span>
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Toggle tema: pakai toggleTheme() dari x-data layout, bukan dari komponen livewire -->
                    <button @click="toggleTheme()" type="button"
                            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                            title="Ubah Mode Tampilan">
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                        {{ auth()->user()->nama ?? auth()->user()->name ?? 'Guest' }}
                    </span>

                    @if(auth()->user()?->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto Profil"
                             class="w-9 h-9 rounded-full object-cover ring-2 ring-blue-500/30">
                    @else
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center tracking-wider shadow-md shadow-blue-600/30 ring-2 ring-blue-500/30">
                            {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'RU', 0, 2)) }}
                        </div>
                    @endif
                </div>
            </header>

            {{--
                Cuma bagian INI yang di-manage Livewire / kena morph saat wire:navigate.
                Sidebar, header, dan x-data darkMode di atas semuanya di luar jangkauan
                morph, jadi tidak pernah ke-reset -> tidak ada celah flicker.
            --}}
            <main class="p-6 min-h-[calc(100vh-4rem)]">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts

    <!-- Re-init Flowbite (dropdown dsb) setelah navigasi Livewire -->
    <script>
        document.addEventListener('livewire:navigated', () => {
            if (typeof initFlowbite === 'function') {
                initFlowbite();
            }
        });
    </script>
</body>
</html>