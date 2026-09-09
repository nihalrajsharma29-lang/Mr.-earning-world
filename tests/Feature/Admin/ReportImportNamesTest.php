<?php

namespace Tests\Feature\Admin;

use App\Models\ReportImportName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
