# ROVIX — Road Vigilance & Index

Sistem Informasi Pelaporan dan Tindak Lanjut Kerusakan Jalan Berbasis Web (**Laravel 12**), **Role-Based Access Control (RBAC)**, **Web GIS (Leaflet.js)**, dan **Validasi Citra AI Vision** dalam mendukung **SDG 11 (Kota dan Pemukiman yang Berkelanjutan)**.

---

## Ruang Lingkup Modul Sistem

1. **Portal Warga Pelapor (`role: warga`)**
   - Pengajuan laporan kerusakan jalan disertai penguncian titik koordinat GPS (*Geotagging*) dan validasi citra berbasis AI.
   - Pemantauan status laporan secara transparan (*Submitted → Verified → In Progress → Resolved*).
   - Pemberian rating bintang 1–5 dan ulasan hasil perbaikan jalan.
2. **Panel Kerja Petugas Lapangan (`role: petugas`)**
   - Daftar penugasan Surat Perintah Kerja (SPK) aktif beserta batas waktu penyelesaian (*Target SLA*).
   - Pembaruan status kerja SPK lapangan, pencatatan perbaikan, unggah bukti foto selesai (*After*), serta arsip riwayat pengerjaan.
3. **Dashboard Admin Dinas PU (`role: admin`)**
   - Pemantauan metrik laporan, peta sebaran titik kerusakan jalan interaktif (**Leaflet.js**), dan distribusi beban kerja petugas otomatis.
   - Verifikasi laporan masuk, deteksi potensi laporan duplikat (**Haversine**), dan pencatatan **Audit Log**.
   - Pengelolaan disposisi SPK digital serta Master Data (**Kategori Kerusakan**, **Wilayah Kecamatan**, dan **Pengguna & RBAC**).

---

## Panduan Instalasi Lokal

1. Clone repositori ke direktori kerja Laragon (`D:\laragon\www\rovix`):
   ```bash
   git clone https://github.com/ROVIXKelompokC/rovix.git
   cd rovix
   ```
2. Pasang dependensi Composer dan salin konfigurasi `.env`:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
3. Pastikan database MySQL **`rovix_db`** telah aktif di Laragon, kemudian jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```
4. Jalankan server pengembangan lokal:
   ```bash
   php artisan serve
   ```

---

## Setup setelah Clone

Bagian ini berlaku untuk anggota yang melakukan clone baru maupun yang sudah punya folder proyek dan ingin menarik versi terbaru.

1. Tarik kode terbaru dari repository (atau clone untuk pengguna baru):
   ```bash
   git pull origin main
   ```
   Bagi pengguna baru, clone dulu: `git clone https://github.com/ROVIXKelompokC/rovix.git` lalu `cd rovix`.
2. Pasang dependensi Composer:
   ```bash
   composer install
   ```
   (Khusus clone baru: `cp .env.example .env` lalu `php artisan key:generate`, dan pastikan file `.env` menunjuk ke database MySQL `rovix_db` di Laragon.)
3. Buat symlink storage (**wajib**, karena folder `/public/storage` tidak ikut di git). Tanpa langkah ini seluruh gambar laporan & bukti perbaikan akan tampil broken (404):
   ```bash
   php artisan storage:link
   ```
4. Siapkan database — **pilih salah satu**:

   **Opsi A — Data contoh (seed):**
   ```bash
   php artisan migrate:fresh --seed
   ```
   Akan membuat 3 laporan sample + 1 SPK selesai beserta seluruh fotonya. Catatan: 3 laporan hasil upload warga (RVX-2026-0004 s.d. RVX-2026-0006) tidak tersedia pada opsi ini.

   **Opsi B — Data sama persis seperti milik Project Manager (import dump):**
   ```bat
   cmd /c "D:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe -h 127.0.0.1 -u root rovix_db < database\rovix_db.sql"
   ```
   Atau lewat phpMyAdmin: pilih database `rovix_db` → tab **Import** → pilih file `database/rovix_db.sql` → Go.
   Opsi ini menampilkan **semua 6 laporan + SPK-002 beserta fotonya** persis seperti di komputer PM.
5. Jalankan server pengembangan dan buka aplikasinya:
   ```bash
   php artisan serve
   ```

**Catatan gambar:** seluruh foto (laporan warga dan bukti foto perbaikan petugas) sudah di-commit ke repository di folder `storage/app/public/reports/` dan `storage/app/public/work_orders/`, sehingga setelah langkah 3–4 selesai, semua gambar otomatis tampil tanpa konfigurasi tambahan. Foto yang diupload langsung dari laptop anggota tersimpan hanya di laptop tersebut dan tidak otomatis ikut ke repository (folder storage di-gitignore), kecuali di-commit manual.
