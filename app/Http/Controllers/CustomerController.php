<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Client\BaseController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends BaseController
{
    private const ALLOWED_COUNTRIES = [
        'India',
        'Nigeria',
        'Morocco',
        'Thailand',
        'Philippines',
        'Malaysia',
        'Vietnam',
    ];

    /**
     * Show Add Host form.
     */
    public function create()
    {
        $countries = self::ALLOWED_COUNTRIES;

        return view('client.hosts.create', compact('countries'));
    }

    /**
     * Store new Host.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|string',
            'country' => 'required|string|in:'.implode(',', self::ALLOWED_COUNTRIES),
        ]);

        $user = Auth::user();

        // Get the client profile of logged-in user
        $client = $user->client;

        if (!$client) {
            abort(403, 'Client profile not found.');
        }

        $rawIds = preg_split('/[\r\n,]+/', $request->customer_ids) ?: [];
        $parsedIds = collect($rawIds)
            ->map(fn ($id) => trim((string) $id))
            ->filter()
            ->values();

        $uniqueIds = $parsedIds->unique()->values();

        if ($uniqueIds->isEmpty()) {
            return redirect()
                ->route('client.hosts.create')
                ->withErrors(['customer_ids' => 'Please enter at least one valid Host ID.'])
                ->withInput();
        }

        if ($uniqueIds->count() > 50) {
            return redirect()
                ->route('client.hosts.create')
                ->withErrors(['customer_ids' => 'You can add up to 50 Host IDs at once.'])
                ->withInput();
        }

        $existingIds = Customer::query()
            ->whereIn('customer_id', $uniqueIds)
            ->pluck('customer_id')
            ->all();

        $existingMap = array_fill_keys($existingIds, true);
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($uniqueIds as $hostId) {
            if (isset($existingMap[$hostId])) {
                $skippedCount++;
                continue;
            }

            // Since the UI only collects Host ID and Country, set `name` to Host ID to satisfy DB constraints.
            Customer::create([
                'client_id' => $client->id,
                'customer_id' => $hostId,
                'name' => $hostId,
                'country' => $request->country,
                'status' => 'Active',
                'approval_status' => 'pending',
            ]);

            $createdCount++;
        }

        if ($createdCount === 0) {
            return redirect()
                ->route('client.hosts.create')
                ->withErrors(['customer_ids' => 'All provided Host IDs already exist.'])
                ->withInput();
        }

        $message = "{$createdCount} host(s) submitted successfully for approval.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} duplicate ID(s) were skipped.";
        }

        return redirect()
            ->route('client.hosts.create')
            ->with('success', $message);
    }
}