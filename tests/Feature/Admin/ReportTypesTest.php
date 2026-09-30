<?php

namespace Tests\Feature\Admin;

use App\Models\ReportColumn;
use App\Models\Client;
use App\Models\Customer;
use App\Models\DailyReport;
use App\Models\ReportImportName;
use App\Models\ReportType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReportTypesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_configure_remove_and_restore_custom_report_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.report-types.store'), ['name' => 'Quality Check Report'])
            ->assertRedirect(route('admin.report-types.index', absolute: false));

        $reportType = ReportType::where('slug', 'quality_check_report')->firstOrFail();
        $this->put(route('admin.report-types.update', $reportType), ['name' => 'Quality Audit Report'])
            ->assertRedirect(route('admin.report-types.index', absolute: false));
        $reportType->refresh();
        $this->assertSame('quality_check_report', $reportType->slug);
        $this->assertSame('Quality Audit Report', $reportType->name);

        $this->assertDatabaseHas('report_columns', [
            'report_type' => $reportType->slug,
            'column_key' => 'host_id',
        ]);

        $this->get(route('admin.daily.import'))
            ->assertOk()
            ->assertSee('Quality Audit Report');
        $this->post(route('admin.daily.import.store'), [
            'report_type' => $reportType->slug,
            'file' => UploadedFile::fake()->createWithContent(
                'quality-check.csv',
                "Host ID,Date,Quality Score\nunknown-host,2026-09-01,95\n"
            ),
        ])->assertRedirect()->assertSessionHas('success');
        $this->get(route('admin.report-columns.index'))
            ->assertOk()
            ->assertSee('Quality Audit Report');
        $this->get(route('admin.reports', ['report_type' => $reportType->slug]))
            ->assertOk()
            ->assertSee('Quality Audit Report Overview');
        $manager = User::factory()->create(['role' => 'manager']);
        $this->actingAs($manager)
            ->get(route('manager.reports', ['report_type' => $reportType->slug]))
            ->assertOk()
            ->assertSee('Quality Audit Report Overview');

        $this->actingAs($admin);
        $this->delete(route('admin.report-types.destroy', $reportType))
            ->assertRedirect(route('admin.report-types.index', absolute: false));
        $this->assertDatabaseHas('report_types', [
            'slug' => $reportType->slug,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('report_columns', [
            'report_type' => $reportType->slug,
            'column_key' => 'host_id',
        ]);
        $this->get(route('admin.reports', ['report_type' => $reportType->slug]))->assertNotFound();

        $this->patch(route('admin.report-types.restore', $reportType))->assertRedirect();
        $this->assertTrue($reportType->fresh()->is_active);
    }

    public function test_admin_can_remove_builtin_pages_but_must_keep_one_page_active(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $dailyReport = ReportType::where('slug', 'daily_report')->firstOrFail();
        $paymentReport = ReportType::where('slug', 'payment_report')->firstOrFail();
        $violationReport = ReportType::where('slug', 'violation_records')->firstOrFail();

        $this->delete(route('admin.report-types.destroy', $dailyReport))->assertRedirect();
        $this->delete(route('admin.report-types.destroy', $paymentReport))->assertRedirect();
        $this->delete(route('admin.report-types.destroy', $violationReport))
            ->assertSessionHasErrors('report_type');

        $this->assertFalse($dailyReport->fresh()->is_active);
        $this->assertFalse($paymentReport->fresh()->is_active);
        $this->assertTrue($violationReport->fresh()->is_active);

        $this->patch(route('admin.report-types.restore', $dailyReport))->assertRedirect();
        $this->assertTrue($dailyReport->fresh()->is_active);
    }

    public function test_admin_can_clear_one_report_pages_data_without_deleting_its_settings_or_other_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reportType = ReportType::create([
            'slug' => 'quality_report',
            'name' => 'Quality Report',
            'is_system' => false,
            'is_active' => true,
        ]);
        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@example.test',
            'phone' => '9999999999',
            'company' => 'Test Company',
            'address' => 'Test Address',
            'status' => 'Active',
        ]);
        $customer = Customer::create([
            'client_id' => $client->id,
            'customer_id' => 'host-1',
            'name' => 'Test Host',
        ]);
        $targetReport = DailyReport::create([
            'client_id' => $client->id,
            'customer_id' => $customer->id,
            'dt' => '2026-09-01',
            'report_type' => $reportType->slug,
            'host_id' => 'host-1',
        ]);
        $otherReport = DailyReport::create([
            'client_id' => $client->id,
            'customer_id' => $customer->id,
            'dt' => '2026-09-01',
            'report_type' => 'payment_report',
            'host_id' => 'host-1',
        ]);
        ReportColumn::create([
            'report_type' => $reportType->slug,
            'column_key' => 'host_id',
            'label' => 'Host ID',
            'type' => 'text',
            'position' => 0,
            'is_visible' => true,
        ]);
        ReportImportName::create([
            'report_type' => $reportType->slug,
            'name' => 'Quality-File',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.report-types.index'))
            ->assertOk()
            ->assertSee('Clear Data')
            ->assertSee(route('admin.report-types.clear-data', $reportType), false);

        $this->delete(route('admin.report-types.clear-data', $reportType))
            ->assertRedirect(route('admin.report-types.index', absolute: false))
            ->assertSessionHas('success', 'Cleared 1 imported report(s) from Quality Report.');

        $this->assertDatabaseMissing('daily_reports', ['id' => $targetReport->id]);
        $this->assertDatabaseHas('daily_reports', ['id' => $otherReport->id]);
        $this->assertDatabaseHas('report_types', ['slug' => $reportType->slug]);
        $this->assertDatabaseHas('report_columns', ['report_type' => $reportType->slug]);
        $this->assertDatabaseHas('report_import_names', ['report_type' => $reportType->slug]);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'clear_report_type_data']);
    }

    public function test_removed_report_page_can_be_permanently_deleted_with_its_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->post(route('admin.report-types.store'), ['name' => 'Temporary Report'])
            ->assertRedirect();

        $reportType = ReportType::where('slug', 'temporary_report')->firstOrFail();
        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@example.test',
            'phone' => '9999999999',
            'company' => 'Test Company',
            'address' => 'Test Address',
            'status' => 'Active',
        ]);
        $customer = Customer::create([
            'client_id' => $client->id,
            'customer_id' => 'host-1',
            'name' => 'Test Host',
        ]);
        DailyReport::create([
            'client_id' => $client->id,
            'customer_id' => $customer->id,
            'dt' => '2026-09-01',
            'report_type' => $reportType->slug,
            'host_id' => 'host-1',
        ]);
        ReportImportName::create([
            'report_type' => $reportType->slug,
            'name' => 'Temporary-File',
        ]);

        $this->delete(route('admin.report-types.force-destroy', $reportType))
            ->assertSessionHasErrors('report_type');
        $this->delete(route('admin.report-types.destroy', $reportType))->assertRedirect();
        $this->delete(route('admin.report-types.force-destroy', $reportType))->assertRedirect();

        $this->assertDatabaseMissing('report_types', ['slug' => $reportType->slug]);
        $this->assertDatabaseMissing('daily_reports', ['report_type' => $reportType->slug]);
        $this->assertDatabaseMissing('report_columns', ['report_type' => $reportType->slug]);
        $this->assertDatabaseMissing('report_import_names', ['report_type' => $reportType->slug]);
    }
}