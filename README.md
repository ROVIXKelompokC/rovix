# ROVIX — Road Vigilance & Index

Sistem Informasi Pelaporan & Tindak Lanjut Kerusakan Jalan Berbasis Web Monolitik (**Laravel**), **Role-Based Access Control (RBAC)**, **Web GIS (Leaflet.js)**, dan **Validasi Citra AI Vision** dalam mendukung **SDG 11 (Kota dan Pemukiman yang Berkelanjutan)**.

---

## 🚀 Fitur Utama Sistem

1. **Portal Warga Pelapor (`role: warga`)**
   - Pelaporan kerusakan jalan disertai penguncian titik koordinat GPS (Geotagging) dan validasi citra berbasis AI.
   - Pelacakan linimasa status laporan secara transparan (*Submitted → Verified → In Progress → Resolved*).
   - Penilaian kepuasan bintang 1–5 dan ulasan hasil perbaikan jalan.
2. **Field View Petugas Lapangan (`role: petugas`)**
   - Daftar penugasan Surat Perintah Kerja (SPK) aktif beserta batas waktu penyelesaian (Target SLA).
   - Integrasi navigasi rute GPS menuju lokasi titik kerusakan jalan.
   - Pembaruan status pengerjaan lapangan dan unggah bukti foto hasil perbaikan (*After*).
3. **Management Dashboard Admin Dinas PU (`role: admin`)**
   - Pemantauan metrik operasional dan peta sebaran titik kerusakan jalan interaktif (**Leaflet.js**).
   - Verifikasi laporan masuk, deteksi potensi laporan duplikat, dan pencatatan **Audit Log**.
   - Penerbitan disposisi SPK digital kepada regu teknis lapangan.
   - Pengelolaan Master Data (**Kategori Kerusakan**, **Wilayah Kecamatan**, serta **Pengguna & Hak Akses RBAC**).

---

## ⚙️ Cara Instalasi & Konfigurasi

1. Clone repository ini ke folder kerja Laragon (`D:\laragon\www\rovix`):
   ```bash
   git clone https://github.com/ROVIXKelompokC/rovix.git
   cd rovix
   ```
2. Install dependensi Composer & salin konfigurasi `.env`:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
3. Pastikan database MySQL **`rovix_db`** sudah aktif di Laragon, lalu jalankan migrasi & seeder:
   ```bash
   php artisan migrate --seed
   ```
4. Jalankan server aplikasi:
   ```bash
   php artisan serve
   ```
5. Akun Demo Multi-Role RBAC (Kata sandi: `password`):
   - **Admin Dinas PU:** `admin@rovix.id`
   - **Petugas Lapangan:** `petugas@rovix.id`
   - **Warga Pelapor:** `warga@rovix.id`
