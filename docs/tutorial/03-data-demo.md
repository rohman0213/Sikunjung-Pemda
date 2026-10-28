# 03 — Data Demo SI Kunjung Pemda

## 1. Tujuan

Data demo digunakan untuk membantu pengujian dan demonstrasi fungsi aplikasi tanpa harus memasukkan seluruh data secara manual.

## 2. Seeder yang Digunakan

`DatabaseSeeder` memanggil:

1. `AdminSeeder`
2. `DemoSeeder`

`AdminSeeder` menyiapkan akun administrator. `DemoSeeder` menyiapkan data karyawan, pemohon, pengaturan jadwal, kegiatan, dan kunjungan.

## 3. Data yang Disiapkan

Berdasarkan implementasi `DemoSeeder.php`, data contoh mencakup:

| Jenis data | Jumlah yang direncanakan |
|---|---:|
| Karyawan | 6 |
| Karyawan dengan akun login | 4 |
| Pemohon | 2 |
| Pengaturan jadwal | 84 entri untuk 14 hari |
| Kegiatan | 12 |
| Kunjungan | 20 |

Jumlah pengaturan jadwal merupakan jumlah entri yang direncanakan untuk enam karyawan selama 14 hari. Jumlah akhir data dalam database dapat berbeda jika data sebelumnya sudah ada atau mengalami perubahan.

## 4. Akun Demo

Akun yang didefinisikan oleh seeder menggunakan pola password demo `password`.

Akun administrator:

- Email: `admin@sikunjung.test`

Akun karyawan:

- `rina@sikunjung.test`
- `budi@sikunjung.test`
- `sari@sikunjung.test`
- `maya@sikunjung.test`

Akun pemohon:

- `dedi@sikunjung.test`
- `putri@sikunjung.test`

Akun demo hanya untuk lingkungan pengembangan lokal. Jangan menggunakan password demo pada lingkungan produksi.

## 5. Jadwal Kunjungan

Pengaturan jadwal dibuat untuk 14 hari mulai tanggal eksekusi seeder.

- Jam aktif dimulai pukul 08.00.
- Jam nonaktif ditetapkan pukul 14.00.
- Akhir pekan ditandai sebagai nonaktif.

## 6. Kegiatan dan Kunjungan

Data kegiatan mencakup tanggal lampau dan periode yang mencakup tanggal saat ini.

Data kunjungan mencakup tanggal relatif terhadap waktu eksekusi dan beberapa status, yaitu `menunggu`, `disetujui`, serta `ditolak`.

## 7. Perhatian

Seeder menggunakan `updateOrCreate()`. Menjalankannya kembali dapat memperbarui data dengan kunci yang sama.

Sebelum menjalankan `php artisan db:seed`, periksa dampak seeder terhadap database yang digunakan. Hindari menjalankannya pada database produksi tanpa prosedur yang sesuai.

Perintah yang digunakan pada lingkungan pengembangan:

```bash
php artisan db:seed
```
