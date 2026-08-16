<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Client;
use App\Models\User;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class ClientController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $search = $request->search;

    $clients = Client::withCount('customers')
        ->when($search, function ($query) use ($search) {

            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");

        })
        ->latest()
        ->get();

    return view('admin.clients.index', compact('clients', 'search'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:clients',
            'phone' => 'required',
            'commission_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Create client record
        $client = Client::create($request->all());

        // Optionally create an associated user and send reset link
        if ($request->filled('create_user')) {

            $existingUser = User::where('email', $request->email)->first();
            $password = $request->filled('password')
                ? Hash::make($request->password)
                : Hash::make(Str::random(16));

            if (!$existingUser) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $password,
                    'role' => 'client',
                ]);
            } else {
                $user = $existingUser;
                if ($request->filled('password')) {
                    $user->password = $password;
                    $user->save();
                }
            }

            $client->user_id = $user->id;
            $client->save();

            // Send password reset link if requested or if admin did not set a password for a new user
            if ($request->filled('send_reset') || (!$request->filled('password') && !$existingUser)) {
                Password::sendResetLink(['email' => $user->email]);

                // Audit log entry
                AdminAuditLog::create([
                    'admin_id' => auth()->id(),
                    'client_id' => $client->id,
                    'action' => 'send_reset_link',
                    'details' => 'Created by admin and reset link sent',
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client Added Successfully.');
    }


    /**
     * Send password reset link for client's user (creates user if missing).
     */
    public function sendResetLink(Client $client)
    {
        if (! $client->user) {
            $user = User::create([
                'name' => $client->name,
                'email' => $client->email,
                'password' => Hash::make(Str::random(16)),
                'role' => 'client',
            ]);

            $client->user_id = $user->id;
            $client->save();
        }

        Password::sendResetLink(['email' => $client->email]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'client_id' => $client->id,
            'action' => 'send_reset_link',
            'details' => 'Reset link sent via admin action',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Password reset link sent to client.');
    }


    /**
     * Regenerate a temporary password and send reset link (admin action).
     */
    public function regenPassword(Client $client)
    {
        if (! $client->user) {
            $user = User::create([
                'name' => $client->name,
                'email' => $client->email,
                'password' => Hash::make(Str::random(16)),
                'role' => 'client',
            ]);

            $client->user_id = $user->id;
            $client->save();
        } else {
            $user = $client->user;
            // Set a temporary random password (hashed) to invalidate old credentials
            $user->password = Hash::make(Str::random(12));
            $user->save();
        }

        // Send reset link so client sets their own password securely
        Password::sendResetLink(['email' => $user->email]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'client_id' => $client->id,
            'action' => 'regen_password_and_send_reset',
            'details' => 'Temporary password set by admin and reset link sent',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Temporary password set and reset link sent.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Client $client)
    {
        $user = $client->user ?? User::where('email', $client->email)
            ->where('role', 'client')
            ->first();

        $validated = $request->validate([
            'name'  => 'required',
            'phone' => 'required',
            'commission_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'email' => [
                'required',
                'email',
                Rule::unique('clients', 'email')->ignore($client->id),
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
        ]);

        $client->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company' => $request->input('company'),
            'address' => $request->input('address'),
            'status' => $request->input('status'),
            'commission_percentage' => $validated['commission_percentage'] ?? 0,
        ]);

        if ($user) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client Updated Successfully.');
    }

    /**
     * Update only the client's commission from the management table.
     */
    public function updateCommission(Request $request, Client $client)
    {
        $request->validate([
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $client->update([
            'commission_percentage' => $request->commission_percentage,
        ]);

        return back()->with('success', 'Commission updated successfully.');
    }

    /**
     * Update or create the client's login password directly from admin action.
     */
    public function updatePassword(Request $request, Client $client)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $client->user ?? User::where('email', $client->email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $client->name,
                'email' => $client->email,
                'password' => Hash::make($request->password),
                'role' => 'client',
            ]);

            $client->user_id = $user->id;
            $client->save();
        } else {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return back()->with('success', 'Client password updated successfully.');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Client $client)
    {
        if ($client->user) {
            $client->user->delete();
        }

        $client->customers()->delete();
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client and related hosts deleted successfully.');
    }
}