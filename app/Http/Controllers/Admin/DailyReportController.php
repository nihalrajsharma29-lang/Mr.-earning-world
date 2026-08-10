<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Exports\AdminDailyReportsExport;
use App\Models\DailyReport;
use Illuminate\Http\Request;

class DailyReportController extends BaseController
{
    /**
     * Display all daily reports for admin.
     */
    public function index(Request $request)
    {
        $query = DailyReport::with(['customer', 'client']);

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
            $export = new AdminDailyReportsExport($query);

            return $export->download(
                'admin-daily-reports_'.now()->format('Ymd_His').'.xlsx'
            );
        }

        $reports = $query
            ->paginate(50)
            ->withQueryString();

        return view(
            'admin.clients.daily-reports.index',
            compact('reports')
        );
    }


    /**
     * Delete all reports of a selected date.
     */
    public function destroyByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;

        $deleted = DailyReport::whereDate('dt', $date)->delete();

        return redirect()
            ->route('admin.reports', ['date' => $date])
            ->with(
                'success',
                "{$deleted} daily report(s) deleted for {$date}."
            );
    }
}