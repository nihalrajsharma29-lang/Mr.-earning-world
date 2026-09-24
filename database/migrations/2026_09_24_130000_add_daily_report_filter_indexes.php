<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->index(['report_type', 'dt'], 'daily_reports_type_dt_index');
            $table->index(['report_type', 'weekly_date'], 'daily_reports_type_weekly_date_index');
            $table->index(['client_id', 'report_type', 'dt'], 'daily_reports_client_type_dt_index');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropIndex('daily_reports_type_dt_index');
            $table->dropIndex('daily_reports_type_weekly_date_index');
            $table->dropIndex('daily_reports_client_type_dt_index');
        });
    }
};