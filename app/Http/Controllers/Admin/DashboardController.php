<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\DailyReport;
use App\Models\Client;

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

}