<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Exports\AdminDailyReportsExport;
use App\Models\AdminAuditLog;
use App\Models\DailyReport;
use App\Support\ReportColumnManager;
use Illuminate\Http\Request;

class DailyReportController extends BaseController
{
    public function __construct()
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role, ['admin', 'manager'], true),
            403
        );
    }

    /**
     * Display all daily reports for admin.
     */
    public function index(Request $request)
    {
        $isManager = auth()->user()->role === 'manager';
        $reportType = $request->input('report_type', 'daily_report');

        $query = DailyReport::with(['customer', 'client'])
            ->where('report_type', $reportType);

        $availableColumns = ReportColumnManager::visible($reportType);
        $allowedColumnKeys = collect($availableColumns)->pluck('key')->all();

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date')) {
            $query->whereDate('dt', $request->date);
        }

        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('host_id', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {

                        $customerQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('customer_id', 'like', "%{$search}%");

                    })
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        if ($request->filled('export')) {
            $export = new AdminDailyReportsExport($query, $reportType, ReportColumnManager::visible($reportType));

            return $export->download(
                'admin-daily-reports_'.now()->format('Ymd_His').'.xlsx'
            );
        }

        $paymentSummary = null;
        if ($reportType === 'payment_report') {
            $totalHostFinalRewards = (float) (clone $query)->sum('hosts_final_reward_usd');
            $agentFeeTotal = (float) (clone $query)->sum('agent_fee_usd');
            $agentOneTimeBonusTotal = (float) (clone $query)->sum('agent_one_time_bonus_usd');
            $activeReportIds = (int) (clone $query)
                ->whereNotNull('host_id')
                ->distinct()
                ->count('host_id');

            $paymentSummary = [
                'total_host_final_rewards' => $totalHostFinalRewards,
                'agent_fee_total' => $agentFeeTotal,
                'agent_one_time_bonus_total' => $agentOneTimeBonusTotal,
                'total_salary' => $agentFeeTotal + $agentOneTimeBonusTotal,
                'active_report_ids' => $activeReportIds,
            ];
        }

        $reports = $query
            ->latest('dt')
            ->get();

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

        $sortColumn = in_array($request->input('sort_column'), $allowedColumnKeys, true)
            ? $request->input('sort_column')
            : null;
        $sortDirection = $request->input('sort_direction') === 'desc' ? 'desc' : 'asc';

        if ($sortColumn) {
            $reports = $reports->sortBy(function ($report) use ($sortColumn) {
                return $sortColumn === 'client_name_uid'
                    ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                    : $report->columnValue($sortColumn);
            }, SORT_NATURAL | SORT_FLAG_CASE, $sortDirection === 'desc')->values();
        }

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
            $isManager ? 'manager.reports.index' : 'admin.clients.daily-reports.index',
            compact('reports', 'reportType', 'paymentReportColumns', 'paymentSummary', 'violationReportColumns', 'dailyReportColumns')
        );
    }


    /**
     * Delete all reports of a selected date.
     */
    public function destroyByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'report_type' => 'nullable|string|in:daily_report,payment_report,violation_records',
        ]);

        $date = $request->date;
        $reportType = $request->report_type;

        $query = DailyReport::whereDate('dt', $date);

        if ($reportType) {
            $query->where('report_type', $reportType);
        }

        $deleted = $query->delete();

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_reports_by_date',
            'details' => sprintf(
                '%s deleted %d report(s) for date %s%s.',
                auth()->user()->name,
                $deleted,
                $date,
                $reportType ? ' in ' . ucwords(str_replace('_', ' ', $reportType)) : ''
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $routeName = auth()->user()->role === 'manager' ? 'manager.reports' : 'admin.reports';

        return redirect()
            ->route($routeName, ['date' => $date, 'report_type' => $reportType])
            ->with(
                'success',
                "{$deleted} report(s) deleted for {$date}."
            );
    }

    /**
     * Delete selected reports by row ids.
     */
    public function destroySelected(Request $request)
    {
        $validated = $request->validate([
            'report_ids' => 'required|array|min:1',
            'report_ids.*' => 'integer|exists:daily_reports,id',
            'report_type' => 'nullable|string|in:daily_report,payment_report,violation_records',
        ]);

        $reportType = $validated['report_type'] ?? 'payment_report';
        $reportIds = $validated['report_ids'];

        $deleted = DailyReport::query()
            ->whereIn('id', $reportIds)
            ->where('report_type', $reportType)
            ->delete();

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_selected_reports',
            'details' => sprintf(
                '%s deleted %d selected report(s) from %s.',
                auth()->user()->name,
                $deleted,
                ucwords(str_replace('_', ' ', $reportType))
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $routeName = auth()->user()->role === 'manager' ? 'manager.reports' : 'admin.reports';

        return redirect()
            ->route($routeName, ['report_type' => $reportType])
            ->with('success', "{$deleted} selected report(s) deleted.");
    }
}