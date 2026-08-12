<?php

namespace App\Support;

class ViolationReportColumns
{
    /**
     * Column definitions used by violation report table and export.
     */
    public static function definitions(): array
    {
        return [
            ['key' => 'snapshots_time', 'label' => 'Date', 'type' => 'datetime'],
            ['key' => 'host_id', 'label' => 'Host Id', 'type' => 'text'],
            ['key' => 'user_name', 'label' => 'User Name', 'type' => 'text'],
            ['key' => 'client_name_uid', 'label' => 'Client Name / UID', 'type' => 'text'],
            ['key' => 'nudity_sexual_behavior_non_friend_calls', 'label' => 'Nudity & Sexual Behavior-Non Friend calls', 'type' => 'integer'],
            ['key' => 'nudity_sexual_dress_non_friend_calls', 'label' => 'Nudity & Sexual Dress-Non Friend calls', 'type' => 'integer'],
            ['key' => 'fake_user_in_screen_non_friend_calls', 'label' => 'Fake User in Screen-Non Friend calls', 'type' => 'integer'],
            ['key' => 'black_screen_non_friend_calls', 'label' => 'Black Screen-Non Friend calls', 'type' => 'integer'],
            ['key' => 'no_user_in_screen_non_friend_calls', 'label' => 'No User in Screen-Non Friend calls', 'type' => 'integer'],
            ['key' => 'male_in_screen_non_friend_calls', 'label' => 'Male in Screen-Non Friend calls', 'type' => 'integer'],
            ['key' => 'underage_person_in_screen_all_calls', 'label' => 'Underage Person in Screen-All calls', 'type' => 'integer'],
            ['key' => 'nudity_sexual_behavior_friend_calls', 'label' => 'Nudity & Sexual Behavior-Friend calls', 'type' => 'integer'],
            ['key' => 'nudity_sexual_dress_friend_calls', 'label' => 'Nudity & Sexual Dress-Friend calls', 'type' => 'integer'],
        ];
    }

    public static function headings(): array
    {
        return collect(self::definitions())
            ->pluck('label')
            ->all();
    }
}
