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