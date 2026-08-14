<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BankDetailsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private readonly Collection $clients)
    {
    }

    public function collection(): Collection
    {
        return $this->clients;
    }

    public function headings(): array
    {
        return [
            'Weekly Date',
            'Client',
            'Account Holder Name',
            'Account Number',
            'IFSC',
            'Bank Name',
            'Address',
            'Total Agent Salary',
            'Transfer Status',
        ];
    }

    public function map($client): array
    {
        $weeklyDate = $client->payment_weekly_date
            ? \Illuminate\Support\Carbon::parse($client->payment_weekly_date)
            : null;

        return [
            $weeklyDate ? $weeklyDate->format('d-M-Y').' to '.$weeklyDate->copy()->addDays(6)->format('d-M-Y') : '',
            $client->name,
            $client->bank_account_holder_name ?? '',
            $client->bank_account_number ?? '',
            $client->bank_ifsc_code ?? '',
            $client->bank_name ?? '',
            $client->bank_address ?? '',
            number_format((float) ($client->total_agent_salary ?? 0), 2, '.', ''),
            ucfirst($client->transfer_status ?? 'pending'),
        ];
    }
}