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

        'payment_platform',

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

            'current_week_total_coins' => 'integer',
            'previous_week1_total_coins' => 'integer',
            'previous_week2_total_coins' => 'integer',
            'previous_week3_total_coins' => 'integer',

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