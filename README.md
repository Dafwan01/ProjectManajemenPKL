<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Panduan Fitur
# 🚀 SIMPATI — Sistem Informasi Magang & Presensi Terintegrasi

**SIMPATI** adalah platform berbasis web untuk mengelola dan memantau kegiatan peserta Praktik Kerja Lapangan (PKL) / Magang secara terpadu, mulai dari presensi berbasis GPS, pengelolaan administrasi berkas, penilaian kinerja multiaspek, hingga penerbitan sertifikat digital secara otomatis.

---

## ✨ Fitur-Fitur Utama

### 📊 1. Dashboard & Analitik Real-Time
* **Statistik Harian:** Ringkasan jumlah peserta Hadir, Izin, Sakit, Alpa, serta sebaran lokasi kerja WFO vs WFH.
* **Analitik & Tren:** Visualisasi grafik kehadiran 30 hari terakhir, rata-rata jam masuk, dan *leaderboard* keterlambatan.
* **Quote Motivasi:** Tampilan kutipan inspiratif harian otomatis untuk peserta.

### 📍 2. Monitoring Presensi & Mapping GPS
* **Pelacakan GPS (Leaflet JS):** Pemetaan titik koordinat lokasi check-in peserta pada peta interaktif.
* **Koreksi Presensi:** Edit manual jam masuk, jam keluar, serta logbook kegiatan harian peserta jika terjadi kendala.
* **Filter Bimbingan:** Penyaringan data kehadiran khusus untuk peserta bimbingan mentor tertentu.

### 📝 3. Manajemen Izin & Absen Susulan
* **Verifikasi Permohonan:** Pengolahan pengajuan surat Izin, Sakit, dan Absen Susulan dengan validasi bentrok jadwal.
* **Proteksi Sistem:** Validasi ketat yang mencegah persetujuan absen pulang jika belum ada data absen masuk.
* **Auto Update Logbook:** Otomatis memperbarui status kegiatan harian peserta saat izin disetujui.

### 📈 4. Rekapitulasi Presensi & Cetak PDF
* **Perekapan Kehadiran:** Rekapitulasi statistik kehadiran bulanan dan tahunan per peserta.
* **Pratinjau & Cetak Laporan:** Modal pratinjau detail presensi dan ekspor laporan PDF (DomPDF) berstandar cetak A4.

### 💼 5. Manajemen PKL, Jadwal & Proyek
* **Data Peserta:** Pengelolaan profil peserta (sekolah, jurusan, keahlian, dan periode magang).
* **Jadwal & Tugas:** Pembuatan jadwal harian/mingguan dan penugasan proyek kerja peserta.

### 📑 6. Pengelolaan Berkas Administrasi
* **Surat Penerimaan Magang:** Unggah berkas resmi penerimaan magang (PDF maks. 5MB) dengan urutan prioritas peserta yang belum mengunggah berada di paling atas.
* **Notifikasi Berkas:** Pemberitahuan otomatis ke akun peserta saat dokumen berhasil diunggah.

### 💯 7. Penilaian Kinerja Multiaspek
* **Evaluasi 5 Aspek:** Penilaian Kedisiplinan, Kemampuan Teknis, Problem Solving, Komunikasi/Kerjasama, dan Kualitas Hasil.
* **Kalkulasi Predikat:** Perhitungan otomatis nilai rata-rata dan konversi predikat (Sangat Baik, Baik, Cukup, dll.).
* **Cetak Transkrip PDF:** Ekspor lembar transkrip nilai resmi berformat PDF lengkap dengan info Divisi & Bidang.

### 🎓 8. Penerbitan Sertifikat & Auto-Lulus
* **Sertifikat Digital:** Generasi otomatis sertifikat PDF via `CertificateService` (dukungan TTD Elektronik & Non-Elektronik).
* **Otomatisasi Status:** Mengubah status peserta dari **Aktif** menjadi **Lulus** secara otomatis saat sertifikat diterbitkan.

### 📜 9. Audit Log Aktivitas
* **Audit Trail (Spatie Activitylog):** Pencatatan seluruh riwayat aktivitas dan perubahan data dalam sistem untuk transparansi operasional.

### 👤 10. Manajemen Akun & Profil
* **Multi-Role & Auto Schedule:** Pengelolaan akun (Admin, Mentor, PKL) dengan pembuat jadwal default otomatis (Senin–Jumat).
* **Profil Mandiri:** Pembaruan data diri, ubah kata sandi, dan foto profil.

### 📱 11. Antarmuka Seluler
* **Bottom Navigation:** Navigation bar bagian bawah yang responsif untuk kenyamanan penggunaan dari smartphone.

---

## 🔐 Matriks Hak Akses (RBAC)

| Fitur / Modul | Admin | Mentor | Peserta PKL |
| :--- | :---: | :---: | :---: |
| **Dashboard & Analitik** | ✅ (Semua) | ✅ (Bimbingan) | ❌ |
| **Monitoring Presensi & GPS** | ✅ (Semua) | ✅ (Bimbingan) | ❌ |
| **Persetujuan Izin / Sakit** | ✅ | ✅ | ❌ (Mengajukan) |
| **Input Penilaian Kinerja** | ✅ | ✅ (Bimbingan) | ❌ (Melihat) |
| **Terbitkan Sertifikat Digital** | ✅ | ✅ (Bimbingan) | ❌ (Unduh) |
| **Unggah Surat Penerimaan** | ✅ | ✅ | ❌ (Unduh) |
| **Manajemen Akun & Audit Log** | ✅ | ❌ | ❌ |
| **Kelola Profil Mandiri** | ✅ | ✅ | ✅ |

---

## 🛠️ Arsitektur Teknis

* **Core Framework:** Laravel (Livewire v3)
* **UI & Interaktivitas:** Tailwind CSS & Leaflet.js (Peta GPS)
* **PDF Engine:** `barryvdh/laravel-dompdf`
* **Audit Trail:** `spatie/laravel-activitylog`
* **Locale Date:** Carbon ID (Bahasa Indonesia)
