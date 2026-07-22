<div class="w-full">
    <div class="mb-6 border-b pb-4 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detail Project</h2>
    </div>

    @if($project)
        <div class="space-y-5">
            <div>
                <span class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Nama Project</span>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $project->nama_project ?? '-' }}</p>
            </div>

            <div>
                <span class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">Link GitHub</span>
                @if($project->link_github)
                    <a href="{{ $project->link_github }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.387.6.113.82-.26.82-.577 0-.285-.01-1.04-.016-2.04-3.338.725-4.042-1.61-4.042-1.61-.546-1.387-1.333-1.756-1.333-1.756-1.09-.745.083-.73.083-.73 1.205.084 1.84 1.237 1.84 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.775.418-1.305.762-1.605-2.665-.303-5.466-1.332-5.466-5.93 0-1.31.468-2.38 1.235-3.22-.123-.303-.535-1.523.117-3.176 0 0 1.008-.322 3.3 1.23a11.5 11.5 0 013.003-.404c1.02.005 2.047.138 3.003.404 2.29-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.873.12 3.176.77.84 1.233 1.91 1.233 3.22 0 4.61-2.807 5.624-5.48 5.92.43.372.823 1.102.823 2.222 0 1.606-.014 2.898-.014 3.293 0 .32.216.694.825.576C20.565 21.795 24 17.298 24 12c0-6.63-5.373-12-12-12z"/>
                        </svg>
                        {{ $project->link_github }}
                    </a>
                @else
                    <p class="text-sm text-gray-400">Belum ada link GitHub.</p>
                @endif
            </div>

            <div>
                <span class="block mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">File Project</span>
                @if($project->file_project)
                    <a href="{{ route('project.download', $project->project_id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        Download File
                    </a>
                @else
                    <p class="text-sm text-gray-400">Belum ada file yang diupload.</p>
                @endif
            </div>
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 py-6 text-center">Belum ada data project untuk akun ini.</p>
    @endif

    <div class="flex items-center justify-end pt-6 mt-6 border-t dark:border-gray-700">
        <button 
            type="button"
            wire:click="tutup"
            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500"
        >
            Tutup
        </button>
    </div>
</div>