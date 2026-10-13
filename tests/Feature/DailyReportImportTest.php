<?php

namespace Tests\Feature;

use App\Imports\DailyReportImport;
use App\Models\Client;
use App\Models\Customer;
use App\Models\DailyReport;
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

    public function test_importing_multiple_payment_transactions_for_one_host_keeps_each_row(): void
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

        $import = new DailyReportImport(reportType: 'pa');
        $baseRow = [
            'hostid' => 'host-1',
            'date' => '2026-10-09',
            'txnstatus' => 'SUCCESS',
        ];

        $import->model(array_merge($baseRow, ['tradeorderid' => 'order-1']));
        $import->model(array_merge($baseRow, ['tradeorderid' => 'order-2']));
        $import->model(array_merge($baseRow, ['tradeorderid' => 'order-1', 'txnstatus' => 'UPDATED']));

        $reports = DailyReport::query()->where('report_type', 'pa')->get();

        $this->assertCount(2, $reports);
        $this->assertEqualsCanonicalizing(
            ['order-1', 'order-2'],
            $reports->map(fn (DailyReport $report) => $report->extra_data['tradeorderid'])->all()
        );
        $this->assertDatabaseHas('daily_reports', [
            'report_type' => 'pa',
            'import_key' => hash('sha256', 'order-1'),
        ]);
    }

    public function test_transaction_import_reuses_an_existing_legacy_host_date_row(): void
    {
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
            'dt' => '2026-10-09',
            'report_type' => 'pa',
            'host_id' => 'host-1',
            'extra_data' => ['txnstatus' => 'SUCCESS'],
        ]);

        $import = new DailyReportImport(reportType: 'pa');
        $import->model([
            'hostid' => 'host-1',
            'date' => '2026-10-09',
            'tradeorderid' => 'order-1',
            'txnstatus' => 'SUCCESS',
        ]);

        $this->assertDatabaseCount('daily_reports', 1);
        $this->assertDatabaseHas('daily_reports', [
            'report_type' => 'pa',
            'import_key' => hash('sha256', 'order-1'),
        ]);
    }
}
