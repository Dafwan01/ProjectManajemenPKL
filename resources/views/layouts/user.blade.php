<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SIMPATI (Sistem Informasi Magang dan Presensi dan Aktivitas)' }}</title>

 
   <script src="{{ asset('vendor/mediapipe/camera_utils.js') }}"></script>
<script src="{{ asset('vendor/mediapipe/face_detection.js') }}"></script>
    <!-- Script Anti-Flicker Theme (Inisialisasi Tema Sesuai OS / LocalStorage) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Scripts & Styles (Vite / Tailwind) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }

        /* Custom Indikator Progress Bar SPA saat Pindah Halaman */
        .livewire-progress-bar {
            background-color: #2563eb !important; /* Blue-600 */
            height: 3px !important;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased min-h-screen flex flex-col sm:flex-row transition-colors duration-200">
@php
    $userLogin = auth()->user();
    $adaPerubahanJadwal = false;
    $adaNotifBerkas = false;

    if ($userLogin) {
        // Cek Perubahan Jadwal
        $updateTerakhirJadwal = \App\Models\DetailJadwal::where('user_id', $userLogin->user_id)
            ->max('updated_at');

        if ($updateTerakhirJadwal) {
            $updateTerakhirJadwal = \Carbon\Carbon::parse($updateTerakhirJadwal);
            $adaPerubahanJadwal = is_null($userLogin->jadwal_dilihat_at)
                || $updateTerakhirJadwal->gt($userLogin->jadwal_dilihat_at);
        }

        // Cek Notifikasi Berkas, Nilai & Sertifikat Unread
        $adaNotifBerkas = $userLogin->unreadNotifications()
            ->whereIn('data->title', [
                'Berkas Baru Diunggah', 
                'Surat Penerimaan Magang', 
                'Nilai', 
                'Nilai Baru', 
                'Pembaruan Nilai',
                'Sertifikat' // <-- Tambahkan ini
            ])
            ->exists();
    }
@endphp
    <!-- WRAPPER ALPINE UNTUK GLOBAL THEME TOGGLE -->
    <div x-data="{
            darkMode: document.documentElement.classList.contains('dark'),
            toggleTheme() {
                this.darkMode = !this.darkMode;
                document.documentElement.classList.toggle('dark', this.darkMode);
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            }
         }"
         x-init="
            document.addEventListener('livewire:navigated', () => {
                const savedTheme = localStorage.getItem('theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                darkMode = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);
                document.documentElement.classList.toggle('dark', darkMode);
            });
         "
         class="contents">

        <!-- SIDEBAR MOBILE OVERLAY & TOPBAR MOBILE -->
        <div x-data="{ sidebarOpen: false }"
             x-on:close-mobile-sidebar.window="sidebarOpen = false"
             class="relative z-50 sm:hidden">

            <!-- Topbar Mobile -->
            <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 py-3 flex items-center justify-between w-full transition-colors">
                <!-- Kiri: Burger Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Kanan: Toggle Theme & Profile -->
                <div class="flex items-center gap-2">
                    <!-- Toggle Dark/Light Mode Mobile -->
                    <button @click="toggleTheme()" type="button" class="p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition" title="Ubah Mode Tampilan">
                        <!-- Icon Matahari (Tampil saat Dark Mode) -->
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <!-- Icon Bulan (Tampil saat Light Mode) -->
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>

                    <a href="{{ route('user.profile') }}" wire:navigate class="flex items-center gap-2.5">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate max-w-[120px] text-right">
                            {{ auth()->user()?->nama ?? auth()->user()?->name ?? 'Guest' }}
                        </span>
                        @if (auth()->user()?->foto)
                            <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto Profil" class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-500/30 shrink-0">
                        @else
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-blue-500/30 shrink-0">
                                {{ auth()->user() ? strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 2)) : '?' }}
                            </div>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Mobile Drawer Sidebar -->
            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="sidebarOpen = false"></div>
            <aside x-show="sidebarOpen" x-cloak class="fixed top-0 left-0 w-64 h-full bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 p-5 flex flex-col justify-between transition-all duration-300">
                <div>
                    <div class="flex justify-between items-center mb-8">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/logoEpresensiPKL.png') }}" alt="Logo SIMPATI" class="w-14 h-14 rounded-2xl object-cover">
                            <h2 class="brand-name text-[5rem] font-extrabold leading-tight">SIMPATI</h2>
                        </div>
                        <button @click="sidebarOpen = false" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">✕</button>
                    </div>

                    <!-- Navigation Links (Mobile) -->
                    <nav class="space-y-1.5">
                        <a href="{{ route('user.presensi') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.presensi') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Presensi</span>
                        </a>
                        <a href="{{ route('user.riwayat') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.riwayat') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Riwayat</span>
                        </a>
                        <a href="{{ route('user.izin-sakit') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.izin-sakit') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Izin / Sakit</span>
                        </a>
                        <a href="{{ route('user.dokumen') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.dokumen') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span>Berkas</span>
                            @if($adaNotifBerkas)
        <span class="ms-auto flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white text-[11px] font-bold shrink-0 animate-pulse" title="Ada berkas baru">
            !
        </span>
    @endif
                        </a>
                        @if(Route::has('user.project'))
                        <a href="{{ route('user.project') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.project') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            <span>Project</span>
                        </a>
                        @endif

                        <!-- Jadwal Magang (Mobile) -->
                        <a href="{{ route('jadwal') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('jadwal') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="flex-1">Jadwal Magang</span>
                            @if($adaPerubahanJadwal)
                                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white text-[11px] font-bold shrink-0 animate-pulse" title="Ada perubahan jadwal">
                                    !
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('forum') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('forum') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-.64-.025-1.289-.06-1.938-.105-.286-.02-.572-.045-.858-.073A9.012 9.012 0 0 1 3 10.5M16.5 3a9 9 0 0 0-9 9c0 .762.09 1.503.26 2.213" />
                            </svg>
                            <span>Forum</span>
                        </a>
                        <a href="{{ route('user.profile') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.profile') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Profile</span>
                        </a>
                    </nav>
                </div>

                <!-- Logout Mobile -->
                <div class="border-t border-gray-200 dark:border-gray-800 pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-left text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-xl text-sm font-medium transition group">
                            <svg class="w-5 h-5 shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        <!-- SIDEBAR DESKTOP -->
        <aside class="hidden sm:flex w-72 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex-col justify-between h-screen sticky top-0 flex-shrink-0 transition-colors duration-200">
            <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-center gap-3 mb-8 px-2">
                    <img src="{{ asset('images/logoEpresensiPKL.png') }}" alt="Logo SIMPATI" class="w-16 h-16 rounded-2xl object-cover">
                    <h1 class="brand-name text-[8rem] font-extrabold leading-tight">SIMPATI</h1>
                </div>

                <!-- Navigation Links (Desktop) -->
                <nav class="space-y-1.5 flex-1">
                    <a href="{{ route('user.presensi') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.presensi') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Presensi</span>
                    </a>
                    <a href="{{ route('user.riwayat') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.riwayat') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat</span>
                    </a>
                    <a href="{{ route('user.izin-sakit') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.izin-sakit') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Izin / Sakit</span>
                    </a>
                    <a href="{{ route('user.dokumen') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.dokumen') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span>Berkas</span>
            @if($adaNotifBerkas)
        <span class="ms-auto flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white text-[11px] font-bold shrink-0 animate-pulse" title="Ada berkas baru">
            !
        </span>
    @endif
                    </a>
                    @if(Route::has('user.project'))
                    <a href="{{ route('user.project') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.project') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        <span>Project</span>
                    </a>
                    @endif

                    <!-- Jadwal Magang (Desktop) -->
                    <a href="{{ route('jadwal') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('jadwal') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="flex-1">Jadwal Magang</span>
                        @if($adaPerubahanJadwal)
                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white text-[11px] font-bold shrink-0 animate-pulse" title="Ada perubahan jadwal">
                                !
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('forum') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('forum') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-.64-.025-1.289-.06-1.938-.105-.286-.02-.572-.045-.858-.073A9.012 9.012 0 0 1 3 10.5M16.5 3a9 9 0 0 0-9 9c0 .762.09 1.503.26 2.213" />
                        </svg>
                        <span>Forum</span>
                    </a>
                    <a href="{{ route('user.profile') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl text-sm font-medium transition min-h-[46px] {{ request()->routeIs('user.profile') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profile</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Bottom Footer Logout -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-left text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-2xl text-sm font-medium transition group min-h-[50px]">
                        <svg class="w-5 h-5 shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- Top Navbar Header (Desktop Only) -->
            <header class="hidden sm:flex bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-6 py-3.5 items-center justify-end gap-4 sticky top-0 z-40 transition-colors duration-200">

                <!-- Toggle Dark/Light Mode Desktop -->
                <button @click="toggleTheme()" type="button" class="p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition" title="Ubah Mode Tampilan">
                    <!-- Icon Matahari (Tampil saat Dark Mode) -->
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <!-- Icon Bulan (Tampil saat Light Mode) -->
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>

                <a href="{{ route('user.profile') }}" wire:navigate class="flex items-center gap-3 text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-white transition">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ auth()->user()?->nama ?? auth()->user()?->name ?? 'Guest' }}</span>
                    @if (auth()->user()?->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto Profil" class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-500/30">
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-blue-500/30">
                            {{ auth()->user() ? strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 2)) : '?' }}
                        </div>
                    @endif
                </a>
            </header>

            <!-- Main Slot Livewire View Page -->
            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                {{ $slot }}
            </main>
        </div>

    </div>

    @livewireScripts

    <script>
        // Tutup otomatis sidebar mobile saat navigasi Livewire SPA selesai berpindah halaman
        document.addEventListener('livewire:navigated', () => {
            window.dispatchEvent(new CustomEvent('close-mobile-sidebar'));
        });
    </script>
</body>
</html>