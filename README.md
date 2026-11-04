# ROVIX — Road Vigilance & Index (Kelompok C)

Sistem Informasi Pelaporan & Tindak Lanjut Kerusakan Jalan Berbasis Web Monolitik (**Laravel**), **Role-Based Access Control (RBAC)**, **Web GIS (Leaflet.js)**, dan **Validasi Citra AI Vision** dalam mendukung **SDG 11 (Kota dan Pemukiman yang Berkelanjutan)**.

---

## 👥 Pembagian Modul & PBI Tim (Kelompok C)

| Nama Anggota | Peran | Modul & Entitas Utama | Kode PBI (Sprint 1 & 2) |
| :--- | :--- | :--- | :--- |
| **Mazaya Nur Angelina (Angel)** | Project Manager | Base Project, Dashboard Multi-Role, Users & RBAC (`users`) | `SCRUM-106`, `SCRUM-110` |
| **Anggoro Cipta Widodo (Dodo)** | Developer | Pelaporan Warga, GPS Geotagging, & AI Triage (`reports`) | `SCRUM-6`, `SCRUM-7`, `SCRUM-54`, `SCRUM-58` |
| **Hafiz Fadilah (Hafis)** | Developer | Verifikasi Admin, Peta GIS Leaflet, & Audit Log (`reports`, `audit_logs`) | `SCRUM-8`, `SCRUM-9`, `SCRUM-62`, `SCRUM-66` |
| **Nazwa Atipa Ridwani (Tipa)** | Developer | Master Kategori Kerusakan Jalan (`report_categories`) | `SCRUM-22`, `SCRUM-26`, `SCRUM-70`, `SCRUM-74` |
| **Guntur Khalil Gibran (Guntur)** | Developer | Master Wilayah & Zonasi Kecamatan (`districts`) | `SCRUM-30`, `SCRUM-34`, `SCRUM-78`, `SCRUM-82` |
| **Faliha Ersa Fathiana (Ersa)** | Developer | Disposisi SPK Digital & Field View Petugas (`work_orders`) | `SCRUM-38`, `SCRUM-42`, `SCRUM-86`, `SCRUM-90` |
| **Naswa Nur Aqilah (Naswa)** | Developer | Evaluasi & Rating Kepuasan Warga (`ratings`) | `SCRUM-46`, `SCRUM-50`, `SCRUM-94`, `SCRUM-98` |

---

## ⚙️ Cara Instalasi bagi Anggota Tim

1. Clone repository ini ke folder `D:\laragon\www\rovix`:
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
3. Pastikan database **`rovix_db`** sudah aktif di MySQL Laragon, lalu jalankan migrasi & seeder:
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

---

## 🛡️ Fitur RBAC & Manajemen Pengguna (`SCRUM-106` & `SCRUM-110`)
- **Middleware Multi-Role (`role:admin`, `role:petugas`, `role:warga`):** Mengarahkan setiap pengguna secara otomatis ke halaman sesuai hak aksesnya.
- **CRUD Pengguna & Penugasan Zona Wilayah:** Admin Dinas dapat menambah, melihat daftar, memfilter berdasarkan peran/kecamatan, melihat detail aktivitas, mengubah, dan menghapus akun pengguna.

