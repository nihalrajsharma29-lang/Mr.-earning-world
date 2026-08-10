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
        Schema::create('daily_reports', function (Blueprint $table) {

            $table->id();

            // Client
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            // Host / Customer
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            // Daily report date
            $table->date('dt');

            // Host information
            $table->string('host_id')->nullable();
            $table->string('group_name')->nullable();
            $table->string('user_name')->nullable();

            // Status
            $table->string('story_status')->nullable();

            // Coins
            $table->unsignedBigInteger('gift_coins')->default(0);
            $table->unsignedBigInteger('non_friend_video_coins')->default(0);
            $table->unsignedBigInteger('friend_video_coins')->default(0);
            $table->unsignedBigInteger('task_coins')->default(0);
            $table->unsignedBigInteger('box_coins')->default(0);
            $table->unsignedBigInteger('total_coins')->default(0);

            // Group
            $table->dateTime('group_time')->nullable();

            // Match
            $table->unsignedInteger('match_count')->default(0);
            $table->decimal('match_duration_min', 12, 6)->default(0);

            // KYC / Profile
            $table->string('app_kyc_pass')->nullable();
            $table->string('profile_video_status')->nullable();
            $table->string('category')->nullable();

            // Call performance
            $table->decimal('long_call_ratio', 12, 6)->default(0);
            $table->decimal('avg_friend_call_duration_s30d', 12, 2)->default(0);
            $table->decimal('total_call_duration_m', 12, 2)->default(0);

            // Bank
            $table->string('bank_country', 10)->nullable();
            $table->string('if_bind_bank_info')->nullable();

            // Active
            $table->string('if_active')->nullable();

            // Weekly coins
            $table->unsignedBigInteger('current_week_total_coins')->default(0);
            $table->unsignedBigInteger('previous_week1_total_coins')->default(0);
            $table->unsignedBigInteger('previous_week2_total_coins')->default(0);
            $table->unsignedBigInteger('previous_week3_total_coins')->default(0);

            // Payment
            $table->string('payment_platform')->nullable();

            // App
            $table->string('app_id')->nullable();

            // Live
            $table->boolean('has_live_permission')->default(false);
            $table->decimal('start_live_duration_min', 12, 2)->default(0);
            $table->decimal('live_to_call_ratio', 12, 6)->default(0);

            $table->timestamps();

            // Same host should normally have one report per date
            $table->unique(
                ['customer_id', 'dt'],
                'daily_reports_customer_date_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};