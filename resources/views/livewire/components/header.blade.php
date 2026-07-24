<div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">

  <!-- SIDEBAR (Permanen di desktop, otomatis sembunyi di mobile) -->
  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-gray-50 dark:bg-gray-800 border-e border-gray-200 dark:border-gray-700" aria-label="Sidebar">
     <div class="h-full px-3 py-4 overflow-y-auto">
        <a href="https://v3.flowbite.com/" class="flex items-center ps-2.5 mb-5">
           <img src="https://flowbite.com/images/logo.svg" class="h-6 me-3 sm:h-7" alt="Flowbite Logo" />
           <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">Flowbite</span>
        </a>
        <ul class="space-y-2 font-medium">
           <!-- 1. Dashboard -->
           <li>
              <a href="#" wire:click="GoToDashboard" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                 <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                    <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                    <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                 </svg>
                 <span class="ms-3">Dashboard</span>
              </a>
           </li>
           <!-- 2. Melihat Absensi -->
           <li>
              <a href="#" wire:click="GoToMonitoringAbsensi" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                 <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5H4v10h12V7H6zm2 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                 </svg>
                 <span class="flex-1 ms-3 whitespace-nowrap">Melihat Absensi</span>
              </a>
           </li>
           <!-- 3. Manajemen Anak PKL -->
           <li>
              <a href="#" wire:click="GoToMamnajemenPkl" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                 <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-1.815-4.236 4 4 0 015.815 3.236v3h-4zM5.815 10.764A5.972 5.972 0 004 15v3H0v-3a4 4 0 015.815-3.236z"/>
                 </svg>
                 <span class="flex-1 ms-3 whitespace-nowrap">Manajemen Anak PKL</span>
              </a>
           </li>
          
           <!-- 4. Upload File -->
           <li>
            <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                  <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm8 3.5a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5zm2.22.72a.75.75 0 10-1.06-1.06L9.5 10.81 7.84 9.16a.75.75 0 00-1.06 1.06l2.2 2.2a.75.75 0 001.06 0l2.2-2.2z"/>
                 </svg>
                  <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Upload File</span>
                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                  </svg>
            </button>
            <ul id="dropdown-example" class="hidden py-2 space-y-2">
                  <li>
                     <a href="#" wire:click="GoToSertifikat" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Sertifikat</a>
                  </li>
                  <li>
                     <a href="#" wire:click="GoToNilai" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Nilai</a>
                  </li>
                  <li>
                     <a href="#" wire:click="GoToSuratPenerimaanMagang" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Surat Penerimaan Magang</a>
                  </li>
            </ul>
         </li>
           <!-- 5. Manajemen Akun -->
           <li>
              <a href="#" wire:click="GoToManajemenAkun" wire:navigate class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                 <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 10a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                 </svg>
                 <span class="flex-1 ms-3 whitespace-nowrap">Manajemen Akun</span>
              </a>
           </li>

           <li>
              <a href="#" wire:click="GoToPermohonanIzin" wire:navigate class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                 <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 10a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                 </svg>
                 <span class="flex-1 ms-3 whitespace-nowrap">Permohonan Izin/Sakit</span>
              </a>
           </li>
           <!-- 6. Log Out -->
           <li>
              <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                 <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h7a1 1 0 100-2H4V5h6a1 1 0 100-2H3zm8.707 3.293a1 1 0 00-1.414 1.414L12.586 10l-2.293 2.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414l-3-3z" clip-rule="evenodd"/>
                 </svg>
                 <span class="flex-1 ms-3 whitespace-nowrap">Log Out</span>
              </a>
           </li>
        </ul>
     </div>
  </aside>

  <!-- AREA KONTEN (Otomatis bergeser di desktop berkat sm:ml-64) -->
  <div class="sm:ml-64">
     
     <!-- HEADER / NAVBAR -->
     <header class="bg-[#F6F6F6] dark:bg-gray-800 flex justify-between items-center w-full h-14 px-4 border-b border-gray-200 dark:border-gray-700">
         
         <!-- Tombol Burger Bawaan Flowbite (sm:hidden agar otomatis hilang di desktop) -->
         <div>
             <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 sm:hidden">
                <span class="sr-only">Toggle sidebar</span>
                <i class="fa-solid fa-bars text-lg"></i>
             </button>
         </div>
       
         <!-- Profil User -->
         <div class="flex items-center gap-2 h-full">
             <img src="/images/profile-placeholder.png" alt="Logo" class="w-7 h-7 rounded-full object-cover">
             <p class="text-sm font-medium">John Doe</p>
         </div>
     </header>

     <!-- ISI KONTEN UTAMA -->
     <main class="p-4">
         {{ $slot ?? '' }}
     </main>
     
  </div>

</div>