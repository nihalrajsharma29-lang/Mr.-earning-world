<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientInviteController extends Controller
{
    public function create()
    {
        return view('client-invites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients', 'email'),
                Rule::unique('users', 'email'),
            ],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:5000'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $client = DB::transaction(function () use ($validated): Client {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'client',
            ]);

            return Client::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'status' => 'Active',
                'commission_percentage' => 0,
                'user_id' => $user->id,
            ]);
        });

        return view('client-invites.success', ['clientName' => $client->name]);
    }
}