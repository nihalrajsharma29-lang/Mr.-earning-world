<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HostIdsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private readonly Builder $hosts)
    {
    }

    public function query(): Builder
    {
        return $this->hosts;
    }

    public function headings(): array
    {
        return ['Host ID', 'Client ID', 'Client Name'];
    }

    public function map($host): array
    {
        return [
            $host->customer_id ?? '',
            $host->client_id,
            $host->client?->name ?? '',
        ];
    }
}