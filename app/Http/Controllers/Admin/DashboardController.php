<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\DailyReport;
use App\Models\Client;
use App\Models\Customer;
use App\Models\ReportImportName;
use App\Models\ReportType;
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
            'reportTypes' => ReportType::active()->orderBy('name')->get(),
        ]);
    }

    public function saveReportImportNames(Request $request)
    {
        $validated = $request->validate([
            'report_names' => ['nullable', 'array'],
            'report_names.*' => ['nullable', 'string', 'max:150'],
            'daily_report' => ['nullable', 'string', 'max:150'],
            'payment_report' => ['nullable', 'string', 'max:150'],
            'violation_records' => ['nullable', 'string', 'max:150'],
        ]);

        foreach (ReportType::active()->get() as $reportType) {
            $value = trim((string) ($validated['report_names'][$reportType->slug]
                ?? $validated[$reportType->slug]
                ?? ''));

            ReportImportName::updateOrCreate(
                ['report_type' => $reportType->slug],
                ['name' => $value !== '' ? $value : null]
            );
        }

        return redirect()->route('admin.report-import-names')->with('success', 'Report file names updated successfully.');
    }
}