<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Models\Customer;
use App\Models\DailyReport;
use App\Models\ReportColumn;
use App\Models\ReportType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomReportPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_custom_report_page_is_visible_to_clients_with_only_their_own_rows(): void
    {
        $reportType = ReportType::create([
            'slug' => 'quality_checks',
            'name' => 'Quality Checks',
            'is_system' => false,
            'is_active' => true,
        ]);
        ReportColumn::create([
            'report_type' => $reportType->slug,
            'column_key' => 'host_id',
            'label' => 'Host ID',
            'type' => 'text',
            'position' => 0,
            'is_visible' => true,
        ]);
        ReportColumn::create([
            'report_type' => $reportType->slug,
            'column_key' => 'quality_score',
            'label' => 'Quality Score',
            'type' => 'integer',
            'position' => 1,
            'is_visible' => true,
        ]);

        $clientUser = User::factory()->create(['role' => 'client']);
        $client = Client::create([
            'name' => 'Visible Client',
            'email' => $clientUser->email,
            'phone' => '9999999991',
            'status' => 'Active',
            'user_id' => $clientUser->id,
        ]);
        $customer = Customer::create([
            'client_id' => $client->id,
            'customer_id' => 'visible-host',
            'name' => 'Visible Host',
        ]);
        DailyReport::create([
            'client_id' => $client->id,
            'customer_id' => $customer->id,
            'dt' => '2026-09-01',
            'report_type' => $reportType->slug,
            'host_id' => 'visible-host',
            'extra_data' => ['quality_score' => 91],
        ]);

        $otherClientUser = User::factory()->create(['role' => 'client']);
        $otherClient = Client::create([
            'name' => 'Other Client',
            'email' => $otherClientUser->email,
            'phone' => '9999999992',
            'status' => 'Active',
            'user_id' => $otherClientUser->id,
        ]);
        $otherCustomer = Customer::create([
            'client_id' => $otherClient->id,
            'customer_id' => 'private-host',
            'name' => 'Private Host',
        ]);
        DailyReport::create([
            'client_id' => $otherClient->id,
            'customer_id' => $otherCustomer->id,
            'dt' => '2026-09-01',
            'report_type' => $reportType->slug,
            'host_id' => 'private-host',
            'extra_data' => ['quality_score' => 12],
        ]);

        $this->actingAs($clientUser)
            ->get(route('client.dashboard'))
            ->assertOk()
            ->assertSee('Quality Checks');

        $this->get(route('client.daily.reports', ['report_type' => $reportType->slug]))
            ->assertOk()
            ->assertSee('Quality Checks')
            ->assertSee('Quality Score')
            ->assertSee('visible-host')
            ->assertDontSee('private-host');
    }
}