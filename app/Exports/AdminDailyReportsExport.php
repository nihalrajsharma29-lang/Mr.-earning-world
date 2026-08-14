<?php

namespace App\Exports;

use App\Models\DailyReport;
use App\Support\ReportColumnManager;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AdminDailyReportsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    private Builder $query;
    private string $reportType;
    private array $columns;

    public function __construct(Builder $query, string $reportType = 'daily_report', ?array $columns = null)
    {
        $this->query = $query->latest('dt');
        $this->reportType = $reportType;
        $this->columns = $columns ?? ReportColumnManager::visible($reportType);
    }

    public function query()
    {
        return $this->query;
    }

    public function map($report): array
    {
        if ($this->reportType === 'payment_report') {
            return collect($this->columns)
                ->map(function (array $column) use ($report) {
                    $value = $column['key'] === 'client_name_uid'
                        ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                        : $report->columnValue($column['key']);

                    if ($column['key'] === 'group_time') {
                        return $report->group_time?->format('Y-m-d H:i:s') ?? '';
                    }

                    return $value ?? '';
                })
                ->all();
        }

        if ($this->reportType === 'violation_records') {
            return collect($this->columns)
                ->map(function (array $column) use ($report) {
                    $value = $column['key'] === 'client_name_uid'
                        ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                        : $report->columnValue($column['key']);

                    if ($column['key'] === 'snapshots_time') {
                        return $report->snapshots_time?->format('Y-m-d H:i:s') ?? '';
                    }

                    return $value ?? '';
                })
                ->all();
        }

        return collect($this->columns)->map(fn (array $column) => $this->value($report, $column))->all();
    }

    public function headings(): array
    {
        if ($this->reportType === 'payment_report') {
            return collect($this->columns)->pluck('label')->all();
        }

        if ($this->reportType === 'violation_records') {
            return collect($this->columns)->pluck('label')->all();
        }

        return collect($this->columns)->pluck('label')->all();
    }

    private function value($report, array $column): mixed
    {
        if ($column['key'] === 'client_name_uid') {
            return trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'));
        }

        $value = data_get($report, $column['key']);

        if (in_array($column['key'], ['dt', 'group_time', 'snapshots_time'], true)) {
            return $value?->format($column['key'] === 'dt' ? 'Y-m-d' : 'Y-m-d H:i:s') ?? '';
        }

        if ($column['key'] === 'has_live_permission') {
            return $value ? 'Yes' : 'No';
        }

        return $value ?? '';
    }
}
