<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminAuditLog;
use App\Models\DailyReport;
use App\Models\ReportColumn;
use App\Models\ReportImportName;
use App\Models\ReportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ReportTypeController extends BaseController
{
    public function index()
    {
        return view('admin.report-types.index', [
            'reportTypes' => ReportType::orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $slug = Str::of($validated['name'])->slug('_')->limit(100, '')->value();

        if ($slug === '') {
            return back()->withErrors(['name' => 'Enter a page name containing letters or numbers.'])->withInput();
        }

        if (ReportType::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'A report page with this name already exists.'])->withInput();
        }

        DB::transaction(function () use ($validated, $slug): void {
            ReportType::create([
                'slug' => $slug,
                'name' => $validated['name'],
                'is_active' => true,
                'is_system' => false,
            ]);

            foreach ([
                ['dt', 'Date', 'date'],
                ['host_id', 'Host ID', 'text'],
                ['client_name_uid', 'Client Name / UID', 'text'],
                ['user_name', 'Username', 'text'],
            ] as $position => [$key, $label, $type]) {
                ReportColumn::create([
                    'report_type' => $slug,
                    'column_key' => $key,
                    'label' => $label,
                    'type' => $type,
                    'position' => $position,
                    'is_visible' => true,
                ]);
            }
        });

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'create_report_type',
            'details' => auth()->user()->name . ' created report page ' . $validated['name'] . '.',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.report-types.index')->with('success', 'Report page created. Configure its Excel columns in Report Columns.');
    }

    public function update(Request $request, ReportType $reportType)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('report_types', 'name')->ignore($reportType->id),
            ],
        ]);

        $oldName = $reportType->name;
        $reportType->update(['name' => $validated['name']]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'rename_report_type',
            'details' => auth()->user()->name . ' renamed report page ' . $oldName . ' to ' . $reportType->name . '.',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.report-types.index')->with('success', 'Report page name updated.');
    }

    public function destroy(ReportType $reportType)
    {
        if (! $reportType->is_active) {
            return redirect()->route('admin.report-types.index');
        }

        if (ReportType::active()->count() <= 1) {
            return back()->withErrors(['report_type' => 'At least one report page must remain active.']);
        }

        $reportType->update(['is_active' => false]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'archive_report_type',
            'details' => auth()->user()->name . ' removed report page ' . $reportType->name . '. Imported reports were retained.',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.report-types.index')->with('success', 'Report page removed. Its imported data and column settings were retained.');
    }

    public function restore(ReportType $reportType)
    {
        $reportType->update(['is_active' => true]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'restore_report_type',
            'details' => auth()->user()->name . ' restored report page ' . $reportType->name . '.',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.report-types.index')->with('success', 'Report page restored.');
    }

    public function forceDestroy(ReportType $reportType)
    {
        if ($reportType->is_active) {
            return back()->withErrors(['report_type' => 'Remove the report page before permanently deleting it.']);
        }

        $name = $reportType->name;

        DB::transaction(function () use ($reportType, $name): void {
            DailyReport::where('report_type', $reportType->slug)->delete();
            ReportColumn::where('report_type', $reportType->slug)->delete();
            ReportImportName::where('report_type', $reportType->slug)->delete();

            AdminAuditLog::create([
                'admin_id' => auth()->id(),
                'action' => 'permanently_delete_report_type',
                'details' => auth()->user()->name . ' permanently deleted report page ' . $name . ' and its imported data.',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $reportType->delete();
        });

        return redirect()->route('admin.report-types.index')->with('success', 'Report page, imported reports, columns, and filename settings were permanently deleted.');
    }
}