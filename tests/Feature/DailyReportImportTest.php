<?php

namespace Tests\Feature;

use App\Imports\DailyReportImport;
use App\Models\Client;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReportImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_importing_same_payment_host_and_date_updates_existing_report(): void
    {
        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '1234567890',
        ]);
        Customer::create([
            'client_id' => $client->id,
            'customer_id' => 'host-1',
            'name' => 'Test Host',
        ]);

        $import = new DailyReportImport(reportType: 'payment_report');
        $row = [
            'hostid' => 'host-1',
            'date' => '2026-10-02',
            'totalcoins' => 10,
        ];

        $import->model($row);
        $import->model(array_merge($row, ['totalcoins' => 25]));

        $this->assertDatabaseCount('daily_reports', 1);
        $this->assertDatabaseHas('daily_reports', [
            'report_type' => 'payment_report',
            'dt' => '2026-10-02',
            'total_coins' => 25,
        ]);
    }
}