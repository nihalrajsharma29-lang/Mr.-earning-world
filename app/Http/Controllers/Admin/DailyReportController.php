<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Exports\AdminDailyReportsExport;
use App\Models\DailyReport;
use App\Support\PaymentReportColumns;
use App\Support\ViolationReportColumns;
use Illuminate\Http\Request;

class DailyReportController extends BaseController
{
    /**
     * Display all daily reports for admin.
     */
    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'daily_report');

        if ($reportType === 'payment_status') {
            return redirect()
                ->route('admin.reports', ['report_type' => 'daily_report'])
                ->with('error', 'Payment Status option will be opened very soon.');
        }

        $query = DailyReport::with(['customer', 'client'])
            ->where('report_type', $reportType);

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
            $export = new AdminDailyReportsExport($query, $reportType);

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

        $paymentReportColumns = $reportType === 'payment_report'
            ? PaymentReportColumns::definitions()
            : [];

        $violationReportColumns = $reportType === 'violation_records'
            ? ViolationReportColumns::definitions()
            : [];

        return view(
            'admin.clients.daily-reports.index',
            compact('reports', 'reportType', 'paymentReportColumns', 'paymentSummary', 'violationReportColumns')
        );
    }


    /**
     * Delete all reports of a selected date.
     */
    public function destroyByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'report_type' => 'nullable|string|in:daily_report,payment_report,payment_status,violation_records',
        ]);

        $date = $request->date;
        $reportType = $request->report_type;

        $query = DailyReport::whereDate('dt', $date);

        if ($reportType) {
            $query->where('report_type', $reportType);
        }

        $deleted = $query->delete();

        return redirect()
            ->route('admin.reports', ['date' => $date, 'report_type' => $reportType])
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
            'report_type' => 'nullable|string|in:daily_report,payment_report,payment_status,violation_records',
        ]);

        $reportType = $validated['report_type'] ?? 'payment_report';
        $reportIds = $validated['report_ids'];

        $deleted = DailyReport::query()
            ->whereIn('id', $reportIds)
            ->where('report_type', $reportType)
            ->delete();

        return redirect()
            ->route('admin.reports', ['report_type' => $reportType])
            ->with('success', "{$deleted} selected report(s) deleted.");
    }
}