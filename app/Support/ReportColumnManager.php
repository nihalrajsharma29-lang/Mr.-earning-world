<?php

namespace App\Support;

use App\Models\ReportColumn;

class ReportColumnManager
{
    public static function defaults(string $reportType): array
    {
        return match ($reportType) {
            'payment_report' => PaymentReportColumns::definitions(),
            'violation_records' => ViolationReportColumns::definitions(),
            default => DailyReportColumns::definitions(),
        };
    }

    public static function all(): array
    {
        return [
            'daily_report' => self::defaults('daily_report'),
            'payment_report' => self::defaults('payment_report'),
            'violation_records' => self::defaults('violation_records'),
        ];
    }

    public static function visible(string $reportType): array
    {
        $savedQuery = ReportColumn::where('report_type', $reportType);
        $saved = (clone $savedQuery)
            ->where('is_visible', true)
            ->orderBy('position')
            ->get()
            ->keyBy('column_key');

        if (! $savedQuery->exists()) {
            return self::defaults($reportType);
        }

        $columns = $saved->reject(fn (ReportColumn $column) => $reportType === 'payment_report' && $column->column_key === 'weekly_date')
            ->map(fn (ReportColumn $column) => [
            'key' => $column->column_key,
            'label' => $column->label,
            'type' => $column->type,
        ])->sortBy(fn (array $column) => $saved[$column['key']]->position)
            ->values()
            ->all();

        return $columns;
    }
}