<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('daily_reports as reports')
            ->join('customers', 'customers.id', '=', 'reports.customer_id')
            ->where('reports.report_type', 'payment_report')
            ->whereNull('reports.bank_country')
            ->whereNotNull('customers.country')
            ->select('reports.id', 'customers.country')
            ->get()
            ->each(function (object $report): void {
                DB::table('daily_reports')
                    ->where('id', $report->id)
                    ->update(['bank_country' => $report->country]);
            });
    }

    public function down(): void
    {
        // Existing bank-country values cannot be distinguished from backfilled values.
    }
};