<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'E-Presensi PKL' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/face_detection.js" crossorigin="anonymous"></script>
    
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
<body class="bg-gray-950 text-gray-100 antialiased min-h-screen flex flex-col sm:flex-row">

    <!-- SIDEBAR MOBILE OVERLAY & TOPBAR MOBILE -->
    <div x-data="{ sidebarOpen: false }" 
         x-on:close-mobile-sidebar.window="sidebarOpen = false"
         class="relative z-50 sm:hidden">
        
        <!-- Topbar Mobile (Kiri: Burger Menu | Kanan: Profile) -->
        <div class="bg-gray-900 border-b border-gray-800 px-4 py-3 flex items-center justify-between w-full">
            <!-- Kiri: Burger Menu Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white focus:outline-none p-1.5 rounded-lg hover:bg-gray-800 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Kanan: Profile & Nama User -->
            <a href="{{ route('user.profile') }}" wire:navigate class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-100 truncate max-w-[150px] text-right">
                    {{ auth()->user()?->nama ?? auth()->user()?->name ?? 'Guest' }}
                </span>
                @if (auth()->user()?->foto)
                    <img 
                        src="{{ asset('storage/' . auth()->user()->foto) }}" 
                        alt="Foto Profil" 
                        class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-500/30 shrink-0"
                    >
                @else
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-blue-500/30 shrink-0">
                        {{ auth()->user() ? strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 2)) : '?' }}
                    </div>
                @endif
            </a>
        </div>

        <!-- Mobile Drawer Sidebar -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="sidebarOpen = false"></div>
        <aside x-show="sidebarOpen" x-cloak class="fixed top-0 left-0 w-64 h-full bg-gray-900 border-r border-gray-800 p-5 flex flex-col justify-between transition-all duration-300">
            <div>
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-blue-600/30">
                            P
                        </div>
                        <h2 class="text-base font-bold text-white tracking-wider">E-PRESENSI PKL</h2>
                    </div>
                    <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800">✕</button>
                </div>
                
                <!-- Navigation Links (Mobile) -->
                <nav class="space-y-1.5">
                    <!-- Presensi -->
                    <a href="{{ route('user.presensi') }}" wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.presensi') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Presensi</span>
                    </a>

                    <!-- Riwayat -->
                    <a href="{{ route('user.riwayat') }}" wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.riwayat') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat</span>
                    </a>

                    <!-- Izin / Sakit -->
                    <a href="{{ route('user.izin-sakit') }}" wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.izin-sakit') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Izin / Sakit</span>
                    </a>

                    <!-- Upload File -->
                    <a href="{{ route('user.dokumen') }}" wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.dokumen') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span>Upload File</span>
                    </a>

                    <!-- Project -->
                    @if(Route::has('user.project'))
                    <a href="{{ route('user.project') }}" wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.project') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        <span>Project</span>
                    </a>
                    @endif

                    <!-- Profile -->
                    <a href="{{ route('user.profile') }}" wire:navigate 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.profile') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profile</span>
                    </a>
                </nav>
            </div>

            <!-- Logout Mobile -->
            <div class="border-t border-gray-800 pt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-left text-gray-400 hover:text-red-400 hover:bg-red-950/20 rounded-xl text-sm font-medium transition group">
                        <svg class="w-5 h-5 shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>

    <!-- SIDEBAR DESKTOP -->
    <aside class="hidden sm:flex w-64 bg-gray-900 border-r border-gray-800/80 flex-col justify-between h-screen sticky top-0 flex-shrink-0">
        <div class="p-5 flex-1 flex flex-col">
            <!-- App Brand Header -->
            <div class="flex items-center gap-3 mb-8 px-2">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-600/30">
                    P
                </div>
                <h1 class="text-base font-bold text-white tracking-wider">E-PRESENSI PKL</h1>
            </div>

            <!-- Navigation Links (Desktop) -->
            <nav class="space-y-1.5 flex-1">
                <!-- Presensi -->
                <a href="{{ route('user.presensi') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.presensi') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Presensi</span>
                </a>

                <!-- Riwayat -->
                <a href="{{ route('user.riwayat') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.riwayat') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat</span>
                </a>

                <!-- Izin / Sakit -->
                <a href="{{ route('user.izin-sakit') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.izin-sakit') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Izin / Sakit</span>
                </a>

                <!-- Upload File -->
                <a href="{{ route('user.dokumen') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.dokumen') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <span>Upload File</span>
                </a>

                <!-- Project -->
                @if(Route::has('user.project'))
                <a href="{{ route('user.project') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.project') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <span>Project</span>
                </a>
                @endif

                <!-- Profile -->
                <a href="{{ route('user.profile') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.profile') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Profile</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom Footer Logout -->
        <div class="p-4 border-t border-gray-800/80">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-left text-gray-400 hover:text-red-400 hover:bg-red-950/20 rounded-xl text-sm font-medium transition group">
                    <svg class="w-5 h-5 shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Top Navbar Header (Desktop Only) -->
        <header class="hidden sm:flex bg-gray-900/50 backdrop-blur-md border-b border-gray-800/80 px-6 py-3.5 items-center justify-end sticky top-0 z-40">
            <a href="{{ route('user.profile') }}" wire:navigate class="flex items-center gap-3 text-gray-100 hover:text-white transition">
                <span class="text-sm font-medium text-gray-200">{{ auth()->user()?->nama ?? auth()->user()?->name ?? 'Guest' }}</span>
                @if (auth()->user()?->foto)
                    <img 
                        src="{{ asset('storage/' . auth()->user()->foto) }}" 
                        alt="Foto Profil" 
                        class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-500/30"
                    >
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

    @livewireScripts

    <script>
        // Tutup otomatis sidebar mobile saat navigasi Livewire SPA selesai berpindah halaman
        document.addEventListener('livewire:navigated', () => {
            window.dispatchEvent(new CustomEvent('close-mobile-sidebar'));
        });
    </script>
</body>
</html>