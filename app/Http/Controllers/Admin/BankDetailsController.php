<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BankDetailsExport;
use App\Models\Client;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BankDetailsController extends BaseController
{
    public function index()
    {
        [$clients, $weeklyDate] = $this->bankDetailsClients();

        return view('admin.bank-details.index', compact('clients', 'weeklyDate'));
    }

    public function export()
    {
        [$clients] = $this->bankDetailsClients();

        return Excel::download(
            new BankDetailsExport($clients),
            'bank-details-'.now()->format('Ymd_His').'.xlsx'
        );
    }

    private function bankDetailsClients(): array
    {
        $weeklyDate = DailyReport::query()
            ->where('report_type', 'payment_report')
            ->whereNotNull('weekly_date')
            ->max('weekly_date');

        $weeklyDate = $weeklyDate
            ? Carbon::parse($weeklyDate)->toDateString()
            : null;

        $clients = Client::query()
            ->where(function ($query) {
                $query->whereNotNull('bank_account_number')
                    ->orWhereNotNull('bank_ifsc_code')
                    ->orWhereNotNull('bank_name')
                    ->orWhereNotNull('bank_address');
            })
            ->latest()
            ->get();

        $paymentReports = DailyReport::query()
            ->with('client')
            ->where('report_type', 'payment_report')
            ->when($weeklyDate, fn ($query) => $query->whereDate('weekly_date', $weeklyDate))
            ->get()
            ->groupBy('client_id');

        $clients->each(function (Client $client) use ($paymentReports, $weeklyDate): void {
            $clientReports = $paymentReports->get($client->id, collect());
            $client->setAttribute('payment_weekly_date', $weeklyDate);
            $client->setAttribute('total_agent_salary', round($clientReports->sum(function (DailyReport $report): float {
                $agentFee = (float) ($report->getRawOriginal('agent_fee_usd') ?? 0);
                if ($agentFee <= 0) {
                    $agentFee = (float) ($report->weekly_reward_base_usd_hosts ?? 0)
                        * ((float) ($report->client?->commission_percentage ?? 0) / 100);
                }

                return $agentFee + (float) ($report->agent_one_time_bonus_usd ?? 0);
            }), 2));
        });

        return [$clients, $weeklyDate];
    }

    public function updateTransferStatus(Request $request, Client $client)
    {
        $validated = $request->validate([
            'transfer_status' => ['required', 'string', 'in:pending,transferred'],
        ]);

        $client->update(['transfer_status' => $validated['transfer_status']]);

        return redirect()->route('admin.bank-details')
            ->with('success', 'Transfer status updated for '.$client->name.'.');
    }

    public function destroy(Client $client)
    {
        $client->update([
            'bank_account_number' => null,
            'bank_ifsc_code' => null,
            'bank_name' => null,
            'bank_address' => null,
        ]);

        return redirect()->route('admin.bank-details')
            ->with('success', 'Bank details removed for ' . $client->name . '.');
    }
}
