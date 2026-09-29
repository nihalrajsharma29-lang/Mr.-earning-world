<?php

namespace App\Http\Controllers\Manager;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
            ->paginate(20)
            ->withQueryString();

        $inviteUrl = URL::temporarySignedRoute('client.invite.create', now()->addDays(7));

        return view('manager.clients.index', compact('clients', 'search', 'inviteUrl'));
    }
}
