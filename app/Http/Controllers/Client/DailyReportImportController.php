<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Client\BaseController;
use App\Imports\DailyReportImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as ExcelReader;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

        if (! $this->hasDailyReportDateHeader($request->file('file'))) {
            return redirect()
                ->route('client.daily.import')
                ->with(
                    'error',
                    'Invalid Daily Report file: first row must contain a Date column (Date / DT / Report Date).'
                );
        }

        try {

            $import = new DailyReportImport(
                auth()->user()->client?->id,
                'daily_report',
                null,
                $request->file('file')->getClientOriginalName(),
                auth()->id()
            );

            Excel::import(
                $import,
                $request->file('file'),
                null,
                $this->readerType($request->file('file'))
            );

            $imported = $import->getImportedRows();
            $skipped = $import->getSkippedRows();
            $skippedUnknownHost = $import->getSkippedUnknownHostRows();

            $message = "Daily Reports imported. Imported: {$imported}, Skipped: {$skipped}.";

            if ($skippedUnknownHost > 0) {
                $message .= " Unknown host IDs skipped: {$skippedUnknownHost}.";
            }

            return redirect()
                ->route('client.daily.import')
                ->with(
                    'success',
                    $message
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

    private function readerType(UploadedFile $file): string
    {
        return match (strtolower($file->getClientOriginalExtension())) {
            'xlsx' => ExcelReader::XLSX,
            'xls' => ExcelReader::XLS,
            'csv' => ExcelReader::CSV,
            default => throw new \InvalidArgumentException('Unsupported import file type.'),
        };
    }

    private function normalizeHeader(string $value): string
    {
        return strtolower((string) preg_replace('/[^a-z0-9]+/', '', trim($value)));
    }
}