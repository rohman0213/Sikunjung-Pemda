# 01 — Instalasi SI Kunjung Pemda

## 1. Tujuan

Dokumen ini menjelaskan persiapan lingkungan dan langkah instalasi aplikasi SI Kunjung Pemda pada komputer lokal.

## 2. Persyaratan

Siapkan perangkat lunak berikut:

- Laragon.
- PHP sesuai persyaratan versi Laravel yang digunakan.
- Composer.
- MySQL atau MariaDB.
- Git jika proyek diambil dari repositori.

## 3. Menyiapkan Proyek

Buka terminal pada direktori proyek:

```powershell
cd C:\laragon\www\sikunjungPemda
```

Pasang dependensi PHP:

```powershell
composer install
```

## 4. Menyiapkan Konfigurasi

Salin file konfigurasi contoh jika file `.env` belum tersedia:

```powershell
Copy-Item .env.example .env
```

**Jangan menimpa `.env` yang sudah ada.** File tersebut dapat berisi konfigurasi database dan application key yang sedang digunakan.

Buat application key hanya jika diperlukan, misalnya pada instalasi baru:

```powershell
php artisan key:generate
```

Sesuaikan konfigurasi koneksi database pada `.env` dengan database lokal.

## 5. Menyiapkan Database

Pastikan database yang dikonfigurasi pada `.env` sudah tersedia.

Untuk instalasi baru, jalankan migrasi:

```powershell
php artisan migrate
```

Jika ingin menggunakan data demo, periksa isi dan dampak seeder sebelum menjalankan:

```powershell
php artisan db:seed
```

Jangan menjalankan migrasi ulang atau seeder secara sembarangan pada database yang sudah berisi data penting.

## 6. Menjalankan Aplikasi

Jalankan aplikasi melalui terminal:

```powershell
php artisan serve
```

Buka alamat lokal yang ditampilkan di terminal. Jika menggunakan virtual host Laragon, gunakan alamat yang telah dikonfigurasi di Laragon.

## 7. Menjalankan Pengujian

Untuk memeriksa fungsi aplikasi:

```powershell
php artisan test
```

Pastikan seluruh pengujian selesai tanpa kegagalan.

## 8. Pemecahan Masalah

- **Koneksi database gagal:** periksa nama database, host, port, username, dan password pada `.env`.
- **Dependensi tidak tersedia:** jalankan `composer install`.
- **Application key belum tersedia:** pada instalasi baru, jalankan `php artisan key:generate`.
- **Pengujian gagal:** baca pesan kegagalan dan perbaiki penyebabnya sebelum mengulangi pengujian.

## 9. Keamanan

Jangan memasukkan file `.env`, password, atau application key ke repositori publik. Gunakan `.env.example` untuk membagikan konfigurasi contoh tanpa rahasia.
