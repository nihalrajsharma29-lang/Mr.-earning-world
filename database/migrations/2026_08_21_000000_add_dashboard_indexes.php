<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->index(['approval_status', 'client_id'], 'customers_approval_client_index');
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->index(['report_type', 'dt'], 'daily_reports_type_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_approval_client_index');
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropIndex('daily_reports_type_date_index');
        });
    }
};