<?php

namespace Tests\Feature\Admin;

use App\Models\ReportImportName;
use App\Models\ReportType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReportImportNamesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_and_reload_report_import_names(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.report-import-names.store'), [
            'daily_report' => 'Daily-2026',
            'payment_report' => 'Payment-2026',
            'violation_records' => 'Violations-2026',
        ]);

        $response->assertRedirect(route('admin.report-import-names', absolute: false));
        $this->assertDatabaseHas('report_import_names', [
            'report_type' => 'payment_report',
            'name' => 'Payment-2026',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.report-import-names'))
            ->assertSee('Payment-2026');

        $this->assertSame('Payment-2026', ReportImportName::values()['payment_report']);
    }

    public function test_active_custom_report_page_has_a_filename_setting(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        ReportType::create([
            'slug' => 'quality_check_report',
            'name' => 'Quality Check Report',
            'is_system' => false,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.report-import-names'))
            ->assertOk()
            ->assertSee('Quality Check Report')
            ->assertSee('report_names[quality_check_report]');

        $this->post(route('admin.report-import-names.store'), [
            'report_names' => ['quality_check_report' => 'Quality-Export'],
        ])->assertRedirect(route('admin.report-import-names', absolute: false));

        $this->assertDatabaseHas('report_import_names', [
            'report_type' => 'quality_check_report',
            'name' => 'Quality-Export',
        ]);
        $this->assertSame('Quality-Export', ReportImportName::values()['quality_check_report']);

        $this->post(route('admin.daily.import.store'), [
            'report_type' => 'quality_check_report',
            'file' => UploadedFile::fake()->createWithContent(
                'wrong-name.csv',
                "Host ID,Date\nunknown-host,2026-09-01\n"
            ),
        ])->assertRedirect()->assertSessionHas('error');

        $this->post(route('admin.daily.import.store'), [
            'report_type' => 'quality_check_report',
            'file' => UploadedFile::fake()->createWithContent(
                'quality-export.csv',
                "Host ID,Date\nunknown-host,2026-09-01\n"
            ),
        ])->assertRedirect()->assertSessionHas('success');
    }
}
