<div class="w-full">
    <!-- Header Modal -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Detail Project</h2>
    </div>

    @if($project)
        <div class="space-y-5">
            <!-- Nama Project -->
            <div>
                <span class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Nama Project
                </span>
                <div class="p-3.5 bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700/80 rounded-2xl">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $project->nama_project ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Link GitHub -->
            <div>
                <span class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    Link Repository GitHub
                </span>
                @if($project->link_github)
                    <a href="{{ $project->link_github }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-3 p-3.5 bg-gray-50 dark:bg-gray-800/60 hover:bg-gray-100 dark:hover:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl transition group">
                        <div class="p-2 bg-gray-200 dark:bg-gray-700 rounded-xl group-hover:bg-gray-300 dark:group-hover:bg-gray-600 transition shrink-0">
                            <svg class="w-5 h-5 text-gray-800 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.387.6.113.82-.26.82-.577 0-.285-.01-1.04-.016-2.04-3.338.725-4.042-1.61-4.042-1.61-.546-1.387-1.333-1.756-1.333-1.756-1.09-.745.083-.73.083-.73 1.205.084 1.84 1.237 1.84 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.775.418-1.305.762-1.605-2.665-.303-5.466-1.332-5.466-5.93 0-1.31.468-2.38 1.235-3.22-.123-.303-.535-1.523.117-3.176 0 0 1.008-.322 3.3 1.23a11.5 11.5 0 013.003-.404c1.02.005 2.047.138 3.003.404 2.29-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.873.12 3.176.77.84 1.233 1.91 1.233 3.22 0 4.61-2.807 5.624-5.48 5.92.43.372.823 1.102.823 2.222 0 1.606-.014 2.898-.014 3.293 0 .32.216.694.825.576C20.565 21.795 24 17.298 24 12c0-6.63-5.373-12-12-12z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:underline break-all">
                            {{ $project->link_github }}
                        </span>
                    </a>
                @else
                    <div class="p-3.5 bg-gray-50 dark:bg-gray-800/40 border border-dashed border-gray-300 dark:border-gray-700/60 rounded-2xl">
                        <p class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada link GitHub yang disematkan.</p>
                    </div>
                @endif
            </div>

            <!-- File Project -->
            <div>
                <span class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                    File Lampiran / Source Code
                </span>
                @if($project->file_project)
                    <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700/80 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate max-w-[200px] sm:max-w-xs">
                                {{ basename($project->file_project) }}
                            </span>
                        </div>

                        <a href="{{ route('project.download', $project->project_id) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md shadow-blue-600/20 transition shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                            </svg>
                            Download
                        </a>
                    </div>
                @else
                    <div class="p-3.5 bg-gray-50 dark:bg-gray-800/40 border border-dashed border-gray-300 dark:border-gray-700/60 rounded-2xl">
                        <p class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada file yang diunggah.</p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="py-12 text-center">
            <div class="inline-flex p-3 bg-gray-100 dark:bg-gray-800 rounded-full mb-3 text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Belum ada data project untuk akun ini.</p>
        </div>
    @endif

    <!-- Footer Action -->
    <div class="flex items-center justify-end pt-5 mt-6 border-t border-gray-200 dark:border-gray-800">
        <button 
            type="button"
            wire:click="tutup"
            class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800/60 hover:bg-gray-200 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700/50 rounded-2xl transition"
        >
            Tutup
        </button>
    </div>
</div>