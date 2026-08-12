<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daily Reports - Client Portal</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        /* =========================
           SIDEBAR
        ========================== */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #111827;
            color: white;
            padding: 20px 0;
            overflow-y: auto;
        }

        .logo {
            padding: 0 20px 25px;
            font-size: 21px;
            font-weight: bold;
            border-bottom: 1px solid #374151;
        }

        .logo span {
            color: #60a5fa;
        }

        .menu {
            margin-top: 15px;
        }

        .menu-title {
            padding: 12px 20px 7px;
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu a {
            display: block;
            padding: 12px 20px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14px;
        }

        .menu a:hover {
            background: #1f2937;
            color: white;
        }

        .menu a.active {
            background: #2563eb;
            color: white;
        }

        .logout-form {
            margin-top: 10px;
        }

        .logout-btn {
            width: 100%;
            border: none;
            background: transparent;
            color: #d1d5db;
            text-align: left;
            padding: 12px 20px;
            font-size: 14px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #1f2937;
            color: white;
        }

        /* =========================
           MAIN
        ========================== */

        .main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .topbar h2 {
            font-size: 20px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-icon {
            width: 38px;
            height: 38px;
            background: #2563eb;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .content {
            padding: 30px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 27px;
            margin-bottom: 7px;
        }

        .page-header p {
            color: #6b7280;
        }

        /* =========================
           SUMMARY CARDS
        ========================== */

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
        }

        .summary-title {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .summary-value {
            font-size: 25px;
            font-weight: bold;
        }

        /* =========================
           FILTER
        ========================== */

        .filter-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 12px;
            color: #6b7280;
        }

        .filter-group input {
            height: 40px;
            min-width: 180px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            padding: 0 12px;
            outline: none;
        }

        .filter-group input:focus {
            border-color: #2563eb;
        }

        .filter-btn {
            height: 40px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            padding: 0 18px;
            cursor: pointer;
        }

        .filter-btn:hover {
            background: #1d4ed8;
        }

        .reset-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            padding: 0 18px;
            border-radius: 7px;
            background: #e5e7eb;
            color: #374151;
            text-decoration: none;
        }

        /* =========================
           TABLE
        ========================== */

        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-header h3 {
            font-size: 17px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 2200px;
        }

        th {
            background: #f9fafb;
            text-align: left;
            padding: 14px 15px;
            font-size: 12px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        td {
            padding: 14px 15px;
            font-size: 13px;
            border-bottom: 1px solid #f0f0f0;
            white-space: nowrap;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .total-coins {
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-yes {
            background: #dcfce7;
            color: #166534;
        }

        .status-no {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-pass {
            background: #dcfce7;
            color: #166534;
        }

        .status-fail {
            background: #fee2e2;
            color: #991b1b;
        }

        /* =========================
           PAGINATION
        ========================== */

        .pagination {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .pagination nav {
            display: flex;
            justify-content: center;
        }

        /* =========================
           EMPTY
        ========================== */

        .empty {
            padding: 60px 20px;
            text-align: center;
            color: #6b7280;
        }

        .empty-icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        /* =========================
           MOBILE
        ========================== */

        @media (max-width: 1000px) {

            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 0 15px;
            }

            .content {
                padding: 15px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group input {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    {{-- =========================
         SIDEBAR
    ========================== --}}

    <aside class="sidebar">

        <div class="logo">
            Client <span>Portal</span>
        </div>

        <div class="menu">

            <div class="menu-title">
                Main Menu
            </div>

            <a href="{{ route('client.dashboard') }}">
                📊 Data Dashboard
            </a>

            <a href="#">
                👤 Personal Information
            </a>

            <a href="{{ route('client.hosts.create') }}">
                ➕ Add Host
            </a>

            <a href="{{ route('client.hosts.audit') }}">
                🔍 New Host Audit Results
            </a>

            <a
                href="{{ route('client.daily.reports') }}"
                class="active"
            >
                📅 Daily Reports
            </a>

            <a href="#">
                💰 Payment Reports
            </a>

            <a href="#">
                💳 Payment Results
            </a>

            <a href="#">
                ⚠️ Violations Records
            </a>

            <a href="#">
                📢 Event Notice
            </a>

            <a href="#">
                💬 Feedback
            </a>

            <div class="menu-title">
                Account
            </div>

            <form
                action="{{ route('logout') }}"
                method="POST"
                class="logout-form"
            >
                @csrf

                <button
                    type="submit"
                    class="logout-btn"
                >
                    🚪 Logout
                </button>
            </form>

        </div>

    </aside>


    {{-- =========================
         MAIN CONTENT
    ========================== --}}

    <main class="main">

        <div class="topbar">

            <h2>
                Daily Reports
            </h2>

            <div class="profile">

                <div class="profile-icon">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div>
                    <strong>
                        {{ auth()->user()->name }}
                    </strong>
                </div>

            </div>

        </div>


        <div class="content">

            <div class="page-header">

                <h1>
                            {{ request('report_type', 'daily_report') === 'daily_report' ? '📅 Daily Reports' : (request('report_type') === 'payment_report' ? '💰 Payment Report' : (request('report_type') === 'payment_status' ? '💳 Payment Status' : '⚠️ Violation Records')) }}
                        </h1>

                        <p>
                            {{ request('report_type', 'daily_report') === 'daily_report' ? 'Daily working performance of your hosts.' : (request('report_type') === 'payment_report' ? 'Payment report data for your hosts.' : (request('report_type') === 'payment_status' ? 'Payment status details for your hosts.' : 'Violation records for your hosts.')) }}


            {{-- =========================
                 SUMMARY
            ========================== --}}

            <div class="summary-grid">

                <div class="summary-card">

                    <div class="summary-title">
                        Total Reports
                    </div>

                    <div class="summary-value">
                        {{ $reports->total() }}
                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-title">
                        Reports This Page
                    </div>

                    <div class="summary-value">
                        {{ $reports->count() }}
                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-title">
                        Total Coins This Page
                    </div>

                    <div class="summary-value">
                        {{ number_format($reports->sum('total_coins')) }}
                    </div>

                </div>

                <div class="summary-card">

                    <div class="summary-title">
                        Total Salary This Page
                    </div>

                    <div class="summary-value">
                        ₹{{ number_format($reports->sum('salary_amount'), 2) }}
                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-title">
                        Active Hosts This Page
                    </div>

                    <div class="summary-value">
                        {{ $reports->where('if_active', 'Yes')->count() }}
                    </div>

                </div>

            </div>


            {{-- =========================
                 FILTER
            ========================== --}}

            <div class="filter-card">

                <form
                    action="{{ route('client.daily.reports', ['report_type' => request('report_type', 'daily_report')]) }}"
                    method="GET"
                    class="filter-form"
                >
                    <input type="hidden" name="report_type" value="{{ request('report_type', 'daily_report') }}">

                    <div class="filter-group">

                        <label for="host_id">
                            Host ID
                        </label>

                        <input
                            type="text"
                            name="host_id"
                            id="host_id"
                            value="{{ request('host_id') }}"
                            placeholder="Enter Host ID"
                        >

                    </div>


                    <div class="filter-group">

                        <label for="date">
                            Date
                        </label>

                        <input
                            type="date"
                            name="date"
                            id="date"
                            value="{{ request('date') }}"
                        >

                    </div>


                    <button
                        type="submit"
                        class="filter-btn"
                    >
                        🔎 Search
                    </button>

                    <button
                        type="submit"
                        name="export"
                        value="1"
                        class="reset-btn"
                    >
                        ⬇️ Export to Excel
                    </button>

                    <a
                        href="{{ route('client.daily.reports') }}"
                        class="reset-btn"
                    >
                        Reset
                    </a>

                </form>

            </div>


            {{-- =========================
                 REPORT TABLE
            ========================== --}}

            <div class="card">

                <div class="table-header">

                    <h3>
                        Host Daily Working Reports
                    </h3>

                </div>


                @if($reports->count() > 0)

                    <div class="table-wrapper">

                        <table>

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Host ID</th>
                                    <th>Group Name</th>
                                    <th>User Name</th>
                                    <th>Story Status</th>

                                    <th>Gift Coins</th>
                                    <th>Non-Friend Video Coins</th>
                                    <th>Friend Video Coins</th>
                                    <th>Task Coins</th>
                                    <th>Box Coins</th>
                                    <th>Total Coins</th>
                                    <th>Salary Amount</th>
                                    <th>Salary Status</th>
                                    <th>Violation Records</th>

                                    <th>Group Time</th>

                                    <th>Match Count</th>
                                    <th>Match Duration Min</th>

                                    <th>KYC</th>
                                    <th>Profile Video</th>
                                    <th>Category</th>

                                    <th>Long Call Ratio</th>
                                    <th>Avg Friend Call 30D</th>
                                    <th>Total Call Duration</th>

                                    <th>Bank Country</th>
                                    <th>Bank Info</th>
                                    <th>Active</th>

                                    <th>Current Week Coins</th>
                                    <th>Previous Week 1</th>
                                    <th>Previous Week 2</th>
                                    <th>Previous Week 3</th>

                                    <th>Payment Platform</th>
                                    <th>App ID</th>

                                    <th>Live Permission</th>
                                    <th>Live Duration</th>
                                    <th>Live / Call Ratio</th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($reports as $report)

                                    <tr>

                                        <td>
                                            {{ $reports->firstItem() + $loop->index }}
                                        </td>

                                        <td>
                                            {{ $report->dt?->format('d M Y') ?? '-' }}
                                        </td>

                                        <td>
                                            <strong>
                                                {{ $report->host_id ?? '-' }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ $report->group_name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $report->user_name ?? $report->customer?->name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $report->story_status ?? '-' }}
                                        </td>


                                        {{-- COINS --}}

                                        <td>
                                            {{ number_format($report->gift_coins ?? 0) }}
                                        </td>

                                        <td>
                                            {{ number_format($report->non_friend_video_coins ?? 0) }}
                                        </td>

                                        <td>
                                            {{ number_format($report->friend_video_coins ?? 0) }}
                                        </td>

                                        <td>
                                            {{ number_format($report->task_coins ?? 0) }}
                                        </td>

                                        <td>
                                            {{ number_format($report->box_coins ?? 0) }}
                                        </td>

                                        <td class="total-coins">
                                            {{ number_format($report->total_coins ?? 0) }}
                                        </td>

                                        <td>
                                            ₹{{ number_format($report->salary_amount ?? 0, 2) }}
                                        </td>

                                        <td>
                                            {{ $report->salary_status ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $report->violation_records ?? '-' }}
                                        </td>


                                        {{-- GROUP --}}

                                        <td>
                                            {{ $report->group_time?->format('d M Y H:i') ?? '-' }}
                                        </td>


                                        {{-- MATCH --}}

                                        <td>
                                            {{ number_format($report->match_count ?? 0) }}
                                        </td>

                                        <td>
                                            {{ $report->match_duration_min ?? 0 }}
                                        </td>


                                        {{-- KYC / PROFILE --}}

                                        <td>

                                            @if(strtolower($report->app_kyc_pass ?? '') === 'pass')

                                                <span class="status status-pass">
                                                    Pass
                                                </span>

                                            @else

                                                {{ $report->app_kyc_pass ?? '-' }}

                                            @endif

                                        </td>

                                        <td>
                                            {{ $report->profile_video_status ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $report->category ?? '-' }}
                                        </td>


                                        {{-- CALL --}}

                                        <td>
                                            {{ $report->long_call_ratio ?? 0 }}
                                        </td>

                                        <td>
                                            {{ $report->avg_friend_call_duration_s30d ?? 0 }}
                                        </td>

                                        <td>
                                            {{ $report->total_call_duration_m ?? 0 }}
                                        </td>


                                        {{-- BANK --}}

                                        <td>
                                            {{ $report->bank_country ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $report->if_bind_bank_info ?? '-' }}
                                        </td>

                                        <td>

                                            @if(strtolower($report->if_active ?? '') === 'yes')

                                                <span class="status status-yes">
                                                    Yes
                                                </span>

                                            @else

                                                <span class="status status-no">
                                                    {{ $report->if_active ?? '-' }}
                                                </span>

                                            @endif

                                        </td>


                                        {{-- WEEKLY COINS --}}

                                        <td>
                                            {{ number_format($report->current_week_total_coins ?? 0) }}
                                        </td>

                                        <td>
                                            {{ number_format($report->previous_week1_total_coins ?? 0) }}
                                        </td>

                                        <td>
                                            {{ number_format($report->previous_week2_total_coins ?? 0) }}
                                        </td>

                                        <td>
                                            {{ number_format($report->previous_week3_total_coins ?? 0) }}
                                        </td>


                                        {{-- PAYMENT --}}

                                        <td>
                                            {{ $report->payment_platform ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $report->app_id ?? '-' }}
                                        </td>


                                        {{-- LIVE --}}

                                        <td>

                                            @if($report->has_live_permission)

                                                <span class="status status-yes">
                                                    TRUE
                                                </span>

                                            @else

                                                <span class="status status-no">
                                                    FALSE
                                                </span>

                                            @endif

                                        </td>

                                        <td>
                                            {{ $report->start_live_duration_min ?? 0 }}
                                        </td>

                                        <td>
                                            {{ $report->live_to_call_ratio ?? 0 }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- PAGINATION --}}

                    <div class="pagination">

                        {{ $reports->withQueryString()->links() }}

                    </div>

                @else

                    <div class="empty">

                        <div class="empty-icon">
                            📋
                        </div>

                        <h3>
                            No Daily Reports Found
                        </h3>

                        <p style="margin-top: 8px;">
                            There are no daily reports available for your account.
                        </p>

                    </div>

                @endif

            </div>

        </div>

    </main>

</body>

</html>