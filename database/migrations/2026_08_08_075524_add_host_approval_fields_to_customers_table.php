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
        Schema::table('customers', function (Blueprint $table) {

            // Host approval status
            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending')->after('status');

            // Admin approval date
            $table->timestamp('approved_at')
                ->nullable()
                ->after('approval_status');

            // Reason if admin rejects the host
            $table->text('rejection_reason')
                ->nullable()
                ->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status',
                'approved_at',
                'rejection_reason',
            ]);
        });
    }
};