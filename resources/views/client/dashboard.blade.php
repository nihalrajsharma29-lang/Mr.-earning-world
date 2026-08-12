@extends('layouts.client')

@section('title', 'Client Dashboard')
@section('page-heading', 'Data Dashboard')

@push('styles')
<style>
    .welcome {
        margin-bottom: 25px;
    }

    .welcome h1 {
        font-size: 29px;
        margin-bottom: 8px;
    }

    .welcome p {
        color: #6b7280;
        line-height: 1.6;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    }

    .stat-title {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 34px;
        font-weight: 700;
    }

    .stat-description {
        margin-top: 10px;
        color: #6b7280;
        font-size: 13px;
    }

    .card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .card-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-header h3 {
        font-size: 20px;
        margin: 0;
    }

    .card-header p {
        color: #6b7280;
        margin-top: 8px;
        font-size: 14px;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 16px 18px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
    }

    th {
        background: #f9fafb;
        color: #6b7280;
        font-weight: 700;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-approved {
        background: #dcfce7;
        color: #166534;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .empty {
        padding: 40px;
        text-align: center;
        color: #6b7280;
    }

    .empty a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 700;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .action {
        display: block;
        background: white;
        padding: 22px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        text-decoration: none;
        color: #111827;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .action:hover {
        border-color: #2563eb;
        transform: translateY(-2px);
    }

    .action-icon {
        font-size: 26px;
        margin-bottom: 12px;
    }

    .action-title {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .action-description {
        color: #6b7280;
        font-size: 13px;
    }

    @media (max-width: 900px) {
        .stats,
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="welcome">
        <h1>👋 Welcome, {{ auth()->user()->name }}</h1>
        <p>Manage your hosts, track approvals, and monitor daily performance from one place.</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-title">Total Hosts</div>
            <div class="stat-value">{{ auth()->user()->client?->customers?->count() ?? 0 }}</div>
            <div class="stat-description">Hosts submitted by your account</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Pending Approvals</div>
            <div class="stat-value">{{ auth()->user()->client?->customers?->where('approval_status', 'pending')->count() ?? 0 }}</div>
            <div class="stat-description">Hosts waiting for admin review</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Approved Hosts</div>
            <div class="stat-value">{{ auth()->user()->client?->customers?->where('approval_status', 'approved')->count() ?? 0 }}</div>
            <div class="stat-description">Hosts currently approved</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Host Performance</h3>
            <p>Quick snapshot of your hosts and approval status.</p>
        </div>

        <div class="table-wrapper">
            @if(auth()->user()->client?->customers?->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Host ID</th>
                            <th>Country</th>
                            <th>Submitted Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(auth()->user()->client->customers as $host)
                            <tr>
                                <td>{{ $host->customer_id ?? '-' }}</td>
                                <td>{{ $host->country ?? '-' }}</td>
                                <td>{{ $host->created_at?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    @if($host->approval_status === 'approved')
                                        <span class="badge badge-approved">Approved</span>
                                    @elseif($host->approval_status === 'rejected')
                                        <span class="badge badge-rejected">Rejected</span>
                                    @else
                                        <span class="badge badge-pending">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">
                    <p>No hosts added yet.</p>
                    <br>
                    <a href="{{ route('client.hosts.create') }}">➕ Add Your First Host</a>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Quick Actions</h3>
            <p>Access the most important pages quickly.</p>
        </div>

        <div style="padding: 20px;">
            <div class="quick-actions">
                <a href="{{ route('client.hosts.create') }}" class="action">
                    <div class="action-icon">➕</div>
                    <div class="action-title">Add Host</div>
                    <div class="action-description">Submit a new host for approval.</div>
                </a>

                <a href="{{ route('client.daily.reports') }}" class="action">
                    <div class="action-icon">📅</div>
                    <div class="action-title">Daily Reports</div>
                    <div class="action-description">Browse host performance reports.</div>
                </a>

                <a href="{{ route('client.daily.reports', ['report_type' => 'payment_report']) }}" class="action">
                    <div class="action-icon">💰</div>
                    <div class="action-title">Payment Report</div>
                    <div class="action-description">Open host payment report data.</div>
                </a>

                <a href="{{ route('client.daily.reports', ['report_type' => 'payment_status']) }}" class="action">
                    <div class="action-icon">💳</div>
                    <div class="action-title">Payment Status</div>
                    <div class="action-description">Check payment status entries.</div>
                </a>

                <a href="{{ route('client.daily.reports', ['report_type' => 'violation_records']) }}" class="action">
                    <div class="action-icon">⚠️</div>
                    <div class="action-title">Violation Records</div>
                    <div class="action-description">Review violation-related reports.</div>
                </a>
            </div>
        </div>
    </div>
@endsection
