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
            $table->decimal('salary_amount', 15, 2)->default(0)->after('total_coins');
            $table->string('salary_status')->nullable()->after('salary_amount');
            $table->text('violation_records')->nullable()->after('salary_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['salary_amount', 'salary_status', 'violation_records']);
        });
    }
};
