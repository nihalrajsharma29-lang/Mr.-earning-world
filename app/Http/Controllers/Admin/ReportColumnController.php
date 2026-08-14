<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminAuditLog;
use App\Models\ReportColumn;
use App\Support\ReportColumnManager;
use Illuminate\Http\Request;

class ReportColumnController extends BaseController
{
    public function index()
    {
        return view('admin.report-columns.index', [
            'columnsByType' => ReportColumn::orderBy('report_type')->orderBy('position')->get()->groupBy('report_type'),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'visible' => ['nullable', 'array'],
            'labels' => ['nullable', 'array'],
            'labels.*' => ['nullable', 'array'],
            'labels.*.*' => ['nullable', 'string', 'max:150'],
            'source_keys' => ['nullable', 'array'],
            'source_keys.*' => ['nullable', 'string', 'max:150'],
            'types' => ['nullable', 'array'],
            'types.*' => ['nullable', 'in:text,integer,decimal,currency,datetime,date,boolean'],
            'positions' => ['nullable', 'array'],
            'positions.*' => ['nullable', 'integer', 'min:1'],
            'new_columns' => ['nullable', 'array'],
            'new_columns.*.label' => ['nullable', 'string', 'max:150'],
            'new_columns.*.key' => ['nullable', 'string', 'max:150'],
            'new_columns.*.type' => ['nullable', 'in:text,integer,decimal,currency,datetime,date,boolean'],
            'new_columns.*.position' => ['nullable', 'integer', 'min:1'],
        ]);

        $visible = $validated['visible'] ?? [];
        $labels = $validated['labels'] ?? [];

        foreach (ReportColumnManager::all() as $reportType => $columns) {
            $visibleKeys = $visible[$reportType] ?? [];

            $columns = ReportColumn::where('report_type', $reportType)->orderBy('position')->get()
                ->sortBy(fn (ReportColumn $column) => (int) ($validated['positions'][$column->id] ?? $column->position))
                ->values();

            foreach ($columns as $position => $column) {
                ReportColumn::where('report_type', $reportType)
                    ->whereKey($column->id)
                    ->update([
                        'position' => $position,
                        'label' => $labels[$reportType][$column->id] ?? $column->label,
                        'column_key' => $this->normalizeColumnKey($validated['source_keys'][$column->id] ?? $column->column_key),
                        'type' => $validated['types'][$column->id] ?? $column->type,
                        'is_visible' => in_array($column->column_key, $visibleKeys, true),
                    ]);
            }
        }

        foreach ($validated['new_columns'] ?? [] as $reportType => $newColumn) {
            if (empty($newColumn['label'])) {
                continue;
            }

            $newColumn['key'] = $this->normalizeColumnKey($newColumn['key'] ?? $newColumn['label']);

            $exists = ReportColumn::where('report_type', $reportType)
                ->where('column_key', $newColumn['key'])
                ->exists();

            if ($exists) {
                return back()->withErrors(['new_columns.' . $reportType . '.key' => 'This source key already exists for the selected report type.']);
            }

            $position = (int) ($newColumn['position'] ?? ReportColumn::where('report_type', $reportType)->count() + 1) - 1;
            $position = max(0, min($position, ReportColumn::where('report_type', $reportType)->count()));

            ReportColumn::where('report_type', $reportType)
                ->where('position', '>=', $position)
                ->increment('position');

            ReportColumn::create([
                'report_type' => $reportType,
                'column_key' => $newColumn['key'],
                'label' => $newColumn['label'],
                'type' => $newColumn['type'] ?? 'text',
                'position' => $position,
                'is_visible' => true,
            ]);
        }

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_report_columns',
            'details' => auth()->user()->name . ' updated report column visibility settings.',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.report-columns.index')->with('success', 'Report columns updated successfully.');
    }

    private function normalizeColumnKey(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized);

        return trim($normalized, '_');
    }

    public function destroy(ReportColumn $reportColumn)
    {
        $reportColumn->delete();

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_report_column',
            'details' => auth()->user()->name . ' deleted report column ' . $reportColumn->label . '.',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.report-columns.index')->with('success', 'Report column deleted successfully.');
    }
}