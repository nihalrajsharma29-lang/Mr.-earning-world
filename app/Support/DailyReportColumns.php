<?php

namespace App\Support;

class DailyReportColumns
{
    public static function definitions(): array
    {
        return [
            ['key' => 'dt', 'label' => 'Date', 'type' => 'date'],
            ['key' => 'report_type', 'label' => 'Report Type', 'type' => 'text'],
            ['key' => 'host_id', 'label' => 'Host ID', 'type' => 'text'],
            ['key' => 'client_name_uid', 'label' => 'Client Name / UID', 'type' => 'text'],
            ['key' => 'user_name', 'label' => 'Username', 'type' => 'text'],
            ['key' => 'story_status', 'label' => 'Story Status', 'type' => 'text'],
            ['key' => 'gift_coins', 'label' => 'Gift Coins', 'type' => 'integer'],
            ['key' => 'non_friend_video_coins', 'label' => 'Non-Friend Video Coins', 'type' => 'integer'],
            ['key' => 'friend_video_coins', 'label' => 'Friend Video Coins', 'type' => 'integer'],
            ['key' => 'task_coins', 'label' => 'Task Coins', 'type' => 'integer'],
            ['key' => 'box_coins', 'label' => 'Box Coins', 'type' => 'integer'],
            ['key' => 'total_coins', 'label' => 'Total Coins', 'type' => 'integer'],
            ['key' => 'group_time', 'label' => 'Group Time', 'type' => 'datetime'],
            ['key' => 'match_count', 'label' => 'Match Count', 'type' => 'integer'],
            ['key' => 'match_duration_min', 'label' => 'Match Duration (Min)', 'type' => 'decimal'],
            ['key' => 'app_kyc_pass', 'label' => 'App KYC Pass', 'type' => 'text'],
            ['key' => 'profile_video_status', 'label' => 'Profile Video Status', 'type' => 'text'],
            ['key' => 'category', 'label' => 'Category', 'type' => 'text'],
            ['key' => 'long_call_ratio', 'label' => 'Long Call Ratio', 'type' => 'decimal'],
            ['key' => 'avg_friend_call_duration_s30d', 'label' => 'Avg. Friend Call Duration (30D)', 'type' => 'decimal'],
            ['key' => 'total_call_duration_m', 'label' => 'Total Call Duration (Min)', 'type' => 'decimal'],
            ['key' => 'bank_country', 'label' => 'Bank Country', 'type' => 'text'],
            ['key' => 'if_active', 'label' => 'Active Status', 'type' => 'text'],
            ['key' => 'current_week_total_coins', 'label' => 'Current Week Total Coins', 'type' => 'integer'],
            ['key' => 'previous_week1_total_coins', 'label' => 'Previous Week 1 Total Coins', 'type' => 'integer'],
            ['key' => 'previous_week2_total_coins', 'label' => 'Previous Week 2 Total Coins', 'type' => 'integer'],
            ['key' => 'previous_week3_total_coins', 'label' => 'Previous Week 3 Total Coins', 'type' => 'integer'],
            ['key' => 'payment_platform', 'label' => 'Payment Platform', 'type' => 'text'],
            ['key' => 'app_id', 'label' => 'App ID', 'type' => 'text'],
            ['key' => 'has_live_permission', 'label' => 'Live Permission', 'type' => 'boolean'],
            ['key' => 'start_live_duration_min', 'label' => 'Start Live Duration (Min)', 'type' => 'decimal'],
            ['key' => 'live_to_call_ratio', 'label' => 'Live-to-Call Ratio', 'type' => 'decimal'],
        ];
    }
}