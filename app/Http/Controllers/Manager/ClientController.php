<?php

namespace App\Http\Controllers\Manager;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends BaseController
{
    public function index(Request $request)
    {
        $search = $request->search;

        $clients = Client::withCount('customers')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('manager.clients.index', compact('clients', 'search'));
    }
}
