<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Customer;
use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClearPaymentReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_clear_all_payment_reports_without_deleting_other_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '1234567890',
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
            'report_type' => 'payment_report',
        ]);
        DailyReport::create([
            'client_id' => $client->id,
            'customer_id' => $customer->id,
            'dt' => '2026-09-02',
            'report_type' => 'daily_report',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.reports.payment.clear-all'));

        $response->assertRedirect(route('admin.reports', ['report_type' => 'payment_report'], false));
        $this->assertDatabaseMissing('daily_reports', ['report_type' => 'payment_report']);
        $this->assertDatabaseHas('daily_reports', ['report_type' => 'daily_report']);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'clear_all_payment_reports']);
    }
}
