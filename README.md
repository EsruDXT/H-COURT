# H-COURT — Reservasi Lapangan Sekolah

<p align="left">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS v4">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Pest-5-F28D1A?style=for-the-badge" alt="Pest 5">
  <img src="https://img.shields.io/badge/Figma-F24E1E?style=for-the-badge&logo=figma&logoColor=white" alt="Figma">
</p>

**H-COURT** adalah sistem reservasi lapangan olahraga **sekolah** yang menggantikan buku catatan peminjaman manual di ruang piket. Siswa dan pihak umum dapat melihat jadwal futsal, basket, dan badminton, lalu mengajukan reservasi. Pengelola meninjau, menerima, atau menolak pengajuan dari dashboard.

Projek kelompok mata pelajaran **Pemrograman Lanjutan** (Kelompok 3).

## Masalah yang diselesaikan

| Sebelumnya | Dengan H-COURT |
| :--- | :--- |
| Catatan manual mudah hilang | Semua reservasi tersimpan terpusat di database |
| Jadwal kosong tidak terlihat | Ketersediaan bisa dicek kapan saja lewat halaman jadwal |
| Reservasi sering bentrok | Server menolak slot yang sudah terisi, termasuk saat dua orang mengajukan bersamaan |

## Fitur

**Pengguna (siswa / umum)**
- Registrasi dan login, dengan pilihan peran siswa atau umum.
- Lupa kata sandi lewat tautan reset.
- Jadwal lapangan mingguan per lapangan (Kosong / Menunggu / Terisi).
- Pengajuan reservasi dalam 4 langkah: pilih lapangan, hari, jam dan durasi, lalu data peminjam.
- Riwayat "Reservasi Saya" dan pembatalan reservasi sebelum waktu penggunaan dimulai.
- Notifikasi saat reservasi diterima, ditolak, atau dibatalkan pengelola.

**Pengelola (admin)**
- Dashboard statistik dan daftar reservasi dengan filter status, serta tombol Terima, Tolak, dan Edit.
- Kelola lapangan: tambah, ubah, aktif/nonaktifkan, hapus, dan unggah gambar.
- Jam operasional mingguan per lapangan dan penutupan khusus (libur atau perawatan).
- Laporan statistik berdasarkan rentang tanggal, termasuk ekspor CSV.
- Notifikasi untuk reservasi baru dan pembatalan oleh pengguna.

**Aturan reservasi**
- Slot jam mulai: `13:00`, `14:00`, `15:00`, `16:00`, `17:00`; durasi 1-3 jam; selesai paling lambat `18:00`.
- Reservasi dapat diajukan untuk hari ini sampai 5 hari ke depan.
- Status: `pending` (menunggu), `confirmed` (diterima), `rejected` (ditolak), `cancelled` (dibatalkan).
- Reservasi `pending` dan `confirmed` sama-sama memblokir slot.
- Pengecekan bentrok dilakukan di server di dalam transaksi database.

## Tech Stack

| Kategori | Teknologi |
| :--- | :--- |
| Framework | Laravel 13 |
| Bahasa | PHP 8.3+ |
| Template | Blade (render server-side, tanpa framework JavaScript) |
| CSS | Tailwind CSS v4 via Vite |
| Database | MySQL (sesi, cache, dan antrean memakai driver `database`) |
| Pengujian | Pest 5 |
| Format kode | Laravel Pint |
| Desain | Figma |

Interaksi dibuat sepenuhnya server-side: form GET/POST, query string, redirect, dan utility Tailwind (`peer-checked:`, `hover:`). Tidak memakai Alpine.js atau library JavaScript lain.

## Prasyarat

- PHP 8.3 atau lebih baru, dengan ekstensi `pdo_mysql` dan `pdo_sqlite` (SQLite dipakai untuk pengujian)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) 22 atau lebih baru dan npm
- MySQL (bisa lewat XAMPP / Laragon)

## Instalasi

```bash
git clone https://github.com/EsruDXT/H-COURT.git
cd H-COURT

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Buat database kosong bernama `h_court` di MySQL. Pengaturan bawaan di `.env.example`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=h_court
DB_USERNAME=root
DB_PASSWORD=
```

Lanjutkan dengan migrasi, seeder, dan tautan storage:

```bash
php artisan migrate --seed
php artisan storage:link
```

## Menjalankan

```bash
composer run dev
```

Perintah ini menjalankan server (`http://127.0.0.1:8000`), queue listener, dan Vite sekaligus.

### Akun contoh (dari seeder)

| Peran | Email | Kata sandi |
| :--- | :--- | :--- |
| Pengelola | `admin@hcourt.test` | `password` |
| Siswa | `siswa@hcourt.test` | `password`  |

Akun pengelola hanya dibuat lewat seeder, tidak bisa lewat form registrasi publik. Ganti kata sandi ini sebelum dipakai di luar lingkungan lokal.

### Email reset kata sandi

Secara bawaan `MAIL_MAILER=log`, sehingga email tidak benar-benar terkirim. Tautan reset kata sandi dapat dilihat di `storage/logs/laravel.log`.

## Pengujian

```bash
php artisan test --compact
vendor/bin/pint --dirty --format agent
```

Tes memakai SQLite in-memory, jadi ekstensi `pdo_sqlite` harus aktif di `php.ini`.

## Struktur Singkat

```
app/
  Http/Controllers/        # Auth, Landing, Schedule, Reservation, Notification, PasswordReset
  Http/Controllers/Admin/  # Dashboard, Court, Reservation, OperatingSchedule, Report
  Http/Requests/           # Validasi (StoreReservationRequest, Admin\*)
  Http/Middleware/         # EnsureUserIsAdmin
  Models/                  # User, Court, Reservation, CourtOperatingHour, CourtClosure
  Notifications/           # ReservationActivityNotification
resources/views/           # layouts, landing, schedule, reservation, notifications, auth, admin
database/                  # migrations, factories, seeders
tests/Feature/             # Pest feature tests
```

## Alur Kerja Git

Satu branch per fitur dengan format `namadepan/fitur-singkat` (contoh `marvin/finishing-application`), lalu Pull Request ke `main`. Jalankan `git pull origin main` sebelum mulai bekerja. Format commit: `feat:`, `fix:`, `refactor:`, `test:`, `chore:`.

## Tim Pengembang (Kelompok 3)

| Nama | Peran |
| :--- | :--- |
| **Marvin Arif Pratama** | Team Leader & Full-Stack Developer |
| **Axel Lucius Efendi** | Front-End Developer |
| **Bryan Stevent** | UI/UX Designer |
| **Harry Wang** | UI/UX Designer |

## Lisensi

Projek ini dibuat untuk keperluan pendidikan sebagai tugas mata pelajaran sekolah.
