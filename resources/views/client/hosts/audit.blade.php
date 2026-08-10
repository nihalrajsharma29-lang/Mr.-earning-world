@extends('layouts.client')

@section('title', 'Host Audit Results')
@section('page-heading', 'Host Audit Results')

@push('styles')
<style>
    .page-header h1 { margin-bottom: 8px; }
    .page-header p { color: #6b7280; }
    .card { border-radius: 16px; overflow: hidden; }
    .table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 14px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f9fafb; color: #6b7280; }
    .badge { display: inline-flex; align-items: center; justify-content: center; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 12px; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .reject-form { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .reject-form input { min-width: 210px; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; }
    .actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .btn { border: none; border-radius: 8px; padding: 10px 14px; font-weight: 700; cursor: pointer; }
    .btn-approve { background: #16a34a; color: white; }
    .btn-reject { background: #dc2626; color: white; }
    .empty { text-align: center; padding: 50px 20px; color: #6b7280; }
    .empty-icon { font-size: 40px; margin-bottom: 16px; }
    .cta-link { display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #2563eb; font-weight: 700; }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>🔍 Host Audit Results</h1>
        <p>Review the approval status of hosts submitted by your account.</p>
    </div>

    <div class="card">
        @if($hosts->count() > 0)
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Host ID</th>
                            <th>Client Name</th>
                            <th>Country</th>
                            <th>Submitted</th>
                            <th>Status / Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hosts as $host)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $host->customer_id ?? '-' }}</strong></td>
                                <td>{{ $host->client?->name ?? '-' }}</td>
                                <td>{{ $host->country ?? '-' }}</td>
                                <td>{{ $host->created_at?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    @if($host->approval_status === 'approved')
                                        <span class="badge badge-approved">🟢 Approved</span>
                                    @elseif($host->approval_status === 'rejected')
                                        <span class="badge badge-rejected">🔴 {{ $host->rejection_reason ?? 'Rejected' }}</span>
                                    @else
                                        <span class="badge badge-pending">🟡 Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">📋</div>
                <h3>No Hosts Found</h3>
                <p>You have not submitted any hosts yet.</p>
                <a href="{{ route('client.hosts.create') }}" class="cta-link">➕ Add a Host</a>
            </div>
        @endif
    </div>
@endsection
