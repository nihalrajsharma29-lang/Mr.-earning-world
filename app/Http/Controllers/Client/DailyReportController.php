<?php

namespace App\Http\Controllers\Client;

use App\Exports\AdminDailyReportsExport;
use App\Http\Controllers\Client\BaseController;
use App\Models\DailyReport;
use App\Support\PaymentReportColumns;
use Illuminate\Http\Request;

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
            $export = new AdminDailyReportsExport($query, $reportType);

            return $export->download(
                'client-daily-reports_'.now()->format('Ymd_His').'.xlsx'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        $paymentSummary = null;
        if ($reportType === 'payment_report') {
            $paymentSummary = [
                'total_host_salary' => (float) (clone $query)->sum('hosts_final_reward_usd'),
                'agent_fee_total' => (float) (clone $query)->sum('agent_fee_usd'),
                'agent_one_time_bonus_total' => (float) (clone $query)->sum('agent_one_time_bonus_usd'),
            ];
        }

        $reports = $query
            ->latest('dt')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        $paymentReportColumns = $reportType === 'payment_report'
            ? PaymentReportColumns::definitions()
            : [];

        return view(
            'client.daily-reports.index',
            compact('reports', 'client', 'reportType', 'paymentReportColumns', 'paymentSummary')
        );
    }
}