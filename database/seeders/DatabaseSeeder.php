<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\District;
use App\Models\Rating;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Master Wilayah (districts) sesuai kolom rovix_db: name, city_code
        $jagakarsa = District::firstOrCreate(
            ['city_code' => 'JKS-01'],
            ['name' => 'Kecamatan Jagakarsa']
        );

        $pasarMinggu = District::firstOrCreate(
            ['city_code' => 'JKS-02'],
            ['name' => 'Kecamatan Pasar Minggu']
        );

        $pancoran = District::firstOrCreate(
            ['city_code' => 'JKS-03'],
            ['name' => 'Kecamatan Pancoran']
        );

        // 2. Seed Master Kategori Kerusakan (report_categories) sesuai kolom rovix_db: category_name, severity_level
        $katLubang = ReportCategory::firstOrCreate(
            ['category_name' => 'Jalan Berlubang Dalam (Pothole)'],
            ['severity_level' => 5]
        );

        $katAmblas = ReportCategory::firstOrCreate(
            ['category_name' => 'Permukaan Jalan Amblas / Bergelombang'],
            ['severity_level' => 4]
        );

        $katRetak = ReportCategory::firstOrCreate(
            ['category_name' => 'Retak Buaya & Trotoar Rusak'],
            ['severity_level' => 3]
        );

        // 3. Seed Pengguna Multi-Role RBAC (users) sesuai kolom rovix_db: district_id, name, email, phone_number, password, role
        $admin = User::updateOrCreate(
            ['email' => 'admin@rovix.id'],
            [
                'name' => 'Mazaya Nur Angelina (Admin Dinas PU)',
                'phone_number' => '081210204240',
                'role' => 'admin',
                'district_id' => $jagakarsa->id,
                'password' => Hash::make('password'),
            ]
        );

        $petugas = User::updateOrCreate(
            ['email' => 'petugas@rovix.id'],
            [
                'name' => 'Budi Santoso (Petugas Regu Teknis 01)',
                'phone_number' => '081299887766',
                'role' => 'petugas',
                'district_id' => $jagakarsa->id,
                'password' => Hash::make('password'),
            ]
        );

        $warga = User::updateOrCreate(
            ['email' => 'warga@rovix.id'],
            [
                'name' => 'Rizky Pratama (Warga Pelapor)',
                'phone_number' => '081355443322',
                'role' => 'warga',
                'district_id' => $jagakarsa->id,
                'password' => Hash::make('password'),
            ]
        );

        // Akun Anggota Kelompok C
        $members = [
            ['name' => 'Anggoro Cipta Widodo (Dodo)', 'email' => 'dodo@rovix.id', 'role' => 'admin'],
            ['name' => 'Hafiz Fadilah (Hafis)', 'email' => 'hafis@rovix.id', 'role' => 'admin'],
            ['name' => 'Nazwa Atipa Ridwani (Tipa)', 'email' => 'tipa@rovix.id', 'role' => 'admin'],
            ['name' => 'Guntur Khalil Gibran (Guntur)', 'email' => 'guntur@rovix.id', 'role' => 'admin'],
            ['name' => 'Faliha Ersa Fathiana (Ersa)', 'email' => 'ersa@rovix.id', 'role' => 'petugas'],
            ['name' => 'Naswa Nur Aqilah (Naswa)', 'email' => 'naswa@rovix.id', 'role' => 'warga'],
        ];

        foreach ($members as $m) {
            User::updateOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'phone_number' => '081200001111',
                    'role' => $m['role'],
                    'district_id' => $pasarMinggu->id,
                    'password' => Hash::make('password'),
                ]
            );
        }

        // 4. Seed Contoh Laporan Aduan (reports) sesuai kolom rovix_db
        $rep1 = Report::updateOrCreate(
            ['ticket_code' => 'RVX-2026-0001'],
            [
                'user_id' => $warga->id,
                'district_id' => $jagakarsa->id,
                'category_id' => $katLubang->id,
                'description' => 'Jalan Berlubang Lebar di Depan Stasiun Tanjung Barat — Lubang cukup dalam di lajur kiri arah Depok, sering tergenang air saat hujan.',
                'photo_url' => 'reports/sample-pothole-1.jpg',
                'latitude' => -6.30720000,
                'longitude' => 106.83980000,
                'ai_validation' => true,
                'ai_confidence' => 98.40,
                'status' => 'in_progress',
            ]
        );

        $rep2 = Report::updateOrCreate(
            ['ticket_code' => 'RVX-2026-0002'],
            [
                'user_id' => $warga->id,
                'district_id' => $pasarMinggu->id,
                'category_id' => $katAmblas->id,
                'description' => 'Aspal Amblas di Tikungan Jl. Raya Ragunan No. 14 — Permukaan aspal turun sekitar 8 cm sepanjang 2 meter.',
                'photo_url' => 'reports/sample-subsidence-2.jpg',
                'latitude' => -6.28950000,
                'longitude' => 106.84020000,
                'ai_validation' => true,
                'ai_confidence' => 94.10,
                'status' => 'resolved',
            ]
        );

        $rep3 = Report::updateOrCreate(
            ['ticket_code' => 'RVX-2026-0003'],
            [
                'user_id' => $warga->id,
                'district_id' => $pancoran->id,
                'category_id' => $katRetak->id,
                'description' => 'Retak Permukaan Jalan & Trotoar Rusak di Jl. Raya Pasar Minggu KM 7 Kalibata.',
                'photo_url' => 'reports/sample-cracks-3.jpg',
                'latitude' => -6.25910000,
                'longitude' => 106.85110000,
                'ai_validation' => true,
                'ai_confidence' => 91.50,
                'status' => 'submitted',
            ]
        );

        // 5. Seed Contoh SPK Digital (work_orders) sesuai kolom rovix_db
        WorkOrder::updateOrCreate(
            ['spk_number' => 'SPK-PU-2026-001'],
            [
                'report_id' => $rep1->id,
                'officer_id' => $petugas->id,
                'target_sla' => now()->addDays(2)->toDateString(),
                'work_status' => 'in_progress',
                'repair_notes' => 'Segera lakukan penambalan cold-mix aspal karena jalur padat komuter.',
            ]
        );

        WorkOrder::updateOrCreate(
            ['spk_number' => 'SPK-PU-2026-002'],
            [
                'report_id' => $rep2->id,
                'officer_id' => $petugas->id,
                'target_sla' => now()->subDay()->toDateString(),
                'work_status' => 'completed',
                'after_photo_url' => 'work_orders/sample-repaired-2.jpg',
                'repair_notes' => 'Perbaikan struktur dasar dan pengaspalan ulang menggunakan Hotmix Asphalt 400 Kg.',
                'resolved_at' => now(),
            ]
        );

        // 6. Seed Rating & Audit Log sesuai kolom rovix_db
        Rating::updateOrCreate(
            ['report_id' => $rep2->id],
            [
                'user_id' => $warga->id,
                'score' => 5,
                'review_text' => 'Respons petugas sangat cepat, jalan sekarang sudah mulus kembali. Terima kasih Dinas PU!',
            ]
        );

        if (AuditLog::where('report_id', $rep1->id)->count() === 0) {
            AuditLog::create([
                'report_id' => $rep1->id,
                'user_id' => $admin->id,
                'action_name' => 'VERIFIED_AND_DISPATCHED',
                'previous_status' => 'submitted',
                'new_status' => 'in_progress',
            ]);
        }
    }
}
