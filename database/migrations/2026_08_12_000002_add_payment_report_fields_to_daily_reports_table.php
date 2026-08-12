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
            $table->decimal('weekly_total_coins_before_leftover', 16, 2)->default(0)->after('previous_week3_total_coins');
            $table->decimal('weekly_final_coins_hosts', 16, 2)->default(0)->after('weekly_total_coins_before_leftover');
            $table->decimal('weekly_reward_base_usd_before_strike_hosts', 16, 2)->default(0)->after('weekly_final_coins_hosts');
            $table->unsignedInteger('screenshot_strike')->default(0)->after('weekly_reward_base_usd_before_strike_hosts');
            $table->unsignedInteger('message_strike')->default(0)->after('screenshot_strike');
            $table->decimal('weekly_reward_base_usd_hosts', 16, 2)->default(0)->after('message_strike');
            $table->decimal('hosts_ranking_bonus_world_usd', 16, 2)->default(0)->after('weekly_reward_base_usd_hosts');
            $table->decimal('hosts_ranking_bonus_country_usd', 16, 2)->default(0)->after('hosts_ranking_bonus_world_usd');
            $table->decimal('br_co_bonus_usd', 16, 2)->default(0)->after('hosts_ranking_bonus_country_usd');
            $table->decimal('daily_rank_bonus_260803_260809', 16, 2)->default(0)->after('br_co_bonus_usd');
            $table->decimal('hosts_final_reward_usd', 16, 2)->default(0)->after('daily_rank_bonus_260803_260809');
            $table->decimal('agent_fee_usd', 16, 2)->default(0)->after('hosts_final_reward_usd');
            $table->decimal('agent_one_time_bonus_usd', 16, 2)->default(0)->after('agent_fee_usd');
            $table->string('payment_account_name_unique')->nullable()->after('payment_platform');
            $table->string('if_phone_new')->nullable()->after('payment_account_name_unique');
            $table->string('bind_payment_account')->nullable()->after('if_phone_new');
            $table->string('has_been_host_before')->nullable()->after('bind_payment_account');
            $table->decimal('reward_one_time_bonus_before_by_host_id', 16, 2)->default(0)->after('has_been_host_before');
            $table->decimal('average_call', 16, 2)->default(0)->after('reward_one_time_bonus_before_by_host_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn([
                'weekly_total_coins_before_leftover',
                'weekly_final_coins_hosts',
                'weekly_reward_base_usd_before_strike_hosts',
                'screenshot_strike',
                'message_strike',
                'weekly_reward_base_usd_hosts',
                'hosts_ranking_bonus_world_usd',
                'hosts_ranking_bonus_country_usd',
                'br_co_bonus_usd',
                'daily_rank_bonus_260803_260809',
                'hosts_final_reward_usd',
                'agent_fee_usd',
                'agent_one_time_bonus_usd',
                'payment_account_name_unique',
                'if_phone_new',
                'bind_payment_account',
                'has_been_host_before',
                'reward_one_time_bonus_before_by_host_id',
                'average_call',
            ]);
        });
    }
};
