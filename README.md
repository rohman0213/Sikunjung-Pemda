# SI Kunjung Pemda

Sistem Informasi Kunjungan Pemerintah Daerah merupakan aplikasi berbasis web untuk mengelola permohonan kunjungan, jadwal kunjungan, data karyawan, kegiatan, serta proses pelayanan kunjungan.

## Fitur Aplikasi

- Halaman beranda publik.
- Informasi jadwal kunjungan.
- Informasi struktur karyawan.
- Autentikasi pengguna.
- Panel administrator untuk mengelola data karyawan, kegiatan, dan pengaturan jadwal kunjungan.
- Panel karyawan untuk mengelola proses kunjungan.
- Panel pemohon untuk mengajukan dan memantau kunjungan.
- Pengujian otomatis menggunakan PHPUnit.

## Teknologi

- PHP
- Laravel
- MySQL
- Blade
- PHPUnit

## Persyaratan

Pastikan perangkat telah memiliki:

- Laragon atau lingkungan pengembangan PHP yang setara.
- PHP dengan ekstensi yang dibutuhkan Laravel.
- Composer.
- MySQL atau MariaDB.
- Node.js dan npm jika diperlukan untuk membangun aset frontend.

## Instalasi

1. Clone atau salin proyek ke direktori kerja.
2. Buka terminal pada direktori proyek.
3. Pasang dependensi PHP:

   ```bash
   composer install
   ```

4. Salin konfigurasi lingkungan:

   ```bash
   copy .env.example .env
   ```

   Pada Linux atau macOS, gunakan `cp .env.example .env`.

5. Sesuaikan konfigurasi database pada `.env` dengan database lokal.
6. Buat application key:

   ```bash
   php artisan key:generate
   ```

7. Jalankan migrasi database:

   ```bash
   php artisan migrate
   ```

8. Jika ingin mengisi data demo, periksa konfigurasi seeder terlebih dahulu, kemudian jalankan:

   ```bash
   php artisan db:seed
   ```

9. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

10. Buka alamat lokal yang ditampilkan oleh Laravel atau alamat virtual host yang dikonfigurasi di Laragon.

## Pengujian Otomatis

Jalankan seluruh pengujian dengan perintah:

```bash
php artisan test
```

Untuk menjalankan pengujian tertentu:

```bash
php artisan test --filter=LandingTest
```

## Dokumentasi

Dokumentasi tutorial instalasi, konfigurasi, penggunaan aplikasi, dan pengujian akan disediakan di direktori `docs/tutorial/`.

## Catatan

- Sesuaikan nama database dengan konfigurasi lokal pada `.env`.
- Jangan membagikan file `.env` karena dapat berisi konfigurasi sensitif.
- Jangan menjalankan `php artisan migrate:fresh` pada database yang berisi data penting karena perintah tersebut menghapus tabel sebelum migrasi ulang.
