<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $warga;
    protected User $otherWarga;
    protected User $admin;
    protected District $district;
    protected ReportCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Cari atau buat data pengujian
        $this->district = District::firstOrCreate(
            ['city_code' => 'TEST-01'],
            ['name' => 'Kecamatan Pengujian']
        );

        $this->category = ReportCategory::firstOrCreate(
            ['category_name' => 'Jalan Berlubang'],
            ['severity_level' => 5]
        );

        $this->warga = User::updateOrCreate(
            ['email' => 'warga.test@rovix.id'],
            [
                'name' => 'Warga Pengujian',
                'phone_number' => '081234567890',
                'role' => 'warga',
                'district_id' => $this->district->id,
                'password' => bcrypt('password'),
            ]
        );

        $this->otherWarga = User::updateOrCreate(
            ['email' => 'other.test@rovix.id'],
            [
                'name' => 'Warga Lain',
                'phone_number' => '081234567899',
                'role' => 'warga',
                'district_id' => $this->district->id,
                'password' => bcrypt('password'),
            ]
        );

        $this->admin = User::updateOrCreate(
            ['email' => 'admin.test@rovix.id'],
            [
                'name' => 'Admin Pengujian',
                'phone_number' => '081200000000',
                'role' => 'admin',
                'district_id' => $this->district->id,
                'password' => bcrypt('password'),
            ]
        );
    }

    public function test_guest_cannot_access_warga_reports_index(): void
    {
        $response = $this->get(route('warga.reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_warga_can_view_reports_index(): void
    {
        $report = Report::create([
            'user_id' => $this->warga->id,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'ticket_code' => 'RVX-2026-TEST1',
            'description' => 'Jalan berlubang besar di depan pertigaan jalan utama test.',
            'photo_url' => 'reports/test1.jpg',
            'latitude' => -6.30720000,
            'longitude' => 106.83980000,
            'ai_validation' => true,
            'ai_confidence' => 96.50,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->warga)->get(route('warga.reports.index'));

        $response->assertOk();
        $response->assertViewIs('warga.reports.index');
        $response->assertViewHas('reports');
        $response->assertViewHas('stats');
        $response->assertSee($report->ticket_code);
    }

    public function test_authenticated_warga_can_view_create_form(): void
    {
        $response = $this->actingAs($this->warga)->get(route('warga.reports.create'));

        $response->assertOk();
        $response->assertViewIs('warga.reports.create');
        $response->assertViewHas('categories');
        $response->assertViewHas('districts');
        $response->assertSee('Formulir Pengaduan Kerusakan Jalan');
    }

    public function test_store_report_requires_valid_data(): void
    {
        $response = $this->actingAs($this->warga)->post(route('warga.reports.store'), []);

        $response->assertSessionHasErrors([
            'photo',
            'description',
            'category_id',
            'district_id',
            'latitude',
            'longitude',
        ]);
    }

    public function test_authenticated_warga_can_submit_valid_report_with_ai_simulation(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('pothole_kerusakan_jalan.jpg', 640, 480);

        $payload = [
            'photo' => $image,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'description' => 'Aspal retak dan berlubang sekitar 12cm yang membahayakan pengendara motor.',
            'latitude' => -6.31200000,
            'longitude' => 106.84500000,
        ];

        $response = $this->actingAs($this->warga)->post(route('warga.reports.store'), $payload);

        $report = Report::where('user_id', $this->warga->id)
            ->where('description', $payload['description'])
            ->first();

        $this->assertNotNull($report, 'Laporan harus tersimpan di database.');
        $this->assertStringStartsWith('RVX-', $report->ticket_code);
        $this->assertEquals('submitted', $report->status);
        $this->assertGreaterThan(0, $report->ai_confidence);
        Storage::disk('public')->assertExists($report->photo_url);

        // Pastikan tercatat di audit_logs
        $this->assertDatabaseHas('audit_logs', [
            'report_id' => $report->id,
            'user_id' => $this->warga->id,
            'action_name' => 'LAPORAN_BARU_WARGA',
            'new_status' => 'submitted',
        ]);

        $response->assertRedirect(route('warga.reports.show', $report->id));
        $response->assertSessionHas('success');
    }

    public function test_authenticated_warga_can_view_own_report_show(): void
    {
        $report = Report::create([
            'user_id' => $this->warga->id,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'ticket_code' => 'RVX-2026-SHOW1',
            'description' => 'Lubang jalan dalam uji show.',
            'photo_url' => 'reports/show1.jpg',
            'latitude' => -6.30720000,
            'longitude' => 106.83980000,
            'ai_validation' => true,
            'ai_confidence' => 97.20,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->warga)->get(route('warga.reports.show', $report->id));

        $response->assertOk();
        $response->assertViewIs('warga.reports.show');
        $response->assertViewHas('report');
        $response->assertSee($report->ticket_code);
    }

    public function test_warga_cannot_view_other_warga_report(): void
    {
        $otherReport = Report::create([
            'user_id' => $this->otherWarga->id,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'ticket_code' => 'RVX-2026-OTHER',
            'description' => 'Laporan milik warga lain tidak boleh dilihat.',
            'photo_url' => 'reports/other.jpg',
            'latitude' => -6.30720000,
            'longitude' => 106.83980000,
            'ai_validation' => true,
            'ai_confidence' => 95.00,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->warga)->get(route('warga.reports.show', $otherReport->id));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_any_report(): void
    {
        $report = Report::create([
            'user_id' => $this->warga->id,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'ticket_code' => 'RVX-2026-ADMINVIEW',
            'description' => 'Laporan warga dapat dilihat admin.',
            'photo_url' => 'reports/adminview.jpg',
            'latitude' => -6.30720000,
            'longitude' => 106.83980000,
            'ai_validation' => true,
            'ai_confidence' => 95.00,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->admin)->get(route('warga.reports.show', $report->id));

        $response->assertOk();
        $response->assertSee($report->ticket_code);
    }
}
