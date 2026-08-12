<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Imports\DailyReportImport;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DailyReportImportController extends BaseController
{
    public function __construct()
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role, ['admin', 'manager'], true),
            403
        );
    }

    /**
     * Show Admin Daily Report Import page.
     */
    public function create()
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'manager'], true), 403);

        return view(auth()->user()->role === 'manager'
            ? 'manager.daily-reports.import'
            : 'admin.daily-reports.import');
    }

    /**
     * Import Daily Reports from Excel.
     */
    public function store(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'manager'], true), 403);

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
                'in:daily_report,payment_report,violation_records',
            ],
        ]);

        $fileName = $request->file('file')->getClientOriginalName();
        $requiredToken = $this->requiredFileNameToken($request->report_type);

        if ($requiredToken && stripos($fileName, $requiredToken) === false) {
            return redirect()
                ->route(auth()->user()->role === 'manager' ? 'manager.daily.import' : 'admin.daily.import')
                ->with(
                    'error',
                    "Invalid file name for "
                    . ucwords(str_replace('_', ' ', $request->report_type))
                    . ": file name must contain '{$requiredToken}'."
                );
        }

        if (
            $request->report_type === 'daily_report' &&
            ! $this->hasDailyReportDateHeader($request->file('file'))
        ) {
            return redirect()
                ->route(auth()->user()->role === 'manager' ? 'manager.daily.import' : 'admin.daily.import')
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
            $skippedUnknownHost = $import->getSkippedUnknownHostRows();

            $message = 'Reports imported successfully for '
                . ucwords(str_replace('_', ' ', $request->report_type))
                . ". Imported: {$imported}, Skipped: {$skipped}.";

            if ($skippedUnknownHost > 0) {
                $message .= " Unknown host IDs skipped: {$skippedUnknownHost}.";
            }

            AdminAuditLog::create([
                'admin_id' => auth()->id(),
                'action' => 'import_report',
                'details' => sprintf(
                    '%s imported %s file "%s". Imported: %d, Skipped: %d, Unknown host skipped: %d.',
                    auth()->user()->name,
                    ucwords(str_replace('_', ' ', $request->report_type)),
                    $fileName,
                    $imported,
                    $skipped,
                    $skippedUnknownHost
                ),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()
                ->route(auth()->user()->role === 'manager' ? 'manager.daily.import' : 'admin.daily.import')
                ->with(
                    'success',
                    $message
                );

        } catch (\Throwable $e) {

            return redirect()
                ->route(auth()->user()->role === 'manager' ? 'manager.daily.import' : 'admin.daily.import')
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

    private function requiredFileNameToken(string $reportType): ?string
    {
        return match ($reportType) {
            'daily_report' => '4280121896',
            'payment_report' => 'Payment Report',
            'violation_records' => 'Strike Records',
            default => null,
        };
    }
}