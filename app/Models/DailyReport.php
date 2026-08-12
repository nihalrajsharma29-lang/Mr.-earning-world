<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [

        'client_id',
        'customer_id',

        'dt',
        'report_type',

        'host_id',
        'group_name',
        'user_name',

        'story_status',

        'gift_coins',
        'non_friend_video_coins',
        'friend_video_coins',
        'task_coins',
        'box_coins',
        'total_coins',
        'salary_amount',
        'salary_status',
        'violation_records',

        'group_time',

        'match_count',
        'match_duration_min',

        'app_kyc_pass',
        'profile_video_status',
        'category',

        'long_call_ratio',
        'avg_friend_call_duration_s30d',
        'total_call_duration_m',

        'bank_country',
        'if_bind_bank_info',

        'if_active',

        'current_week_total_coins',
        'previous_week1_total_coins',
        'previous_week2_total_coins',
        'previous_week3_total_coins',
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

        'payment_platform',
        'payment_account_name_unique',
        'if_phone_new',
        'bind_payment_account',
        'has_been_host_before',
        'reward_one_time_bonus_before_by_host_id',
        'average_call',

        'app_id',

        'has_live_permission',
        'start_live_duration_min',
        'live_to_call_ratio',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $attributes = [
        'report_type' => 'daily_report',
    ];

    protected function casts(): array
    {
        return [

            'dt' => 'date',

            'group_time' => 'datetime',

            'gift_coins' => 'integer',
            'non_friend_video_coins' => 'integer',
            'friend_video_coins' => 'integer',
            'task_coins' => 'integer',
            'box_coins' => 'integer',
            'total_coins' => 'integer',

            'match_count' => 'integer',

            'match_duration_min' => 'decimal:6',

            'long_call_ratio' => 'decimal:6',

            'avg_friend_call_duration_s30d' => 'decimal:2',

            'total_call_duration_m' => 'decimal:2',

            'report_type' => 'string',
            'salary_amount' => 'decimal:2',
            'salary_status' => 'string',
            'violation_records' => 'string',

            'current_week_total_coins' => 'integer',
            'previous_week1_total_coins' => 'integer',
            'previous_week2_total_coins' => 'integer',
            'previous_week3_total_coins' => 'integer',
            'weekly_total_coins_before_leftover' => 'decimal:2',
            'weekly_final_coins_hosts' => 'decimal:2',
            'weekly_reward_base_usd_before_strike_hosts' => 'decimal:2',
            'screenshot_strike' => 'integer',
            'message_strike' => 'integer',
            'weekly_reward_base_usd_hosts' => 'decimal:2',
            'hosts_ranking_bonus_world_usd' => 'decimal:2',
            'hosts_ranking_bonus_country_usd' => 'decimal:2',
            'br_co_bonus_usd' => 'decimal:2',
            'daily_rank_bonus_260803_260809' => 'decimal:2',
            'hosts_final_reward_usd' => 'decimal:2',
            'agent_fee_usd' => 'decimal:2',
            'agent_one_time_bonus_usd' => 'decimal:2',
            'payment_account_name_unique' => 'string',
            'if_phone_new' => 'string',
            'bind_payment_account' => 'string',
            'has_been_host_before' => 'string',
            'reward_one_time_bonus_before_by_host_id' => 'decimal:2',
            'average_call' => 'decimal:2',

            'has_live_permission' => 'boolean',

            'start_live_duration_min' => 'decimal:2',

            'live_to_call_ratio' => 'decimal:6',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Relationship: Daily Report belongs to Client
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Relationship: Daily Report belongs to Host / Customer
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}