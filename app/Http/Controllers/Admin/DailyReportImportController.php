<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Imports\DailyReportImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
            'report_type' => [
                'required',
                'string',
                'in:daily_report,payment_report,payment_status,violation_records',
            ],
        ]);

        if (
            $request->report_type === 'daily_report' &&
            ! $this->hasDailyReportDateHeader($request->file('file'))
        ) {
            return redirect()
                ->route('admin.daily.import')
                ->with(
                    'error',
                    'Invalid Daily Report file: first row must contain a Date column (Date / DT / Report Date).'
                );
        }

        try {
            $import = new DailyReportImport(
                null,
                $request->report_type
            );

            Excel::import(
                $import,
                $request->file('file')
            );

            $imported = $import->getImportedRows();
            $skipped = $import->getSkippedRows();

            return redirect()
                ->route('admin.daily.import')
                ->with(
                    'success',
                    'Reports imported successfully for ' . ucwords(str_replace('_', ' ', $request->report_type)) . ". Imported: {$imported}, Skipped: {$skipped}."
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

    private function hasDailyReportDateHeader(UploadedFile $file): bool
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();

        $headerRow = $sheet
            ->rangeToArray("A1:{$highestColumn}1", null, true, true, false)[0] ?? [];

        $normalizedHeaders = collect($headerRow)
            ->map(fn ($value) => $this->normalizeHeader((string) $value))
            ->filter()
            ->all();

        $expected = ['date', 'dt', 'reportdate'];

        return count(array_intersect($expected, $normalizedHeaders)) > 0;
    }

    private function normalizeHeader(string $value): string
    {
        return strtolower((string) preg_replace('/[^a-z0-9]+/', '', trim($value)));
    }
}