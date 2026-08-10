<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Client\BaseController;
use App\Models\Customer;

class HostAuditController extends BaseController
{
    /**
     * Show host audit results for the logged-in client.
     */
    public function index()
    {
        $user = auth()->user();

        // Logged-in user ka client profile
        $client = $user->client;

        // Agar client profile nahi mila
        if (!$client) {
            abort(403, 'Client profile not found.');
        }

        // Sirf isi client ke hosts
        $hosts = Customer::with('client')
            ->where('client_id', $client->id)
            ->latest()
            ->get();

        return view('client.hosts.audit', compact('hosts'));
    }
}