<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Client;
use App\Models\Customer;
use Illuminate\Http\Request;

class HostApprovalController extends BaseController
{
    /**
     * Show all hosts and allow admin management.
     */
    public function index(Request $request)
    {
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

        return view('admin.hosts.index', compact('hosts', 'clients'));
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

        return redirect()
            ->route('admin.hosts.index')
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

        $updated = Customer::query()
            ->whereIn('id', $validated['host_ids'])
            ->update([
                'approval_status' => 'approved',
                'rejection_reason' => null,
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

        $customer->update([
            'client_id' => $validated['client_id'],
        ]);

        return redirect()
            ->route('admin.hosts.index')
            ->with('success', 'Host reassigned to a new client successfully.');
    }

    /**
     * Delete host.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('admin.hosts.index')
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

        $deleted = Customer::query()
            ->whereIn('id', $validated['host_ids'])
            ->delete();

        return back()->with('success', "{$deleted} host(s) deleted successfully.");
    }

    /**
     * Reject host.
     */
    public function reject(Request $request, Customer $customer)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $customer->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()
            ->route('admin.hosts.index')
            ->with('success', 'Host rejected successfully.');
    }
}