<?php

namespace App\Http\Controllers\Client;

use App\Exports\AdminDailyReportsExport;
use App\Http\Controllers\Client\BaseController;
use App\Models\DailyReport;
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

        $query = DailyReport::with('customer')
            ->where('client_id', $client->id);

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
        |
        | Host ID
        | User Name
        | Customer Name
        | Customer Username
        | Customer ID
        |
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
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Export
        |--------------------------------------------------------------------------
        */

        if ($request->filled('export')) {
            $export = new AdminDailyReportsExport($query);

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
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'client.daily-reports.index',
            compact('reports', 'client')
        );
    }
}