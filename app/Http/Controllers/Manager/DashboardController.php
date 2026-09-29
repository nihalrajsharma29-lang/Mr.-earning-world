<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Manager\BaseController;
use App\Models\DailyReport;
use App\Models\Customer;
use App\Models\ReportType;
use App\Models\SkippedImportId;

class DashboardController extends BaseController
{
    public function index()
    {
        $dailyReports = DailyReport::where('report_type', 'daily_report')->count();
        $paymentReports = DailyReport::where('report_type', 'payment_report')->count();
        $violationReports = DailyReport::where('report_type', 'violation_records')->count();
        $totalHosts = Customer::count();
        $pendingHosts = Customer::where('approval_status', 'pending')->count();
        $skippedHostIds = SkippedImportId::query()->distinct('host_id')->count('host_id');
        $reportTypes = ReportType::active()->orderBy('name')->get();
        $reportCounts = DailyReport::query()
            ->selectRaw('report_type, count(*) as report_count')
            ->groupBy('report_type')
            ->pluck('report_count', 'report_type');
        $dailyReportDates = DailyReport::query()
            ->where('report_type', 'daily_report')
            ->whereNotNull('dt')
            ->select('dt')
            ->distinct()
            ->orderByDesc('dt')
            ->pluck('dt');

        return view('manager.dashboard', compact(
            'dailyReports',
            'paymentReports',
            'violationReports',
            'totalHosts',
            'pendingHosts',
            'skippedHostIds',
            'dailyReportDates',
            'reportTypes',
            'reportCounts'
        ));
    }
}
