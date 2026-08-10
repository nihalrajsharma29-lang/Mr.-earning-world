<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Imports\DailyReportImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportImportController extends BaseController
{
    /**
     * Show Admin Daily Report Import page.
     */
    public function create()
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.daily-reports.import');
    }

    /**
     * Import Daily Reports from Excel.
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        // TEMPORARY ZIP DEBUG
        

        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:20480',
            ],
        ]);

        try {
            Excel::import(
                new DailyReportImport,
                $request->file('file')
            );

            return redirect()
                ->route('admin.daily.import')
                ->with(
                    'success',
                    'Daily Reports imported successfully.'
                );

        } catch (\Throwable $e) {

            return redirect()
                ->route('admin.daily.import')
                ->with(
                    'error',
                    'Import failed: ' . $e->getMessage()
                );
        }
    }
}