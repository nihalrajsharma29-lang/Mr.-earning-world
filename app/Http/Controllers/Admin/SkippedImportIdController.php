<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminAuditLog;
use App\Models\Client;
use App\Models\Customer;
use App\Models\SkippedImportId;
use Illuminate\Http\Request;

class SkippedImportIdController extends BaseController
{
    public function __construct()
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role, ['admin', 'manager'], true),
            403
        );
    }

    public function index(Request $request)
    {
        $latestIdPerHost = SkippedImportId::query()
            ->selectRaw('MAX(id)')
            ->groupBy('host_id');

        $query = SkippedImportId::with('client')
            ->whereIn('id', $latestIdPerHost)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($query) use ($search) {
                $query->where('host_id', 'like', "%{$search}%");
            });
        }

        $skippedIds = $query->paginate(50)->withQueryString();
        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view('admin.skipped-import-ids.index', compact('skippedIds', 'clients'));
    }

    public function reassign(Request $request, SkippedImportId $skippedImportId)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $hostId = $skippedImportId->host_id;
        $client = Client::findOrFail($validated['client_id']);
        $customer = Customer::where('customer_id', $hostId)->first();

        if ($customer) {
            $customer->update([
                'client_id' => $client->id,
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]);
        } else {
            $customer = Customer::create([
                'client_id' => $client->id,
                'customer_id' => $hostId,
                'name' => $hostId,
                'status' => 'Active',
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]);
        }

        $removedCount = SkippedImportId::where('host_id', $hostId)->delete();

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'client_id' => $client->id,
            'action' => 'reassign_skipped_host',
            'details' => sprintf(
                '%s assigned skipped host ID "%s" to client "%s" and removed %d skipped record(s).',
                auth()->user()->name,
                $hostId,
                $client->name,
                $removedCount
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $routeName = auth()->user()->role === 'manager'
            ? 'manager.skipped-import-ids.index'
            : 'admin.skipped-import-ids.index';

        return redirect()->route($routeName)->with('success', "Host ID {$hostId} assigned to {$client->name} successfully.");
    }

    public function reassignSelected(Request $request)
    {
        $validated = $request->validate([
            'skipped_ids' => 'required|array|min:1',
            'skipped_ids.*' => 'integer|exists:skipped_import_ids,id',
            'client_id' => 'required|exists:clients,id',
        ]);

        $records = SkippedImportId::whereIn('id', $validated['skipped_ids'])->get();
        $hostIds = $records->pluck('host_id')->unique()->values();
        $client = Client::findOrFail($validated['client_id']);

        foreach ($hostIds as $hostId) {
            $customer = Customer::where('customer_id', $hostId)->first();

            if ($customer) {
                $customer->update([
                    'client_id' => $client->id,
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                ]);
            } else {
                Customer::create([
                    'client_id' => $client->id,
                    'customer_id' => $hostId,
                    'name' => $hostId,
                    'status' => 'Active',
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                ]);
            }
        }

        $removedCount = SkippedImportId::whereIn('host_id', $hostIds)->delete();

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'client_id' => $client->id,
            'action' => 'reassign_selected_skipped_hosts',
            'details' => sprintf(
                '%s assigned %d skipped host ID(s) to client "%s" and removed %d skipped record(s).',
                auth()->user()->name,
                $hostIds->count(),
                $client->name,
                $removedCount
            ),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "{$hostIds->count()} host ID(s) assigned to {$client->name} successfully.");
    }

    public function destroySelected(Request $request)
    {
        $validated = $request->validate([
            'skipped_ids' => 'required|array|min:1',
            'skipped_ids.*' => 'integer|exists:skipped_import_ids,id',
        ]);

        $hostIds = SkippedImportId::whereIn('id', $validated['skipped_ids'])
            ->pluck('host_id')
            ->unique()
            ->values();
        $deleted = SkippedImportId::whereIn('host_id', $hostIds)->delete();

        return back()->with('success', "{$deleted} skipped record(s) deleted successfully.");
    }
}