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
            $table->unsignedInteger('nudity_sexual_behavior_non_friend_calls')->default(0)->after('violation_records');
            $table->unsignedInteger('nudity_sexual_dress_non_friend_calls')->default(0)->after('nudity_sexual_behavior_non_friend_calls');
            $table->unsignedInteger('fake_user_in_screen_non_friend_calls')->default(0)->after('nudity_sexual_dress_non_friend_calls');
            $table->unsignedInteger('black_screen_non_friend_calls')->default(0)->after('fake_user_in_screen_non_friend_calls');
            $table->unsignedInteger('no_user_in_screen_non_friend_calls')->default(0)->after('black_screen_non_friend_calls');
            $table->unsignedInteger('male_in_screen_non_friend_calls')->default(0)->after('no_user_in_screen_non_friend_calls');
            $table->unsignedInteger('underage_person_in_screen_all_calls')->default(0)->after('male_in_screen_non_friend_calls');
            $table->unsignedInteger('nudity_sexual_behavior_friend_calls')->default(0)->after('underage_person_in_screen_all_calls');
            $table->unsignedInteger('nudity_sexual_dress_friend_calls')->default(0)->after('nudity_sexual_behavior_friend_calls');
            $table->dateTime('snapshots_time')->nullable()->after('nudity_sexual_dress_friend_calls');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn([
                'nudity_sexual_behavior_non_friend_calls',
                'nudity_sexual_dress_non_friend_calls',
                'fake_user_in_screen_non_friend_calls',
                'black_screen_non_friend_calls',
                'no_user_in_screen_non_friend_calls',
                'male_in_screen_non_friend_calls',
                'underage_person_in_screen_all_calls',
                'nudity_sexual_behavior_friend_calls',
                'nudity_sexual_dress_friend_calls',
                'snapshots_time',
            ]);
        });
    }
};
