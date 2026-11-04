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
