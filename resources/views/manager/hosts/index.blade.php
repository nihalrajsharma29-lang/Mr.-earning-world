@extends('layouts.manager')

@section('title', 'Manager - Host Management')
@section('page-heading', 'Host Management')

@push('styles')
<style>
    .panel { background: white; border: 1px solid #e5e7eb; border-radius: 18px; margin-bottom: 24px; overflow: hidden; }
    .panel-body { padding: 22px; }
    .panel-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
    .panel-text { color: #6b7280; line-height: 1.7; margin-bottom: 18px; }
    .alert { border-radius: 14px; padding: 16px 18px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 980px; }
    th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f9fafb; color: #6b7280; font-weight: 700; }
    .badge { display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 999px; font-weight: 700; font-size: 12px; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .btn { border: none; border-radius: 10px; padding: 10px 14px; cursor: pointer; font-size: 13px; font-weight: 700; }
    .btn-approve { background: #16a34a; color: white; }
    .btn-reject { background: #dc2626; color: white; }
    .reject-form input { min-width: 180px; padding: 10px; border: 1px solid #d1d5db; border-radius: 10px; }
    .empty { padding: 50px 20px; text-align: center; color: #6b7280; }
    .toolbar { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 18px; align-items: center; }
    .toolbar form { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    @media (max-width: 840px) { .table-wrapper { min-width: 760px; } }
</style>
@endpush

@section('content')
    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Host Management</div>
            <p class="panel-text">Review hosts and approve or reject them from the manager portal.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px; list-style: disc;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="toolbar">
                <form method="GET" action="{{ route('manager.hosts.index') }}">
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <label for="search" style="font-weight: 700; color: #374151;">Search</label>
                        <input id="search" name="search" value="{{ request('search') }}" placeholder="Host ID, client, name" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; min-width: 260px;">
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <label for="status" style="font-weight: 700; color: #374151;">Status</label>
                        <select id="status" name="status" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; min-width: 180px;">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-approve" style="background: #2563eb;">Filter</button>
                </form>
            </div>

            @if($hosts->count() > 0)
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Host ID</th>
                                <th>Host</th>
                                <th>Client</th>
                                <th>Country</th>
                                <th>Submitted Date</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hosts as $host)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $host->customer_id ?? '-' }}</strong></td>
                                    <td><strong>{{ $host->name ?? $host->customer_id ?? '-' }}</strong></td>
                                    <td>{{ $host->client->name ?? 'N/A' }}</td>
                                    <td>{{ $host->country ?? '-' }}</td>
                                    <td>{{ $host->created_at ? $host->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        @if($host->approval_status === 'approved')
                                            <span class="badge badge-approved">Approved</span>
                                        @elseif($host->approval_status === 'rejected')
                                            <span class="badge badge-rejected">Rejected</span>
                                        @else
                                            <span class="badge badge-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $host->rejection_reason ?: '-' }}</td>
                                    <td>
                                        @if($host->approval_status === 'approved')
                                            <span class="badge badge-approved">Approved</span>
                                        @else
                                            <div class="actions">
                                                <form action="{{ route('manager.hosts.approve', $host) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-approve">Approve</button>
                                                </form>

                                                <form action="{{ route('manager.hosts.reject', $host) }}" method="POST" class="reject-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="text" name="rejection_reason" placeholder="Reject reason">
                                                    <button type="submit" class="btn btn-reject">Reject</button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty">
                    <div style="font-size: 40px; margin-bottom: 14px;">📭</div>
                    <h3>No hosts found</h3>
                    <p>Use the filters above to search by host, client, or status.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
