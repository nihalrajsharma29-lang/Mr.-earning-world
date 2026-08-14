<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\DailyReport;
use App\Models\Client;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function index()
    {
        $totalClients = Client::count();
        $totalReports = DailyReport::count();
        $totalSalary = DailyReport::sum('salary_amount');
        $violationReports = DailyReport::whereNotNull('violation_records')
            ->where('violation_records', '<>', '')
            ->count();

        return view('admin.dashboard', compact(
            'totalClients',
            'totalReports',
            'totalSalary',
            'violationReports'
        ));
    }

    public function reportImportNames()
    {
        return view('admin.report-import-names', [
            'names' => config('report_import_names', [
                'daily_report' => '4280121896',
                'payment_report' => 'Payment Report',
                'violation_records' => 'Strike Records',
            ]),
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

        $path = config_path('report_import_names.php');
        $content = "<?php\n\nreturn [\n";

        foreach ($config as $key => $value) {
            $content .= "    '{$key}' => " . (is_null($value) ? 'null' : "'" . addslashes($value) . "'") . ",\n";
        }

        $content .= "];\n";

        file_put_contents($path, $content);

        return redirect()->route('admin.report-import-names')->with('success', 'Report file names updated successfully.');
    }
}