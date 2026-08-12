<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;

class BankCardController extends BaseController
{
    public function index(Request $request)
    {
        $client = auth()->user()->client;
        $editMode = (bool) $request->boolean('edit');

        return view('client.bank-card', compact('client', 'editMode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_holder_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'min:8', 'max:18'],
            'confirm_account_number' => ['required', 'same:account_number'],
            'ifsc_code' => ['required', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_address' => ['required', 'string', 'max:500'],
        ], [
            'confirm_account_number.same' => 'Account number and confirm account number must match.',
            'ifsc_code.regex' => 'Please enter a valid IFSC code.',
        ]);

        $client = auth()->user()->client;

        if (! $client) {
            abort(403, 'Client profile not found.');
        }

        $client->update([
            'bank_account_holder_name' => trim($request->account_holder_name),
            'bank_account_number' => $request->account_number,
            'bank_ifsc_code' => strtoupper($request->ifsc_code),
            'bank_name' => trim($request->bank_name),
            'bank_address' => trim($request->bank_address),
        ]);

        return redirect()->route('client.bank-card')->with('success', 'Bank card details saved successfully.');
    }
}
