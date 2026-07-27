@if(session()->has('riwayat_presensi'))
    <ul>
        @foreach(session('riwayat_presensi') as $item)
            <li>
                <p>Nama: {{ $item['nama'] }}</p>
                <!-- Menampilkan gambar berdasarkan path yang disimpan -->
                <img src="{{ asset('storage/' . $item['foto']) }}" alt="Foto Presensi" width="150">
            </li>
        @endforeach
    </ul>
@endif