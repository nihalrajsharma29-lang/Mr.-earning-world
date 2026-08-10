<?php

namespace App\Exports;

use App\Models\DailyReport;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AdminDailyReportsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    private Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query->latest('dt');
    }

    public function query()
    {
        return $this->query;
    }

    public function map($report): array
    {
        return [
            $report->dt?->format('Y-m-d') ?? '',
            $report->host_id,
            trim(($report->client?->name ?? '') . ' / ' . ($report->client_id ?? '')),
            $report->user_name,
            $report->story_status,
            $report->gift_coins,
            $report->non_friend_video_coins,
            $report->friend_video_coins,
            $report->task_coins,
            $report->box_coins,
            $report->total_coins,
            $report->group_time?->format('Y-m-d H:i:s') ?? '',
            $report->match_count,
            $report->match_duration_min,
            $report->app_kyc_pass,
            $report->profile_video_status,
            $report->category,
            $report->long_call_ratio,
            $report->avg_friend_call_duration_s30d,
            $report->total_call_duration_m,
            $report->bank_country,
            $report->if_bind_bank_info,
            $report->if_active,
            $report->current_week_total_coins,
            $report->previous_week1_total_coins,
            $report->previous_week2_total_coins,
            $report->previous_week3_total_coins,
            $report->payment_platform,
            $report->app_id,
            $report->has_live_permission ? 'Yes' : 'No',
            $report->start_live_duration_min,
            $report->live_to_call_ratio,
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Host ID',
            'Client Name / UID',
            'Username',
            'Story Status',
            'Gift Coins',
            'Non-Friend Video Coins',
            'Friend Video Coins',
            'Task Coins',
            'Box Coins',
            'Total Coins',
            'Group Time',
            'Match Count',
            'Match Duration (Min)',
            'App KYC Pass',
            'Profile Video Status',
            'Category',
            'Long Call Ratio',
            'Avg. Friend Call Duration (30D)',
            'Total Call Duration (Min)',
            'Bank Country',
            'Bank Info Bound',
            'Active Status',
            'Current Week Total Coins',
            'Previous Week 1 Total Coins',
            'Previous Week 2 Total Coins',
            'Previous Week 3 Total Coins',
            'Payment Platform',
            'App ID',
            'Live Permission',
            'Start Live Duration (Min)',
            'Live-to-Call Ratio',
        ];
    }
}
