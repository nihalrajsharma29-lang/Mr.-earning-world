@extends('layouts.client')

@php
    $activeReportType = $reportType ?? request('report_type', 'daily_report');
    $isPaymentReport = $activeReportType === 'payment_report';
    $isViolationReport = $activeReportType === 'violation_records';
@endphp

@section('title', $isPaymentReport ? 'Payment Report' : ($isViolationReport ? 'Violation Records' : 'Daily Reports'))
@section('page-heading', $isPaymentReport ? 'Payment Report' : ($isViolationReport ? 'Violation Records' : 'Daily Reports'))

@push('styles')
<style>
    .page-header h1 { margin-bottom: 8px; }
    .page-header p { color: #6b7280; }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
    }

    .summary-title { color: #6b7280; font-size: 13px; margin-bottom: 10px; }
    .summary-value { font-size: 28px; font-weight: 700; }

    .filter-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 24px;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: flex-end;
    }

    .filter-group { display: flex; flex-direction: column; gap: 6px; }
    .filter-group label { font-size: 13px; color: #6b7280; }
    .filter-group input { height: 42px; min-width: 210px; border: 1px solid #d1d5db; border-radius: 12px; padding: 0 14px; }
    .filter-btn, .reset-btn { height: 42px; border-radius: 12px; border: none; cursor: pointer; font-weight: 700; }
    .filter-btn { background: #2563eb; color: white; padding: 0 22px; }
    .reset-btn { background: #f3f4f6; color: #111827; padding: 0 22px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }

    .card { background: white; border-radius: 16px; border: 1px solid #e5e7eb; overflow: hidden; }
    .table-header { padding: 20px 24px; border-bottom: 1px solid #e5e7eb; }
    .table-header h3 { margin: 0; font-size: 18px; }
    .client-weekly-date { display: inline-flex; align-items: center; gap: 8px; margin-top: 12px; padding: 9px 13px; border: 1px solid #bfdbfe; border-radius: 10px; background: #eff6ff; color: #1e40af; font-size: 13px; font-weight: 700; }
    .client-weekly-date-label { color: #475569; font-weight: 600; }
    .table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 900px; }
    th, td { padding: 14px 18px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { position: sticky; top: 0; z-index: 2; background: #f9fafb; color: #6b7280; font-weight: 700; }
    .overview-title { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
    .overview-text { color: #6b7280; line-height: 1.7; margin: 0; }

    .status { display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
    .status-yes { background: #dcfce7; color: #166534; }
    .status-no { background: #fee2e2; color: #991b1b; }

    .alert { border-radius: 14px; padding: 14px 16px; margin-bottom: 18px; font-size: 14px; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }


    .empty { padding: 60px 20px; text-align: center; color: #6b7280; }
    .empty-icon { font-size: 42px; margin-bottom: 16px; }
    .empty a { color: #2563eb; text-decoration: none; font-weight: 700; }

    @media (max-width: 1000px) {
        .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 700px) {
        .summary-grid { grid-template-columns: 1fr; }
        .filter-form { flex-direction: column; align-items: stretch; }
        .filter-group input { width: 100%; }
    }
</style>
@endpush

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="page-header">
        <h1>{{ $isPaymentReport ? '💰 Payment Reports' : ($isViolationReport ? '⚠️ Violation Records' : '📅 Daily Reports') }}</h1>
        <p>
            {{
                $isPaymentReport
                    ? 'Browse payment report data for your hosts and export as needed.'
                    : ($isViolationReport
                        ? 'Search and review violation records submitted for your hosts.'
                        : 'Browse daily report data for your hosts, filter by date, and search by host name or ID.')
            }}
        </p>
    </div>

    @if($isViolationReport)
        <div class="filter-card">
            <div class="overview-title">Violations Records Overview</div>
            <p class="overview-text">Search, filter, and manage all host reports submitted across the system.</p>
        </div>
    @endif

    @unless($isViolationReport)
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-title">{{ $isPaymentReport ? 'Total Host Salary' : 'Total Host' }}</div>
            <div class="summary-value">
                @if($isPaymentReport)
                    ${{ number_format($paymentSummary['total_host_salary'] ?? 0, 2) }}
                @else
                    {{ $totalHostCount }}
                @endif
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-title">{{ $isPaymentReport ? 'Agent Fee' : 'Working Host' }}</div>
            <div class="summary-value">
                @if($isPaymentReport)
                    ${{ number_format($paymentSummary['agent_fee_total'] ?? 0, 2) }}
                @else
                    {{ $workingHostCount }}
                @endif
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-title">{{ $isPaymentReport ? 'Agent one time Bonus' : 'Total Coins' }}</div>
            <div class="summary-value">
                @if($isPaymentReport)
                    ${{ number_format($paymentSummary['agent_one_time_bonus_total'] ?? 0, 2) }}
                @else
                    {{ number_format($reports->sum('total_coins')) }}
                @endif
            </div>
        </div>
        @if($isPaymentReport)
        <div class="summary-card">
            <div class="summary-title">{{ $isPaymentReport ? 'Agent Total Salary' : 'Active Reports' }}</div>
            <div class="summary-value">
                @if($isPaymentReport)
                    ${{ number_format($paymentSummary['total_salary'] ?? 0, 2) }}
                @else
                    {{ $reports->where('if_active', 'Yes')->count() }}
                @endif
            </div>
        </div>
        @endif
    </div>
    @endunless

    <div class="filter-card">
        <div class="page-header" style="margin-bottom: 12px;">
            <h1 style="font-size: 22px; margin-bottom: 6px;">Search Reports</h1>
            <p style="margin: 0;">Showing: {{ ucwords(str_replace('_', ' ', $activeReportType)) }}</p>
        </div>

        <form action="{{ route('client.daily.reports') }}" method="GET" class="filter-form">
            <input type="hidden" name="report_type" value="{{ $activeReportType }}">

            <div class="filter-group">
                <label for="search">{{ $isViolationReport ? 'Host ID / Client Name / UID' : ($isPaymentReport ? 'Host ID' : 'Host ID / Name') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ $isViolationReport ? 'Search by host, client name, or UID' : ($isPaymentReport ? 'Enter host ID' : 'Enter host ID or name') }}">
            </div>

            @unless($isPaymentReport)
                <div class="filter-group">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}">
                </div>
            @endunless

            @php
                $activeColumns = $isPaymentReport ? $paymentReportColumns : ($isViolationReport ? $violationReportColumns : $dailyReportColumns);
            @endphp
            <div class="filter-group">
                <label for="sort_column">Sort column</label>
                <select id="sort_column" name="sort_column">
                    <option value="">Default order</option>
                    @foreach($activeColumns as $column)
                        <option value="{{ $column['key'] }}" {{ request('sort_column') === $column['key'] ? 'selected' : '' }}>{{ $column['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="sort_direction">Sort direction</label>
                <select id="sort_direction" name="sort_direction">
                    <option value="asc" {{ request('sort_direction', 'asc') === 'asc' ? 'selected' : '' }}>Low to High / A-Z</option>
                    <option value="desc" {{ request('sort_direction') === 'desc' ? 'selected' : '' }}>High to Low / Z-A</option>
                </select>
            </div>

            <button type="submit" class="filter-btn">🔎 Search</button>
            <a href="{{ route('client.daily.reports', ['report_type' => $activeReportType]) }}" class="reset-btn">Reset</a>
            <button type="submit" name="export" value="1" class="reset-btn">⬇️ Export to Excel</button>
        </form>
    </div>

    <div class="card">
        <div class="table-header">
            <h3>{{ $isPaymentReport ? 'Host Payment Reports' : ($isViolationReport ? 'Report Results' : 'Host Daily Reports') }}</h3>
            @if($isPaymentReport && $weeklyDate)
                <div class="client-weekly-date" aria-label="Weekly Date (Mon to Sun)">
                    <span class="client-weekly-date-label">Weekly Date (Mon to Sun):</span>
                    <span>{{ \Illuminate\Support\Carbon::parse($weeklyDate)->format('d-M-Y') }} to {{ \Illuminate\Support\Carbon::parse($weeklyDate)->addDays(6)->format('d-M-Y') }}</span>
                </div>
            @endif
        </div>

        @if($reports->count() > 0)
            <div class="table-wrapper">
                @if($isPaymentReport)
                    <table>
                        <thead>
                            <tr>
                                @foreach($paymentReportColumns as $column)
                                    <th>{{ $column['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    @foreach($paymentReportColumns as $column)
                                        @php
                                            $rawValue = $column['key'] === 'client_name_uid'
                                                ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                                                : $report->columnValue($column['key']);
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
                @elseif($isViolationReport)
                    <table>
                        <thead>
                            <tr>
                                @foreach($violationReportColumns as $column)
                                    <th>{{ $column['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    @foreach($violationReportColumns as $column)
                                        @php
                                            $rawValue = $column['key'] === 'client_name_uid'
                                                ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                                                : $report->columnValue($column['key']);
                                            $type = $column['type'] ?? 'text';
                                        @endphp

                                        @if($type === 'currency')
                                            <td>${{ number_format((float) ($rawValue ?? 0), 2) }}</td>
                                        @elseif($type === 'integer')
                                            <td>{{ number_format((int) ($rawValue ?? 0)) }}</td>
                                        @elseif($type === 'datetime')
                                            <td>
                                                {{
                                                    $rawValue
                                                        ? ($column['key'] === 'snapshots_time'
                                                            ? \Illuminate\Support\Carbon::parse($rawValue)->format('d-M-Y')
                                                            : \Illuminate\Support\Carbon::parse($rawValue)->format('Y-m-d H:i:s'))
                                                        : '-'
                                                }}
                                            </td>
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
                                <th>#</th>
                                @foreach($dailyReportColumns as $column)
                                    <th>{{ $column['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    @foreach($dailyReportColumns as $column)
                                        @php
                                            $rawValue = $column['key'] === 'client_name_uid'
                                                ? trim(($report->client?->name ?? '-') . ' / ' . ($report->client_id ?? '-'))
                                                : $report->columnValue($column['key']);
                                            $type = $column['type'] ?? 'text';
                                        @endphp
                                        @if($type === 'currency')
                                            <td>${{ number_format((float) ($rawValue ?? 0), 2) }}</td>
                                        @elseif($type === 'integer')
                                            <td>{{ number_format((int) ($rawValue ?? 0)) }}</td>
                                        @elseif($type === 'decimal')
                                            <td>{{ $rawValue ?? 0 }}</td>
                                        @elseif($type === 'date')
                                            <td>{{ $rawValue?->format('d M Y') ?? '-' }}</td>
                                        @elseif($type === 'datetime')
                                            <td>{{ $rawValue?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                        @elseif($type === 'boolean')
                                            <td>{{ $rawValue ? 'Yes' : 'No' }}</td>
                                        @else
                                            <td>{{ $rawValue !== null && $rawValue !== '' ? $rawValue : '-' }}</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        @else
            <div class="empty">
                <div class="empty-icon">📭</div>
                <h3>No Reports Found</h3>
                <p>Try adjusting the search filters or import report data.</p>
                <a href="{{ route('client.daily.import') }}">📥 Import Reports</a>
            </div>
        @endif
    </div>
@endsection
