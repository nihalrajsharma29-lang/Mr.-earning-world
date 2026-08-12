<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('bank_account_number')->nullable()->after('address');
            $table->string('bank_ifsc_code')->nullable()->after('bank_account_number');
            $table->string('bank_name')->nullable()->after('bank_ifsc_code');
            $table->text('bank_address')->nullable()->after('bank_name');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'bank_account_number',
                'bank_ifsc_code',
                'bank_name',
                'bank_address',
            ]);
        });
    }
};
