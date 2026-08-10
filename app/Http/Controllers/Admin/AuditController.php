<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AuditController extends BaseController
{
    public function index(Request $request)
    {
        $query = AdminAuditLog::with(['admin', 'client']);

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
