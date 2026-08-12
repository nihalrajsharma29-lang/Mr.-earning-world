<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropUnique('daily_reports_customer_date_unique');
            $table->unique(
                ['customer_id', 'dt', 'report_type'],
                'daily_reports_customer_date_report_type_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropUnique('daily_reports_customer_date_report_type_unique');
            $table->unique(
                ['customer_id', 'dt'],
                'daily_reports_customer_date_unique'
            );
        });
    }
};
