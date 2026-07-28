<div class="w-full mx-auto max-w-4xl">
    <div class="mb-6 border-b border-gray-800 pb-4">
        <h1 class="text-2xl font-bold text-white tracking-wide">Upload File</h1>
        <p class="text-sm text-gray-400 mt-1">Unggah file ZIP/RAR atau tambahkan link GitHub. Riwayat upload Anda ditampilkan di bawah.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 rounded-xl bg-green-900/40 border border-green-600/60 text-green-200 text-sm shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-900/40 border border-red-600/60 text-red-200 text-sm shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-gray-800 border border-gray-700/60 rounded-3xl p-6 shadow-lg">
        <form wire:submit.prevent="submitDocument" class="space-y-6">
            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-200">Nama File</label>
                <input
                    type="text"
                    wire:model="nama"
                    placeholder="Contoh: Proposal Kerja"
                    class="w-full rounded-2xl border border-gray-700 bg-gray-900 text-white text-sm px-4 py-3 focus:border-blue-500 focus:outline-none"
                />
                @error('nama') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-200">Upload File (ZIP/RAR)</label>
                <input
                    type="file"
                    wire:model="fileProject"
                    class="w-full text-sm text-gray-100 file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-full file:border-0 file:shadow-sm"
                    accept=".zip,.rar"
                />
                @error('fileProject') <span class="text-red-400 text-xs block mt-2">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-2">Maksimum 50MB. Hanya ZIP/RAR yang diperbolehkan.</p>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    type="submit"
                    class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-500/20"
                >
                    Upload File
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-gray-800 border border-gray-700/60 rounded-3xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold text-white">Riwayat Upload</h2>
                <p class="text-sm text-gray-400">Daftar file pribadi yang sudah Anda simpan.</p>
            </div>
        </div>

        @if($uploadedFiles->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-700 p-6 text-center text-gray-400">
                Belum ada file atau link yang diunggah.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-gray-200">
                    <thead class="border-b border-gray-700 text-xs uppercase tracking-wide text-gray-400">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Nama File</th>
                            <th class="px-4 py-3">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uploadedFiles as $index => $item)
                            <tr class="border-b border-gray-700 hover:bg-white/5">
                                <td class="px-4 py-3 text-gray-300">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-200">{{ $item->nama_project ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($item->file_project)
                                        <a href="{{ route('project.download', ['project' => $item->project_id]) }}" class="text-blue-400 hover:text-blue-300">Download</a>
                                    @else
                                        <span class="text-gray-500">Tidak ada</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
