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
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <!-- Scripts & Styles (Vite / Tailwind) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 antialiased min-h-screen flex flex-col sm:flex-row">

    <!-- SIDEBAR MOBILE OVERLAY -->
    <div x-data="{ sidebarOpen: false }" class="relative z-50 sm:hidden">
        
        <!-- Topbar Mobile -->
        <div class="bg-gray-900 border-b border-gray-800 p-4 flex items-center justify-between w-full">
            <h1 class="text-lg font-bold text-white tracking-wider">E-PRESENSI PKL</h1>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Drawer Sidebar -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="sidebarOpen = false"></div>
        <aside x-show="sidebarOpen" x-cloak class="fixed top-0 left-0 w-64 h-full bg-gray-900 border-r border-gray-800 p-5 flex flex-col justify-between transition-all duration-300">
            <div>
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-bold text-white tracking-wider">E-PRESENSI PKL</h2>
                    <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white">✕</button>
                </div>
                
                <!-- Navigation Links (Mobile) -->
                <nav class="space-y-2">
                    <a href="{{ route('user.presensi') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.presensi') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Presensi</span>
                    </a>

                    <a href="{{ route('user.riwayat') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.riwayat') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat</span>
                    </a>

                    <a href="{{ route('user.izin-sakit') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.izin-sakit') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Izin / Sakit</span>
                    </a>
                </nav>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <button class="flex items-center gap-3 px-4 py-3 w-full text-left text-gray-400 hover:text-red-400 hover:bg-gray-800/50 rounded-xl text-sm font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Log Out</span>
                </button>
            </div>
        </aside>
    </div>

    <!-- SIDEBAR DESKTOP -->
    <aside class="hidden sm:flex w-64 bg-gray-900 border-r border-gray-800/80 flex-col justify-between h-screen sticky top-0 flex-shrink-0">
        <div class="p-5">
            <!-- App Brand Header -->
            <div class="flex items-center gap-3 mb-8 px-2">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-600/30">
                    P
                </div>
                <h1 class="text-base font-bold text-white tracking-wider">E-PRESENSI PKL</h1>
            </div>

            <!-- Navigation Links (Desktop) -->
            <nav class="space-y-1.5">
                <a href="{{ route('user.presensi') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.presensi') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Presensi</span>
                </a>

                <a href="{{ route('user.riwayat') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.riwayat') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat</span>
                </a>

                <a href="{{ route('user.izin-sakit') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('user.izin-sakit') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-white hover:bg-gray-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Izin / Sakit</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="p-4 border-t border-gray-800/80">
            <button class="flex items-center gap-3 px-4 py-3 w-full text-left text-gray-400 hover:text-red-400 hover:bg-red-950/20 rounded-xl text-sm font-medium transition group">
                <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span>Log Out</span>
            </button>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Top Navbar Header -->
        <header class="bg-gray-900/50 backdrop-blur-md border-b border-gray-800/80 px-6 py-3.5 flex items-center justify-end sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-blue-500/30">
                    JO
                </div>
                <span class="text-sm font-medium text-gray-200">Jonathan</span>
            </div>
        </header>

        <!-- Main Slot Livewire View Page -->
        <main class="p-6 lg:p-8 flex-1">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>