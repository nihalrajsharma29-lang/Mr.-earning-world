@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    Admin Dashboard
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    {{-- Total Clients --}}
    <a
        href="{{ route('clients.index') }}"
        class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-gray-500 text-lg">
            Total Clients
        </h3>

        <h1 class="text-4xl font-bold mt-3">
            {{ \App\Models\Client::count() }}
        </h1>

        <p class="text-blue-600 mt-3">
            View Clients →
        </p>
    </a>


    {{-- Excel Upload --}}
    <a
        href="{{ route('admin.daily.import') }}"
        class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-gray-500 text-lg">
            Excel Upload
        </h3>

        <h1 class="text-4xl font-bold mt-3">
            📁
        </h1>

        <p class="text-blue-600 mt-3">
            Upload Excel →
        </p>
    </a>


    {{-- Reports --}}
    <a
        href="{{ route('admin.reports') }}"
        class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-gray-500 text-lg">
            Reports
        </h3>

        <h1 class="text-4xl font-bold mt-3">
            {{ $totalReports ?? \App\Models\DailyReport::count() }}
        </h1>

        <p class="text-blue-600 mt-3">
            View Reports →
        </p>
    </a>

    {{-- Delete Daily Reports by Date --}}
    <div class="bg-red-50 p-6 rounded-xl shadow border border-red-100">
        <h3 class="text-red-700 text-lg font-semibold">
            Delete Daily Report Data
        </h3>

        <p class="text-red-600 text-sm mt-2">
            Delete all daily reports for a selected date.
        </p>

        <form
            action="{{ route('admin.reports.delete.date', ['date' => 'selected']) }}"
            method="POST"
            class="mt-4"
            onsubmit="return confirm('Are you sure you want to permanently delete all daily reports for this date?');"
        >
            @csrf
            @method('DELETE')
            <input type="hidden" name="report_type" value="daily_report">
            <label for="dashboard-delete-date" class="sr-only">Report date</label>
            <select
                id="dashboard-delete-date"
                name="date"
                required
                class="w-full rounded-lg border border-red-200 bg-white px-3 py-2"
            >
                <option value="">Select report date</option>
                @foreach($dailyReportDates as $reportDate)
                    <option value="{{ $reportDate }}">
                        {{ \Illuminate\Support\Carbon::parse($reportDate)->format('d-M-Y') }}
                    </option>
                @endforeach
            </select>
            <button
                type="submit"
                class="mt-3 w-full rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700"
                {{ $dailyReportDates->isEmpty() ? 'disabled' : '' }}
            >
                Delete Daily Reports
            </button>
        </form>
    </div>

    {{-- Host Management --}}
    <a
        href="{{ route('admin.hosts.index') }}"
        class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-gray-500 text-lg">
            Total Hosts
        </h3>

        <h1 class="text-4xl font-bold mt-3">
            {{ \App\Models\Customer::count() }}
        </h1>

        <p class="text-blue-600 mt-3">
            Manage Hosts →
        </p>
    </a>

    {{-- Add Host for Client --}}
    <a
        href="{{ route('admin.hosts.create') }}"
        class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-gray-500 text-lg">
            Add Host
        </h3>

        <h1 class="text-4xl font-bold mt-3">
            ➕
        </h1>

        <p class="text-blue-600 mt-3">
            Add under a Client →
        </p>
    </a>

    {{-- Pending Host Approvals --}}
    <a
        href="{{ route('admin.hosts.index', ['status' => 'pending']) }}"
        class="block bg-yellow-50 p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-yellow-700 text-lg">
            Pending Host Approvals
        </h3>

        <h1 class="text-4xl font-bold mt-3 text-yellow-800">
            {{ $pendingHosts }}
        </h1>

        <p class="text-yellow-700 mt-3">
            Review pending hosts →
        </p>
    </a>

    {{-- Skipped Host IDs --}}
    <a
        href="{{ route('admin.skipped-import-ids.index') }}"
        class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-gray-500 text-lg">
            Skipped Host IDs
        </h3>

        <h1 class="text-4xl font-bold mt-3">
            {{ \App\Models\SkippedImportId::query()->distinct('host_id')->count('host_id') }}
        </h1>

        <p class="text-blue-600 mt-3">
            Review skipped hosts →
        </p>
    </a>

    {{-- Manage Account --}}
    <a
        href="{{ route('profile.edit') }}"
        class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-gray-500 text-lg">
            Account
        </h3>

        <h1 class="text-4xl font-bold mt-3">
            🔐
        </h1>

        <p class="text-blue-600 mt-3">
            Manage Login & Password →
        </p>
    </a>

    {{-- Report Naming --}}
    <a
        href="{{ route('admin.report-import-names') }}"
        class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
    >
        <h3 class="text-gray-500 text-lg">
            Report Names
        </h3>

        <h1 class="text-4xl font-bold mt-3">
            🏷️
        </h1>

        <p class="text-blue-600 mt-3">
            Edit import file names →
        </p>
    </a>

</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 p-4 rounded-lg mt-6">
        {{ session('success') }}
    </div>
@endif

@endsection