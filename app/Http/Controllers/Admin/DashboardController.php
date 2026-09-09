<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\DailyReport;
use App\Models\Client;
use App\Models\Customer;
use App\Models\ReportImportName;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends BaseController
{
    public function index()
    {
        $totalClients = Client::count();
        $totalHosts = Customer::count();
        $pendingHosts = Customer::where('approval_status', 'pending')->count();
        $totalReports = DailyReport::count();
        $totalSalary = DailyReport::sum('salary_amount');
        $violationReports = DailyReport::whereNotNull('violation_records')
            ->where('violation_records', '<>', '')
            ->count();
        $dailyReportDates = DailyReport::query()
            ->where('report_type', 'daily_report')
            ->whereNotNull('dt')
            ->select('dt')
            ->distinct()
            ->orderByDesc('dt')
            ->pluck('dt');
        $skippedHostIds = \App\Models\SkippedImportId::query()
            ->distinct('host_id')
            ->count('host_id');

        return view('admin.dashboard', compact(
            'totalClients',
            'totalHosts',
            'pendingHosts',
            'totalReports',
            'totalSalary',
            'violationReports',
            'dailyReportDates',
            'skippedHostIds'
        ));
    }

    public function reportImportNames()
    {
        return view('admin.report-import-names', [
            'names' => ReportImportName::values(),
        ]);
    }

    public function saveReportImportNames(Request $request)
    {
        $request->validate([
            'daily_report' => ['nullable', 'string'],
            'payment_report' => ['nullable', 'string'],
            'violation_records' => ['nullable', 'string'],
        ]);

        $config = [
            'daily_report' => trim((string) $request->input('daily_report', '4280121896')),
            'payment_report' => trim((string) $request->input('payment_report', 'Payment Report')),
            'violation_records' => trim((string) $request->input('violation_records', 'Strike Records')),
        ];

        foreach ($config as $key => $value) {
            if ($value === '') {
                $config[$key] = null;
            }
        }

        foreach ($config as $key => $value) {
            ReportImportName::updateOrCreate(
                ['report_type' => $key],
                ['name' => $value]
            );
        }

        return redirect()->route('admin.report-import-names')->with('success', 'Report file names updated successfully.');
    }
}