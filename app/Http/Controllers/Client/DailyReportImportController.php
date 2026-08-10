<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Client\BaseController;
use App\Imports\DailyReportImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportImportController extends BaseController
{
    /**
     * Show Excel import page.
     */
    public function create()
    {
        return view('client.reports.import');
    }

    /**
     * Import Daily Reports from Excel.
     */
    public function store(Request $request)
    {
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
                new DailyReportImport(
                    auth()->user()->client?->id
                ),
                $request->file('file')
            );

            return redirect()
                ->route('client.daily.import')
                ->with(
                    'success',
                    'Daily Reports imported successfully.'
                );

        } catch (\Throwable $e) {

            return redirect()
                ->route('client.daily.import')
                ->with(
                    'error',
                    'Import failed: ' . $e->getMessage()
                );
        }
    }
}