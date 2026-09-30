<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Client\BaseController;
use App\Models\Customer;
use App\Models\DailyReport;
use App\Models\ReportType;

class DashboardController extends BaseController
{
    public function index()
    {
        $client = auth()->user()->client;
        $clientId = $client?->id;

        $totalHosts = $clientId ? Customer::where('client_id', $clientId)->count() : 0;
        $pendingHosts = $clientId
            ? Customer::where('client_id', $clientId)->where('approval_status', 'pending')->count()
            : 0;
        $approvedHosts = $clientId
            ? Customer::where('client_id', $clientId)->where('approval_status', 'approved')->count()
            : 0;
        $hosts = $clientId
            ? Customer::where('client_id', $clientId)
                ->latest()
                ->paginate(10, ['customer_id', 'country', 'approval_status', 'created_at'])
                ->withQueryString()
            : Customer::whereRaw('1 = 0')->paginate(10, ['customer_id', 'country', 'approval_status', 'created_at']);
        $reportTypes = ReportType::active()->orderBy('name')->get();
        $reportCounts = $clientId
            ? DailyReport::where('client_id', $clientId)
                ->selectRaw('report_type, count(*) as report_count')
                ->groupBy('report_type')
                ->pluck('report_count', 'report_type')
            : collect();

        return view('client.dashboard', compact(
            'totalHosts',
            'pendingHosts',
            'approvedHosts',
            'hosts',
            'reportTypes',
            'reportCounts'
        ));
    }
}