<?php

namespace App\Support;

class PaymentReportColumns
{
    /**
     * Column definitions used by payment report tables and export.
     */
    public static function definitions(): array
    {
        return [
            ['key' => 'host_id', 'label' => 'Id', 'type' => 'text'],
            ['key' => 'client_name_uid', 'label' => 'Client Name / UID', 'type' => 'text'],
            ['key' => 'user_name', 'label' => 'User Name', 'type' => 'text'],
            ['key' => 'customer.country', 'label' => 'Country', 'type' => 'text'],
            ['key' => 'category', 'label' => 'Categories', 'type' => 'text'],
            ['key' => 'weekly_total_coins_before_leftover', 'label' => 'Weekly total coins bef. leftover', 'type' => 'decimal'],
            ['key' => 'weekly_final_coins_hosts', 'label' => 'Weekly Final Coins(Hosts)', 'type' => 'decimal'],
            ['key' => 'weekly_reward_base_usd_before_strike_hosts', 'label' => 'Weekly Reward Base(USD) Before strike (Hosts)', 'type' => 'currency'],
            ['key' => 'screenshot_strike', 'label' => 'Screenshot Strike', 'type' => 'integer'],
            ['key' => 'message_strike', 'label' => 'Message Strike', 'type' => 'integer'],
            ['key' => 'weekly_reward_base_usd_hosts', 'label' => 'Weekly Reward Base (USD) (Hosts)', 'type' => 'currency'],
            ['key' => 'hosts_ranking_bonus_world_usd', 'label' => 'Hosts Ranking bonus-World (USD)', 'type' => 'currency'],
            ['key' => 'hosts_ranking_bonus_country_usd', 'label' => 'Hosts Ranking bonus-Country (USD)', 'type' => 'currency'],
            ['key' => 'br_co_bonus_usd', 'label' => 'BR+CO bonus (USD)', 'type' => 'currency'],
            ['key' => 'daily_rank_bonus_260803_260809', 'label' => 'Daily rank Bonus 260803-260809', 'type' => 'currency'],
            ['key' => 'hosts_final_reward_usd', 'label' => 'Hosts Final Reward (USD)', 'type' => 'currency'],
            ['key' => 'agent_fee_usd', 'label' => 'Agent Fee(USD)', 'type' => 'currency'],
            ['key' => 'agent_one_time_bonus_usd', 'label' => 'Agent one time bonus (USD)', 'type' => 'currency'],
            ['key' => 'payment_platform', 'label' => 'Platform', 'type' => 'text'],
            ['key' => 'payment_account_name_unique', 'label' => 'Is the payment account name unique', 'type' => 'text'],
            ['key' => 'if_phone_new', 'label' => 'if phones new', 'type' => 'text'],
            ['key' => 'bind_payment_account', 'label' => 'Whether she bind the payment account', 'type' => 'text'],
            ['key' => 'has_been_host_before', 'label' => 'Whether she has been host before', 'type' => 'text'],
            ['key' => 'reward_one_time_bonus_before_by_host_id', 'label' => 'Reward one time bonus before by Host ID', 'type' => 'currency'],
            ['key' => 'average_call', 'label' => 'Average call', 'type' => 'decimal'],
            ['key' => 'bank_country', 'label' => 'Bank account country', 'type' => 'text'],
            ['key' => 'group_time', 'label' => 'Group Time', 'type' => 'datetime'],
        ];
    }

    public static function headings(): array
    {
        return collect(self::definitions())
            ->pluck('label')
            ->all();
    }
}
