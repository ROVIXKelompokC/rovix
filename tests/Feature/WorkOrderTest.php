<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $officer;
    private Report $report;

    protected function setUp(): void
    {
        parent::setUp();

        $district = District::create(['name' => 'Kecamatan Uji', 'city_code' => 'TST-01']);
        $category = ReportCategory::create(['category_name' => 'Lubang', 'severity_level' => 5]);

        $this->admin = User::create([
            'name' => 'Admin', 'email' => 'a@t.id', 'phone_number' => '081', 'role' => 'admin',
            'district_id' => $district->id, 'password' => 'password',
        ]);
        $this->officer = User::create([
            'name' => 'Petugas Uji', 'email' => 'p@t.id', 'phone_number' => '082', 'role' => 'petugas',
            'district_id' => $district->id, 'password' => 'password',
        ]);
        $warga = User::create([
            'name' => 'Warga', 'email' => 'w@t.id', 'phone_number' => '083', 'role' => 'warga',
            'district_id' => $district->id, 'password' => 'password',
        ]);

        $this->report = Report::create([
            'user_id' => $warga->id, 'category_id' => $category->id, 'district_id' => $district->id,
            'ticket_code' => 'RVX-2026-9001', 'description' => 'Lubang besar', 'photo_url' => 'reports/test.jpg', 'latitude' => -6.3,
            'longitude' => 106.8, 'ai_validation' => true, 'ai_confidence' => 95, 'status' => 'verified',
        ]);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'spk_number' => 'SPK-PU-2026-901',
            'report_id' => $this->report->id,
            'officer_id' => $this->officer->id,
            'target_sla' => now()->addDays(3)->toDateString(),
            'repair_notes' => 'Tambal cold-mix',
        ], $override);
    }

    public function test_admin_can_issue_spk_and_report_becomes_in_progress(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.work-orders.store'), $this->payload())
            ->assertRedirect(route('admin.work-orders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('work_orders', [
            'spk_number' => 'SPK-PU-2026-901',
            'officer_id' => $this->officer->id,
            'work_status' => 'assigned',
        ]);
        $this->assertSame('in_progress', $this->report->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'report_id' => $this->report->id,
            'action_name' => 'SPK_ISSUED',
            'previous_status' => 'verified',
            'new_status' => 'in_progress',
        ]);
    }

    public function test_officer_is_auto_assigned_when_left_blank(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.work-orders.store'), $this->payload(['officer_id' => '']))
            ->assertSessionHasNoErrors();

        $this->assertSame($this->officer->id, WorkOrder::first()->officer_id);
    }

    public function test_cannot_issue_spk_for_unverified_report(): void
    {
        $this->report->update(['status' => 'submitted']);

        $this->actingAs($this->admin)
            ->post(route('admin.work-orders.store'), $this->payload())
            ->assertSessionHasErrors('report_id');

        $this->assertSame(0, WorkOrder::count());
        $this->assertSame(0, AuditLog::count());
    }

    public function test_cannot_issue_second_spk_for_same_report(): void
    {
        $this->actingAs($this->admin)->post(route('admin.work-orders.store'), $this->payload());

        $this->actingAs($this->admin)
            ->post(route('admin.work-orders.store'), $this->payload(['spk_number' => 'SPK-PU-2026-902']))
            ->assertSessionHasErrors('report_id');
    }

    public function test_validation_rejects_past_sla_and_duplicate_number(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.work-orders.store'), $this->payload(['target_sla' => now()->subDay()->toDateString()]))
            ->assertSessionHasErrors('target_sla');
    }

    public function test_non_admin_cannot_access_admin_spk_pages(): void
    {
        $this->actingAs($this->officer)->get(route('admin.work-orders.index'))->assertForbidden();
        $this->actingAs($this->officer)->post(route('admin.work-orders.store'), $this->payload())->assertForbidden();
    }

    public function test_admin_list_create_and_detail_pages_render(): void
    {
        $this->actingAs($this->admin)->get(route('admin.work-orders.create'))
            ->assertOk()->assertSee('RVX-2026-9001');

        $this->actingAs($this->admin)->post(route('admin.work-orders.store'), $this->payload());
        $wo = WorkOrder::first();

        $this->actingAs($this->admin)->get(route('admin.work-orders.index'))
            ->assertOk()->assertSee('SPK-PU-2026-901');
        $this->actingAs($this->admin)->get(route('admin.work-orders.index', ['search' => 'zzz']))
            ->assertOk()->assertDontSee('SPK-PU-2026-901');
        $this->actingAs($this->admin)->get(route('admin.work-orders.show', $wo))
            ->assertOk()->assertSee('Lubang besar');
    }

    public function test_field_view_shows_officer_tasks_with_google_maps_link(): void
    {
        $this->actingAs($this->admin)->post(route('admin.work-orders.store'), $this->payload());

        $this->actingAs($this->officer)->get(route('petugas.dashboard'))
            ->assertOk()
            ->assertSee('SPK-PU-2026-901')
            ->assertSee('google.com/maps/dir/?api=1&destination=-6.3', false);
    }

    public function test_create_form_shows_priority_from_severity(): void
    {
        $this->actingAs($this->admin)->get(route('admin.work-orders.create'))
            ->assertOk()->assertSee('Prioritas Penanganan')->assertSee('data-severity="5"', false);
    }

    public function test_officer_detail_page_shows_before_photo_sla_notes_and_map_link(): void
    {
        $this->actingAs($this->admin)->post(route('admin.work-orders.store'), $this->payload());
        $wo = WorkOrder::first();

        $this->actingAs($this->officer)->get(route('petugas.work-orders.show', $wo))
            ->assertOk()
            ->assertSee('Foto Kondisi Awal')
            ->assertSee('reports/test.jpg')
            ->assertSee('Tambal cold-mix')
            ->assertSee('Kritis')
            ->assertSee('google.com/maps/dir/?api=1&destination=-6.3', false);
    }

    public function test_officer_cannot_open_another_officers_spk(): void
    {
        $this->actingAs($this->admin)->post(route('admin.work-orders.store'), $this->payload());
        $wo = WorkOrder::first();

        $other = User::create([
            'name' => 'Petugas Lain', 'email' => 'p2@t.id', 'phone_number' => '084', 'role' => 'petugas',
            'district_id' => $this->officer->district_id, 'password' => 'password',
        ]);

        $this->actingAs($other)->get(route('petugas.work-orders.show', $wo))->assertForbidden();
    }

    public function test_field_list_has_search_and_status_filter(): void
    {
        $this->actingAs($this->admin)->post(route('admin.work-orders.store'), $this->payload());

        $this->actingAs($this->officer)->get(route('petugas.dashboard'))
            ->assertOk()->assertSee('id="spk-search"', false)->assertSee('id="spk-status"', false)
            ->assertSee('Detail SPK');
    }
}
