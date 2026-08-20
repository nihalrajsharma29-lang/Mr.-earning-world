@extends('layouts.manager')

@section('title', 'Manager Dashboard')
@section('page-heading', 'Manager Dashboard')

@section('content')
    <h1 class="text-3xl font-bold mb-6">
        Manager Dashboard
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-6">
        <a
            href="{{ route('manager.daily.import') }}"
            class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
        >
            <h3 class="text-gray-500 text-lg">
                Import Report
            </h3>

            <h1 class="text-4xl font-bold mt-3">
                📁
            </h1>

            <p class="text-blue-600 mt-3">
                Upload Excel →
            </p>
        </a>

        <a
            href="{{ route('manager.reports', ['report_type' => 'daily_report']) }}"
            class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
        >
            <h3 class="text-gray-500 text-lg">
                Daily Report
            </h3>

            <h1 class="text-4xl font-bold mt-3">
                {{ $dailyReports }}
            </h1>

            <p class="text-blue-600 mt-3">
                View daily report →
            </p>
        </a>

        <a
            href="{{ route('manager.reports', ['report_type' => 'payment_report']) }}"
            class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
        >
            <h3 class="text-gray-500 text-lg">
                Payment Report
            </h3>

            <h1 class="text-4xl font-bold mt-3">
                {{ $paymentReports }}
            </h1>

            <p class="text-blue-600 mt-3">
                View payment report →
            </p>
        </a>

        <a
            href="{{ route('manager.reports', ['report_type' => 'violation_records']) }}"
            class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
        >
            <h3 class="text-gray-500 text-lg">
                Violation Record
            </h3>

            <h1 class="text-4xl font-bold mt-3">
                {{ $violationReports }}
            </h1>

            <p class="text-blue-600 mt-3">
                View violations →
            </p>
        </a>

        <a
            href="{{ route('manager.hosts.index') }}"
            class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
        >
            <h3 class="text-gray-500 text-lg">
                Total Hosts
            </h3>

            <h1 class="text-4xl font-bold mt-3">
                {{ $totalHosts }}
            </h1>

            <p class="text-blue-600 mt-3">
                Manage Hosts →
            </p>
        </a>

        <a
            href="{{ route('manager.hosts.create') }}"
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

        <a
            href="{{ route('manager.hosts.index', ['status' => 'pending']) }}"
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

        <a
            href="{{ route('manager.skipped-import-ids.index') }}"
            class="block bg-white p-6 rounded-xl shadow hover:shadow-lg transition"
        >
            <h3 class="text-gray-500 text-lg">
                Skipped Host IDs
            </h3>

            <h1 class="text-4xl font-bold mt-3">
                {{ $skippedHostIds }}
            </h1>

            <p class="text-blue-600 mt-3">
                Review skipped hosts →
            </p>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mt-6">
            {{ session('success') }}
        </div>
    @endif
@endsection
