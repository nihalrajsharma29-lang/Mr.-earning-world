<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->string('import_key', 64)->default('')->after('report_type');
            $table->dropUnique('daily_reports_customer_date_report_type_unique');
            $table->unique(
                ['customer_id', 'dt', 'report_type', 'import_key'],
                'daily_reports_customer_date_type_import_key_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropUnique('daily_reports_customer_date_type_import_key_unique');
            $table->dropColumn('import_key');
            $table->unique(
                ['customer_id', 'dt', 'report_type'],
                'daily_reports_customer_date_report_type_unique'
            );
        });
    }
};
