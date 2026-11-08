<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\District;
use App\Models\Rating;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Sprint1IntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_sprint_1_end_to_end_across_all_7_modules(): void
    {
        Storage::fake('public');

        // Seed initial data
        $this->seed();

        $admin = User::where('email', 'admin@rovix.id')->firstOrFail();
        $petugas = User::where('email', 'petugas@rovix.id')->firstOrFail();
        $warga = User::where('email', 'warga@rovix.id')->firstOrFail();

        // =====================================================================
        // 1. ANGEL (SCRUM-106 & SCRUM-110): Manajemen Pengguna & RBAC
        // =====================================================================
        $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => 'petugas']))
            ->assertOk()
            ->assertSee('Manajemen Pengguna');

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Petugas Baru Sprint 1',
                'email' => 'petugas.baru@rovix.id',
                'phone_number' => '081234567890',
                'role' => 'petugas',
                'district_id' => $admin->district_id,
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $newOfficer = User::where('email', 'petugas.baru@rovix.id')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.users.show', $newOfficer))
            ->assertOk()
            ->assertSee('Petugas Baru Sprint 1');

        // =====================================================================
        // 2. GUNTUR (SCRUM-30 & SCRUM-34): Master Wilayah & Zonasi Kecamatan
        // =====================================================================
        $this->actingAs($admin)
            ->get(route('admin.districts.index'))
            ->assertOk()
            ->assertSee('Kecamatan Jagakarsa');

        $this->actingAs($admin)
            ->get(route('admin.districts.create'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.districts.store'), [
                'city_code' => 'jks-04',
                'name' => 'Kecamatan Tebet',
            ])
            ->assertRedirect(route('admin.districts.index'));

        $newDistrict = District::where('city_code', 'JKS-04')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.districts.show', $newDistrict->id))
            ->assertOk()
            ->assertSee('Kecamatan Tebet');

        // =====================================================================
        // 3. TIPA (SCRUM-22 & SCRUM-26): Master Kategori Kerusakan Jalan
        // =====================================================================
        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.categories.create'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'category_name' => 'Bahu Jalan Longsor',
                'severity_level' => 4,
            ])
            ->assertRedirect(route('admin.categories.index'));

        $newCategory = ReportCategory::where('category_name', 'Bahu Jalan Longsor')->firstOrFail();

        // =====================================================================
        // 4. DODO (SCRUM-6 & SCRUM-7): Pengajuan & Riwayat Laporan Warga
        // =====================================================================
        $this->actingAs($warga)
            ->get(route('warga.dashboard'))
            ->assertOk();

        $this->actingAs($warga)
            ->get(route('warga.reports.create'))
            ->assertOk();

        $photo = UploadedFile::fake()->image('jalan_berlubang_tebet.jpg', 800, 600);

        $this->actingAs($warga)
            ->post(route('warga.reports.store'), [
                'category_id' => $newCategory->id,
                'district_id' => $newDistrict->id,
                'description' => 'Jalan berlubang cukup dalam di dekat Stasiun Tebet arah Kampung Melayu.',
                'latitude' => -6.22610000,
                'longitude' => 106.85830000,
                'photo' => $photo,
            ])
            ->assertRedirect();

        $createdReport = Report::where('district_id', $newDistrict->id)->firstOrFail();
        $this->assertSame('submitted', $createdReport->status);

        $this->actingAs($warga)
            ->get(route('warga.reports.index'))
            ->assertOk()
            ->assertSee($createdReport->ticket_code);

        $this->actingAs($warga)
            ->get(route('warga.reports.show', $createdReport->id))
            ->assertOk()
            ->assertSee($createdReport->ticket_code);

        // =====================================================================
        // 5. HAFIZ (SCRUM-8 & SCRUM-9): Verifikasi Laporan, Peta GIS, & Audit Log
        // =====================================================================
        $this->actingAs($admin)
            ->get(route('admin.verification.index'))
            ->assertOk()
            ->assertSee($createdReport->ticket_code);

        $this->actingAs($admin)
            ->get(route('admin.verification.show', $createdReport->id))
            ->assertOk()
            ->assertSee($createdReport->ticket_code);

        // Approve report -> status becomes 'verified' & AuditLog recorded
        $this->actingAs($admin)
            ->post(route('admin.verification.approve', $createdReport->id), [
                'notes' => 'Foto valid dan koordinat sesuai.',
            ])
            ->assertRedirect();

        $this->assertSame('verified', $createdReport->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'report_id' => $createdReport->id,
            'previous_status' => 'submitted',
            'new_status' => 'verified',
        ]);

        // Visiting Admin Dashboard must NOT swallow verified reports before manual SPK creation
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
        $this->assertSame('verified', $createdReport->fresh()->status);

        // Test Reject on another submitted report (seeded RVX-2026-0003)
        $submittedSeeded = Report::where('ticket_code', 'RVX-2026-0003')->firstOrFail();
        $this->actingAs($admin)
            ->post(route('admin.verification.reject', $submittedSeeded->id), [
                'reason' => 'Duplikat laporan warga.',
            ])
            ->assertRedirect();
        $this->assertSame('rejected', $submittedSeeded->fresh()->status);

        // =====================================================================
        // 6. ERSA (SCRUM-38 & SCRUM-42): Disposisi SPK Digital & Field View Petugas
        // =====================================================================
        $this->actingAs($admin)
            ->get(route('admin.work-orders.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.work-orders.create'))
            ->assertOk()
            ->assertSee($createdReport->ticket_code);

        $this->actingAs($admin)
            ->post(route('admin.work-orders.store'), [
                'spk_number' => 'SPK-PU-2026-099',
                'report_id' => $createdReport->id,
                'officer_id' => $petugas->id,
                'target_sla' => now()->addDays(3)->toDateString(),
                'repair_notes' => 'Lakukan penambalan menggunakan hotmix.',
            ])
            ->assertRedirect(route('admin.work-orders.index'));

        $workOrder = WorkOrder::where('spk_number', 'SPK-PU-2026-099')->firstOrFail();
        $this->assertSame('in_progress', $createdReport->fresh()->status);

        $this->actingAs($admin)
            ->get(route('admin.work-orders.show', $workOrder))
            ->assertOk()
            ->assertSee('SPK-PU-2026-099');

        // Petugas Field View
        $this->actingAs($petugas)
            ->get(route('petugas.dashboard'))
            ->assertOk()
            ->assertSee('SPK-PU-2026-099');

        $this->actingAs($petugas)
            ->get(route('petugas.history'))
            ->assertOk()
            ->assertSee('SPK-PU-2026-002');

        $this->actingAs($petugas)
            ->get(route('petugas.work-orders.show', $workOrder))
            ->assertOk()
            ->assertSee('SPK-PU-2026-099');

        // =====================================================================
        // 7. NASWA (SCRUM-46 & SCRUM-50): Evaluasi & Rating Kepuasan Warga
        // =====================================================================
        // Simulasikan laporan selesai diperbaiki (resolved) agar siap dinilai warga
        $createdReport->update(['status' => 'resolved']);

        $this->actingAs($warga)
            ->get(route('warga.ratings.index'))
            ->assertOk()
            ->assertSee($createdReport->ticket_code);

        $this->actingAs($warga)
            ->get(route('ratings.create', $createdReport->id))
            ->assertOk()
            ->assertSee($createdReport->ticket_code);

        $this->actingAs($warga)
            ->post(route('ratings.store'), [
                'report_id' => $createdReport->id,
                'score' => 5,
                'review_text' => 'Jalan di dekat Stasiun Tebet sudah kembali mulus.',
            ])
            ->assertRedirect(route('warga.ratings.index'));

        $this->assertDatabaseHas('ratings', [
            'report_id' => $createdReport->id,
            'score' => 5,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.ratings.index'))
            ->assertOk()
            ->assertSee('Jalan di dekat Stasiun Tebet sudah kembali mulus.');

        // Pastikan halaman detail laporan warga yang sudah dinilai (memiliki rating) tampil tanpa error
        $this->actingAs($warga)
            ->get(route('warga.reports.show', $createdReport->id))
            ->assertOk()
            ->assertSee('Jalan di dekat Stasiun Tebet sudah kembali mulus.');

        // Pastikan tidak bisa memberikan rating ganda pada laporan yang sama
        $this->actingAs($warga)
            ->get(route('ratings.create', $createdReport->id))
            ->assertRedirect(route('warga.ratings.index'));

        $this->actingAs($warga)
            ->post(route('ratings.store'), [
                'report_id' => $createdReport->id,
                'score' => 4,
                'review_text' => 'Duplikat rating',
            ])
            ->assertSessionHasErrors('report_id');
    }
}
