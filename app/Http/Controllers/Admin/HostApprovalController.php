<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\AdminAuditLog;
use App\Models\Client;
use App\Models\Customer;
use Illuminate\Http\Request;

class HostApprovalController extends BaseController
{
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

        $hosts = $query->get();
        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view($isManager ? 'manager.hosts.index' : 'admin.hosts.index', compact('hosts', 'clients'));
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