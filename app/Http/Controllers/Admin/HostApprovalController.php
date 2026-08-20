<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\AdminAuditLog;
use App\Models\Client;
use App\Models\Customer;
use App\Models\SkippedImportId;
use Illuminate\Http\Request;

class HostApprovalController extends BaseController
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

    public function __construct()
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role, ['admin', 'manager'], true),
            403
        );
    }

    /**
     * Show all hosts and allow admin management.
     */
    public function index(Request $request)
    {
        $isManager = auth()->user()->role === 'manager';
        $query = Customer::with('client')->latest();

        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('customer_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%");
                    });
            });
        }

        $hosts = $query->paginate(25)->withQueryString();
        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view($isManager ? 'manager.hosts.index' : 'admin.hosts.index', compact('hosts', 'clients'));
    }

    /**
     * Show the form for adding hosts under a client.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get(['id', 'name']);
        $countries = self::ALLOWED_COUNTRIES;

        return view('admin.hosts.create', compact('clients', 'countries'));
    }

    /**
     * Add hosts on behalf of a client.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'customer_ids' => 'required|string',
            'country' => 'required|string|in:'.implode(',', self::ALLOWED_COUNTRIES),
        ]);

        $uniqueIds = collect(preg_split('/[\r\n,]+/', $validated['customer_ids']) ?: [])
            ->map(fn ($id) => trim((string) $id))
            ->filter()
            ->unique()
            ->values();

        if ($uniqueIds->isEmpty()) {
            return back()->withErrors(['customer_ids' => 'Please enter at least one valid Host ID.'])->withInput();
        }

        if ($uniqueIds->count() > 50) {
            return back()->withErrors(['customer_ids' => 'You can add up to 50 Host IDs at once.'])->withInput();
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

            Customer::create([
                'client_id' => $validated['client_id'],
                'customer_id' => $hostId,
                'name' => $hostId,
                'country' => $validated['country'],
                'status' => 'Active',
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]);

            SkippedImportId::where('host_id', $hostId)->delete();

            $createdCount++;
        }

        if ($createdCount === 0) {
            return back()->withErrors(['customer_ids' => 'All provided Host IDs already exist.'])->withInput();
        }

        $client = Client::find($validated['client_id']);
        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'client_id' => $client->id,
            'action' => 'add_host_for_client',
            'details' => sprintf(
                '%s added %d host(s) under client "%s": %s.',
                auth()->user()->name,
                $createdCount,
                $client->name,
                $uniqueIds->reject(fn ($id) => isset($existingMap[$id]))->implode(', ')
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $message = "{$createdCount} host(s) added successfully under {$client->name}.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} duplicate ID(s) were skipped.";
        }

        return redirect()->route(auth()->user()->role === 'manager' ? 'manager.hosts.create' : 'admin.hosts.create')
            ->with('success', $message);
    }

    /**
     * Approve host.
     */
    public function approve(Customer $customer)
    {
        $customer->update([
            'approval_status' => 'approved',
            'rejection_reason' => null,
        ]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'approve_host',
            'details' => sprintf(
                '%s approved host "%s" (Customer ID: %s, Host ID: %s).',
                auth()->user()->name,
                $customer->name ?: $customer->username ?: 'Unknown Host',
                $customer->customer_id ?? $customer->id,
                $customer->id
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $routeName = auth()->user()->role === 'manager' ? 'manager.hosts.index' : 'admin.hosts.index';

        return redirect()
            ->route($routeName)
            ->with('success', 'Host approved successfully.');
    }

    /**
     * Approve selected hosts in one action.
     */
    public function approveSelected(Request $request)
    {
        $validated = $request->validate([
            'host_ids' => 'required|array|min:1',
            'host_ids.*' => 'integer|exists:customers,id',
        ]);

        $hostIds = $validated['host_ids'];
        $hosts = Customer::query()->whereIn('id', $hostIds)->get();

        $updated = Customer::query()
            ->whereIn('id', $hostIds)
            ->update([
                'approval_status' => 'approved',
                'rejection_reason' => null,
            ]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'approve_selected_hosts',
            'details' => sprintf(
                '%s approved %d host(s): %s.',
                auth()->user()->name,
                $updated,
                $hosts->map(function ($host) {
                    return $host->name ?: $host->username ?: 'Unknown Host';
                })->implode(', ')
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "{$updated} host(s) approved successfully.");
    }

    /**
     * Reassign selected hosts to one client.
     */
    public function reassignSelected(Request $request)
    {
        $validated = $request->validate([
            'host_ids' => 'required|array|min:1',
            'host_ids.*' => 'integer|exists:customers,id',
            'client_id' => 'required|exists:clients,id',
        ]);

        $updated = Customer::query()
            ->whereIn('id', $validated['host_ids'])
            ->update(['client_id' => $validated['client_id']]);

        $client = Client::findOrFail($validated['client_id']);
        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'client_id' => $client->id,
            'action' => 'reassign_selected_hosts',
            'details' => sprintf(
                '%s reassigned %d selected host(s) to client "%s".',
                auth()->user()->name,
                $updated,
                $client->name
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "{$updated} host(s) reassigned to {$client->name} successfully.");
    }

    /**
     * Reassign host to another client.
     */
    public function reassign(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $previousClientId = $customer->client_id;
        $customer->update([
            'client_id' => $validated['client_id'],
        ]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'reassign_host',
            'details' => sprintf(
                '%s reassigned host "%s" from client %s to client %s.',
                auth()->user()->name,
                $customer->name ?: $customer->username ?: 'Unknown Host',
                $previousClientId ?? 'unknown',
                $validated['client_id']
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $routeName = auth()->user()->role === 'manager' ? 'manager.hosts.index' : 'admin.hosts.index';

        return redirect()
            ->route($routeName)
            ->with('success', 'Host reassigned to a new client successfully.');
    }

    /**
     * Delete host.
     */
    public function destroy(Customer $customer)
    {
        $customerName = $customer->name ?: $customer->username ?: 'Unknown Host';
        $customer->delete();

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_host',
            'details' => sprintf(
                '%s deleted host "%s" (Customer ID: %s, Host ID: %s).',
                auth()->user()->name,
                $customerName,
                $customer->customer_id ?? $customer->id,
                $customer->id
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $routeName = auth()->user()->role === 'manager' ? 'manager.hosts.index' : 'admin.hosts.index';

        return redirect()
            ->route($routeName)
            ->with('success', 'Host deleted successfully.');
    }

    /**
     * Delete selected hosts in one action.
     */
    public function destroySelected(Request $request)
    {
        $validated = $request->validate([
            'host_ids' => 'required|array|min:1',
            'host_ids.*' => 'integer|exists:customers,id',
        ]);

        $hosts = Customer::query()->whereIn('id', $validated['host_ids'])->get();
        $deleted = Customer::query()
            ->whereIn('id', $validated['host_ids'])
            ->delete();

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_selected_hosts',
            'details' => sprintf(
                '%s deleted %d host(s): %s.',
                auth()->user()->name,
                $deleted,
                $hosts->map(function ($host) {
                    return $host->name ?: $host->username ?: 'Unknown Host';
                })->implode(', ')
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "{$deleted} host(s) deleted successfully.");
    }

    /**
     * Reject host.
     */
    public function reject(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $customer->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => 'reject_host',
            'details' => sprintf(
                '%s rejected host "%s"%s.',
                auth()->user()->name,
                $customer->name ?: $customer->username ?: 'Unknown Host',
                ' with reason: ' . $validated['rejection_reason']
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $routeName = auth()->user()->role === 'manager' ? 'manager.hosts.index' : 'admin.hosts.index';

        return redirect()
            ->route($routeName)
            ->with('success', 'Host rejected successfully.');
    }
}