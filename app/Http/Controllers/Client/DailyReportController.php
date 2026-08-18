<?php

namespace App\Http\Controllers\Client;

use App\Exports\AdminDailyReportsExport;
use App\Http\Controllers\Client\BaseController;
use App\Models\DailyReport;
use App\Support\ReportColumnManager;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DailyReportController extends BaseController
{
    /**
     * Display client's daily reports.
     */
    public function index(Request $request)
    {
        // User login hona chahiye
        abort_unless(auth()->check(), 403);

        // Sirf client role allowed hai
        abort_unless(auth()->user()->role === 'client', 403);

        // Logged-in user ka client profile
        $client = auth()->user()->client;

        // Client profile nahi mila to access deny
        abort_unless($client, 403);

        /*
        |--------------------------------------------------------------------------
        | Daily Reports Query
        |--------------------------------------------------------------------------
        */

        $reportType = $request->input('report_type', 'daily_report');

        $query = DailyReport::with(['customer', 'client'])
            ->where('client_id', $client->id)
            ->where('report_type', $reportType);

        $weeklyDate = null;
        if ($reportType === 'payment_report') {
            $weeklyDate = (clone $query)
                ->whereNotNull('weekly_date')
                ->latest('weekly_date')
                ->value('weekly_date');
        }

        $availableColumns = ReportColumnManager::visible($reportType);
        $allowedColumnKeys = collect($availableColumns)->pluck('key')->all();
        $defaultSortColumn = match ($reportType) {
            'payment_report' => 'weekly_final_coins_hosts',
            'daily_report' => 'total_coins',
            default => null,
        };
        $defaultSortColumn = in_array($defaultSortColumn, $allowedColumnKeys, true)
            ? $defaultSortColumn
            : null;

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if ($reportType !== 'payment_report' && $request->filled('date')) {
            $query->whereDate('dt', $request->date);
        }

        if ($request->filled('host_id')) {
            $query->where('host_id', 'like', '%'.$request->host_id.'%');
        }

        if ($request->filled('salary_status')) {
            $query->where('salary_status', 'like', '%'.$request->salary_status.'%');
        }

        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        |
        | User Name
        | Customer Name
        | Customer Username
        | Customer ID
        |
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search, $reportType) {
                if ($reportType === 'payment_report') {
                    $q->where('host_id', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('customer_id', 'like', "%{$search}%");
                        });
                } elseif ($reportType === 'violation_records') {
                    $q->where('host_id', 'like', "%{$search}%")
                        ->orWhere('user_name', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('customer_id', 'like', "%{$search}%");
                        })
                        ->orWhereHas('client', function ($clientQuery) use ($search) {
                            $clientQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('id', 'like', "%{$search}%");
                        });
                } else {
                    $q->where('user_name', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('customer_id', 'like', "%{$search}%");
                        });
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Export
        |--------------------------------------------------------------------------
        */

        if ($request->filled('export')) {
            $export = new AdminDailyReportsExport($query, $reportType, ReportColumnManager::visible($reportType));

            return $export->download(
                'client-daily-reports_'.now()->format('Ymd_His').'.xlsx'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        $reports = $query
            ->latest('dt')
            ->get();

        if ($reportType === 'payment_report' && ! $weeklyDate) {
            $weeklyDate = $reports
                ->first(fn ($report) => $report->weekly_date !== null)
                ?->weekly_date
                ?->toDateString();
        }

        $filterColumn = in_array($request->input('filter_column'), $allowedColumnKeys, true)
            ? $request->input('filter_column')
            : null;
        $filterValue = trim((string) $request->input('filter_value', ''));

        if ($filterColumn && $filterValue !== '') {
            $reports = $reports->filter(function ($report) use ($filterColumn, $filterValue) {
                $value = $filterColumn === 'client_name_uid'
                    ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                    : $report->columnValue($filterColumn);

                return str_contains(strtolower((string) $value), strtolower($filterValue));
            })->values();
        }

        $totalHostCount = $reportType === 'daily_report'
            ? $reports->whereNotNull('host_id')->unique('host_id')->count()
            : 0;
        $workingHostCount = $reportType === 'daily_report'
            ? $reports
                ->filter(fn ($report) => (float) ($report->total_coins ?? 0) > 0)
                ->whereNotNull('host_id')
                ->unique('host_id')
                ->count()
            : 0;

        $paymentSummary = null;
        if ($reportType === 'payment_report') {
            $agentFeeTotal = (float) $reports->sum(fn ($report) => (float) ($report->agent_fee_usd ?? 0));
            $agentOneTimeBonusTotal = (float) $reports->sum(fn ($report) => (float) ($report->agent_one_time_bonus_usd ?? 0));

            $paymentSummary = [
                'total_host_salary' => (float) $reports->sum(fn ($report) => (float) ($report->hosts_final_reward_usd ?? 0)),
                'agent_fee_total' => $agentFeeTotal,
                'agent_one_time_bonus_total' => $agentOneTimeBonusTotal,
                'total_salary' => $agentFeeTotal + $agentOneTimeBonusTotal,
            ];
        }

        $sortColumn = in_array($request->input('sort_column', $defaultSortColumn), $allowedColumnKeys, true)
            ? $request->input('sort_column', $defaultSortColumn)
            : $defaultSortColumn;
        $sortDirection = $request->input('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        if ($sortColumn) {
            $reports = $reports->sortBy(function ($report) use ($sortColumn) {
                return $sortColumn === 'client_name_uid'
                    ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                    : $report->columnValue($sortColumn);
            }, SORT_NATURAL | SORT_FLAG_CASE, $sortDirection === 'desc')->values();
        }

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        $paymentReportColumns = $reportType === 'payment_report'
            ? ReportColumnManager::visible('payment_report')
            : [];

        $violationReportColumns = $reportType === 'violation_records'
            ? ReportColumnManager::visible('violation_records')
            : [];

        $dailyReportColumns = $reportType === 'daily_report'
            ? ReportColumnManager::visible('daily_report')
            : [];

        return view(
            'client.daily-reports.index',
            compact('reports', 'client', 'reportType', 'weeklyDate', 'totalHostCount', 'workingHostCount', 'paymentReportColumns', 'paymentSummary', 'violationReportColumns', 'dailyReportColumns', 'defaultSortColumn')
        );
    }
}