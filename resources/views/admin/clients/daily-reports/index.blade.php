@extends('layouts.app')

@section('title', 'Admin - Daily Reports')
@section('page-heading', 'Daily Reports')

@push('styles')
<style>
    .summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px; }
    .summary-card { background: white; border: 1px solid #e5e7eb; border-radius: 18px; padding: 22px; }
    .summary-label { color: #6b7280; font-size: 13px; margin-bottom: 8px; }
    .summary-value { font-size: 30px; font-weight: 700; }
    .panel { background: white; border: 1px solid #e5e7eb; border-radius: 18px; margin-bottom: 24px; overflow: hidden; }
    .panel-body { padding: 22px; }
    .panel-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
    .panel-text { color: #6b7280; line-height: 1.7; }
    .filter-form { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; align-items: end; }
    .filter-group { display: flex; flex-direction: column; gap: 8px; }
    .filter-group label { font-size: 13px; color: #6b7280; }
    .filter-group input { height: 44px; border: 1px solid #d1d5db; border-radius: 12px; padding: 0 14px; }
    .btn { border: none; border-radius: 12px; cursor: pointer; font-weight: 700; }
    .btn-primary { background: #2563eb; color: white; padding: 0 20px; height: 44px; }
    .btn-danger { background: #dc2626; color: white; padding: 0 20px; height: 44px; }
    .btn-secondary { background: #f8fafc; color: #111827; padding: 0 20px; height: 44px; border: 1px solid #e5e7eb; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
    .table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 900px; }
    th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f9fafb; color: #6b7280; font-weight: 700; }
    .badge { display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 999px; font-weight: 700; font-size: 12px; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .empty { padding: 48px; text-align: center; color: #6b7280; }
    .pagination { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; padding-top: 18px; }
    @media (max-width: 960px) { .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .filter-form { grid-template-columns: 1fr; } }
    @media (max-width: 660px) { .summary-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Daily Reports Overview</div>
            <p class="panel-text">Search, filter, and manage all host reports submitted across the system. Use the date delete card to remove all reports for a specific date.</p>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Reports</div>
            <div class="summary-value">{{ $reports->total() }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Reports on Page</div>
            <div class="summary-value">{{ $reports->count() }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Total Coins</div>
            <div class="summary-value">{{ number_format($reports->sum('total_coins')) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Active Reports</div>
            <div class="summary-value">{{ $reports->where('if_active', 'Yes')->count() }}</div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Search Reports</div>
            <form action="{{ route('admin.reports') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="search">Host ID / Client Name / UID</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search by host, client name, or UID">
                </div>

                <div class="filter-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="{{ request('date') }}">
                </div>

                <button type="submit" class="btn btn-primary">🔎 Filter</button>
                <a href="{{ route('admin.reports') }}" class="btn-secondary">Reset</a>
                <button type="submit" name="export" value="1" class="btn btn-secondary">⬇️ Export to Excel</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Delete Reports By Date</div>
            <p class="panel-text">Remove all reports for a selected date. This action cannot be undone.</p>
            <form id="delete-report-form" action="{{ route('admin.reports.delete.date', ['date' => 'DATE_PLACEHOLDER']) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="filter-form">
                    <div class="filter-group">
                        <label for="delete_date">Select a date</label>
                        <input type="date" id="delete_date" required>
                        <input type="hidden" id="delete_date_input" name="date" value="">
                    </div>
                    <button type="submit" class="btn btn-danger">🗑️ Delete Reports</button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Report Results</div>
            @if($reports->count() > 0)
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Host ID</th>
                                <th>Client Name / UID</th>
                                <th>Username</th>
                                <th>Story Status</th>
                                <th>Gift Coins</th>
                                <th>Non-Friend Video Coins</th>
                                <th>Friend Video Coins</th>
                                <th>Task Coins</th>
                                <th>Box Coins</th>
                                <th>Total Coins</th>
                                <th>Group Time</th>
                                <th>Match Count</th>
                                <th>Match Duration (Min)</th>
                                <th>App KYC Pass</th>
                                <th>Profile Video Status</th>
                                <th>Category</th>
                                <th>Long Call Ratio</th>
                                <th>Avg. Friend Call Duration (30D)</th>
                                <th>Total Call Duration (Min)</th>
                                <th>Bank Country</th>
                                <th>Bank Info Bound</th>
                                <th>Active Status</th>
                                <th>Current Week Total Coins</th>
                                <th>Previous Week 1 Total Coins</th>
                                <th>Previous Week 2 Total Coins</th>
                                <th>Previous Week 3 Total Coins</th>
                                <th>Payment Platform</th>
                                <th>App ID</th>
                                <th>Live Permission</th>
                                <th>Start Live Duration (Min)</th>
                                <th>Live-to-Call Ratio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ $reports->firstItem() + $loop->index }}</td>
                                    <td>{{ $report->dt?->format('d M Y') ?? '-' }}</td>
                                    <td><strong>{{ $report->host_id ?? '-' }}</strong></td>
                                    <td>{{ $report->client?->name ?? '-' }} / {{ $report->client_id }}</td>
                                    <td>{{ $report->user_name ?? '-' }}</td>
                                    <td>{{ $report->story_status ?? '-' }}</td>
                                    <td>{{ number_format($report->gift_coins ?? 0) }}</td>
                                    <td>{{ number_format($report->non_friend_video_coins ?? 0) }}</td>
                                    <td>{{ number_format($report->friend_video_coins ?? 0) }}</td>
                                    <td>{{ number_format($report->task_coins ?? 0) }}</td>
                                    <td>{{ number_format($report->box_coins ?? 0) }}</td>
                                    <td>{{ number_format($report->total_coins ?? 0) }}</td>
                                    <td>{{ $report->group_time?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td>{{ $report->match_count ?? 0 }}</td>
                                    <td>{{ $report->match_duration_min ?? 0 }}</td>
                                    <td>{{ $report->app_kyc_pass ?? '-' }}</td>
                                    <td>{{ $report->profile_video_status ?? '-' }}</td>
                                    <td>{{ $report->category ?? '-' }}</td>
                                    <td>{{ $report->long_call_ratio ?? 0 }}</td>
                                    <td>{{ $report->avg_friend_call_duration_s30d ?? 0 }}</td>
                                    <td>{{ $report->total_call_duration_m ?? 0 }}</td>
                                    <td>{{ $report->bank_country ?? '-' }}</td>
                                    <td>{{ $report->if_bind_bank_info ?? '-' }}</td>
                                    <td>{{ $report->if_active ?? '-' }}</td>
                                    <td>{{ number_format($report->current_week_total_coins ?? 0) }}</td>
                                    <td>{{ number_format($report->previous_week1_total_coins ?? 0) }}</td>
                                    <td>{{ number_format($report->previous_week2_total_coins ?? 0) }}</td>
                                    <td>{{ number_format($report->previous_week3_total_coins ?? 0) }}</td>
                                    <td>{{ $report->payment_platform ?? '-' }}</td>
                                    <td>{{ $report->app_id ?? '-' }}</td>
                                    <td>{{ $report->has_live_permission ? 'Yes' : 'No' }}</td>
                                    <td>{{ $report->start_live_duration_min ?? 0 }}</td>
                                    <td>{{ $report->live_to_call_ratio ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination">{{ $reports->withQueryString()->links() }}</div>
            @else
                <div class="empty">
                    <div class="empty-icon">📭</div>
                    <h3>No Reports Found</h3>
                    <p>Adjust filters or upload new report data.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('delete-report-form')?.addEventListener('submit', function (event) {
        const date = document.getElementById('delete_date').value;
        if (!date) {
            event.preventDefault();
            alert('Please select a date to delete.');
            return;
        }
        this.action = "{{ url('/admin/reports/date') }}" + '/' + date;
        document.getElementById('delete_date_input').value = date;
    });
</script>
@endpush
