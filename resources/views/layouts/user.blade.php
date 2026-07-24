<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'E-Presensi PKL - User' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-900 text-white font-sans antialiased">

    <!-- Header Top Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-gray-800 border-b border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <span class="self-center text-xl font-bold sm:text-2xl whitespace-nowrap text-white pl-2">
                        E-PRESENSI PKL
                    </span>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3 gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-sm">
                            JO
                        </div>
                        <span class="text-sm font-medium text-white">Jonathan</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Khusus User -->
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-gray-800 border-r border-gray-700 sm:translate-x-0" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-gray-800 flex flex-col justify-between">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="/user/presensi" class="flex items-center p-2.5 text-white rounded-lg bg-blue-600 hover:bg-blue-700 group">
                        <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
                        </svg>
                        <span class="ms-3">Presensi</span>
                    </a>
                </li>
            </ul>

            <ul class="space-y-2 font-medium pt-4 border-t border-gray-700">
                <li>
                    <a href="#" class="flex items-center p-2.5 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
                        </svg>
                        <span class="ms-3">Log Out</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Content Area Tempat Halaman Presensi Tampil -->
    <main class="p-4 sm:ml-64 pt-20 min-h-screen bg-gray-900">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>