<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;

class BankDetailsController extends BaseController
{
    public function index()
    {
        $clients = Client::query()
            ->where(function ($query) {
                $query->whereNotNull('bank_account_number')
                    ->orWhereNotNull('bank_ifsc_code')
                    ->orWhereNotNull('bank_name')
                    ->orWhereNotNull('bank_address');
            })
            ->latest()
            ->get();

        return view('admin.bank-details.index', compact('clients'));
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
