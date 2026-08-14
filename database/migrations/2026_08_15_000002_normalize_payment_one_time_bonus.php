<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('daily_reports')
            ->where('report_type', 'payment_report')
            ->where('agent_one_time_bonus_usd', '>', 0)
            ->update(['agent_one_time_bonus_usd' => 5]);
    }

    public function down(): void
    {
        // Original bonus amounts cannot be restored after normalization.
    }
};