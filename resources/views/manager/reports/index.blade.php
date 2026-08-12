@extends('layouts.manager')

@php
    $activeReportType = $reportType ?? request('report_type', 'daily_report');
    $isPaymentReport = $activeReportType === 'payment_report';
    $isViolationReport = $activeReportType === 'violation_records';
@endphp

@section('title', $isPaymentReport ? 'Manager - Payment Report' : ($isViolationReport ? 'Manager - Violation Records' : 'Manager - Daily Reports'))
@section('page-heading', $isPaymentReport ? 'Payment Report' : ($isViolationReport ? 'Violation Records' : 'Daily Reports'))

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
    .alert { border-radius: 14px; padding: 14px 16px; margin-bottom: 18px; font-size: 14px; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .empty { padding: 48px; text-align: center; color: #6b7280; }
    @media (max-width: 960px) { .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .filter-form { grid-template-columns: 1fr; } }
    @media (max-width: 660px) { .summary-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">{{ $isPaymentReport ? 'Payment Reports Overview' : ($isViolationReport ? 'Violations Records Overview' : 'Daily Reports Overview') }}</div>
            <p class="panel-text">Search and review imported report data for the manager portal.</p>
        </div>
    </div>

    @unless($isViolationReport)
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">{{ $isPaymentReport ? 'Total Host Final Rewards' : 'Total Reports' }}</div>
            <div class="summary-value">
                @if($isPaymentReport)
                    ${{ number_format($paymentSummary['total_host_final_rewards'] ?? 0, 2) }}
                @else
                    {{ $reports->count() }}
                @endif
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">{{ $isPaymentReport ? 'Agent Fee' : 'Reports on Page' }}</div>
            <div class="summary-value">
                @if($isPaymentReport)
                    ${{ number_format($paymentSummary['agent_fee_total'] ?? 0, 2) }}
                @else
                    {{ $reports->count() }}
                @endif
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">{{ $isPaymentReport ? 'Agent One time Bonus' : 'Total Coins' }}</div>
            <div class="summary-value">
                @if($isPaymentReport)
                    ${{ number_format($paymentSummary['agent_one_time_bonus_total'] ?? 0, 2) }}
                @else
                    {{ number_format($reports->sum('total_coins')) }}
                @endif
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Agent Total Salary</div>
            <div class="summary-value">
                @if($isPaymentReport)
                    ${{ number_format($paymentSummary['total_salary'] ?? 0, 2) }}
                @else
                    {{ $reports->where('if_active', 'Yes')->count() }}
                @endif
            </div>
        </div>
    </div>
    @endunless

    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Search Reports</div>
            <form action="{{ route('manager.reports') }}" method="GET" class="filter-form">
                <input type="hidden" name="report_type" value="{{ request('report_type', 'daily_report') }}">

                <div class="filter-group">
                    <label for="search">Host ID / Client Name / UID</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search by host, client name, or UID">
                </div>

                @unless($isPaymentReport)
                    <div class="filter-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" value="{{ request('date') }}">
                    </div>
                @endunless

                <button type="submit" class="btn btn-primary">🔎 Search</button>
                <a href="{{ route('manager.reports', ['report_type' => request('report_type', 'daily_report')]) }}" class="btn-secondary">Reset</a>
                <button type="button" id="delete-selected-btn" class="btn btn-danger">🗑️ Delete Selected</button>
            </form>

            <form id="bulk-delete-form" action="{{ route('manager.reports.delete.selected') }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
                <input type="hidden" name="report_type" value="{{ $activeReportType }}">
                <div id="bulk-delete-inputs"></div>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Report Results</div>
            @if($reports->count() > 0)
                <div class="table-wrapper">
                    @if(($reportType ?? request('report_type', 'daily_report')) === 'payment_report')
                        <table>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all-reports"></th>
                                    @foreach($paymentReportColumns as $column)
                                        <th>{{ $column['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    <tr>
                                        <td>
                                            <input
                                                type="checkbox"
                                                class="report-select-checkbox"
                                                value="{{ $report->id }}"
                                            >
                                        </td>
                                        @foreach($paymentReportColumns as $column)
                                            @php
                                                $rawValue = $column['key'] === 'client_name_uid'
                                                    ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                                                    : data_get($report, $column['key']);
                                                $type = $column['type'] ?? 'text';
                                            @endphp

                                            @if($type === 'currency')
                                                <td>${{ number_format((float) ($rawValue ?? 0), 2) }}</td>
                                            @elseif($type === 'decimal')
                                                <td>{{ rtrim(rtrim(number_format((float) ($rawValue ?? 0), 6, '.', ''), '0'), '.') }}</td>
                                            @elseif($type === 'integer')
                                                <td>{{ number_format((int) ($rawValue ?? 0)) }}</td>
                                            @elseif($type === 'datetime')
                                                <td>{{ $rawValue ? \Illuminate\Support\Carbon::parse($rawValue)->format('Y-m-d H:i:s') : '-' }}</td>
                                            @else
                                                <td>{{ $rawValue !== null && $rawValue !== '' ? $rawValue : '-' }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif(($reportType ?? request('report_type', 'daily_report')) === 'violation_records')
                        <table>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all-reports"></th>
                                    @foreach($violationReportColumns as $column)
                                        <th>{{ $column['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    <tr>
                                        <td>
                                            <input
                                                type="checkbox"
                                                class="report-select-checkbox"
                                                value="{{ $report->id }}"
                                            >
                                        </td>
                                        @foreach($violationReportColumns as $column)
                                            @php
                                                $rawValue = $column['key'] === 'client_name_uid'
                                                    ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                                                    : data_get($report, $column['key']);
                                                $type = $column['type'] ?? 'text';
                                            @endphp

                                            @if($type === 'currency')
                                                <td>${{ number_format((float) ($rawValue ?? 0), 2) }}</td>
                                            @elseif($type === 'decimal')
                                                <td>{{ rtrim(rtrim(number_format((float) ($rawValue ?? 0), 6, '.', ''), '0'), '.') }}</td>
                                            @elseif($type === 'integer')
                                                <td>{{ number_format((int) ($rawValue ?? 0)) }}</td>
                                            @elseif($type === 'datetime')
                                                <td>{{ $rawValue ? \Illuminate\Support\Carbon::parse($rawValue)->format('Y-m-d H:i:s') : '-' }}</td>
                                            @else
                                                <td>{{ $rawValue !== null && $rawValue !== '' ? $rawValue : '-' }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all-reports"></th>
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
                                        <td>
                                            <input
                                                type="checkbox"
                                                class="report-select-checkbox"
                                                value="{{ $report->id }}"
                                            >
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
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
                    @endif
                </div>
            @else
                <div class="empty">No reports found for this filter.</div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const managerSelectAll = document.getElementById('select-all-reports');
    const managerRowCheckboxes = document.querySelectorAll('.report-select-checkbox');
    const managerDeleteSelectedBtn = document.getElementById('delete-selected-btn');
    const managerBulkDeleteForm = document.getElementById('bulk-delete-form');
    const managerBulkDeleteInputs = document.getElementById('bulk-delete-inputs');

    managerSelectAll?.addEventListener('change', function () {
        managerRowCheckboxes.forEach(function (checkbox) {
            checkbox.checked = managerSelectAll.checked;
        });
    });

    managerRowCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const checkedCount = Array.from(managerRowCheckboxes).filter(function (cb) {
                return cb.checked;
            }).length;

            if (managerSelectAll) {
                managerSelectAll.checked = checkedCount === managerRowCheckboxes.length && managerRowCheckboxes.length > 0;
            }
        });
    });

    managerDeleteSelectedBtn?.addEventListener('click', function () {
        const selectedIds = Array.from(managerRowCheckboxes)
            .filter(function (cb) { return cb.checked; })
            .map(function (cb) { return cb.value; });

        if (selectedIds.length === 0) {
            alert('Please select at least one report to delete.');
            return;
        }

        if (!confirm('Delete selected report(s)?')) {
            return;
        }

        if (!managerBulkDeleteForm || !managerBulkDeleteInputs) {
            return;
        }

        managerBulkDeleteInputs.innerHTML = '';
        selectedIds.forEach(function (id) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'report_ids[]';
            input.value = id;
            managerBulkDeleteInputs.appendChild(input);
        });

        managerBulkDeleteForm.submit();
    });
</script>
@endpush
