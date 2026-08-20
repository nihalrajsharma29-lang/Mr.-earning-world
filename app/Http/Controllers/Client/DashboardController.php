<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Client\BaseController;
use App\Models\Customer;

class DashboardController extends BaseController
{
    public function index()
    {
        $client = auth()->user()->client;
        $clientId = $client?->id;

        $totalHosts = $clientId ? Customer::where('client_id', $clientId)->count() : 0;
        $pendingHosts = $clientId
            ? Customer::where('client_id', $clientId)->where('approval_status', 'pending')->count()
            : 0;
        $approvedHosts = $clientId
            ? Customer::where('client_id', $clientId)->where('approval_status', 'approved')->count()
            : 0;
        $hosts = $clientId
            ? Customer::where('client_id', $clientId)
                ->latest()
                ->get(['customer_id', 'country', 'approval_status', 'created_at'])
            : collect();

        return view('client.dashboard', compact(
            'totalHosts',
            'pendingHosts',
            'approvedHosts',
            'hosts'
        ));
    }
}