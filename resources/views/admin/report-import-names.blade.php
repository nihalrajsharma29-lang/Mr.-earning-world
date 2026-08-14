@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-3xl font-bold mb-2">Report File Naming</h1>
        <p class="text-gray-600 mb-6">
            Set the required text that must appear in uploaded file names for each report type.
            If a file name does not contain this value, the import will be rejected.
        </p>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-5">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.report-import-names.store') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="daily_report" class="block font-semibold mb-2">Daily Report</label>
                    <input type="text" id="daily_report" name="daily_report" value="{{ old('daily_report', $names['daily_report'] ?? '') }}" class="w-full border rounded p-3" placeholder="Example: 4280121896">
                </div>

                <div>
                    <label for="payment_report" class="block font-semibold mb-2">Payment Report</label>
                    <input type="text" id="payment_report" name="payment_report" value="{{ old('payment_report', $names['payment_report'] ?? '') }}" class="w-full border rounded p-3" placeholder="Example: Payment Report">
                </div>

                <div>
                    <label for="violation_records" class="block font-semibold mb-2">Violation Records</label>
                    <input type="text" id="violation_records" name="violation_records" value="{{ old('violation_records', $names['violation_records'] ?? '') }}" class="w-full border rounded p-3" placeholder="Example: Strike Records">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded hover:bg-blue-700">Save File Names</button>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-5 py-3 rounded hover:bg-gray-600">Back to Dashboard</a>
            </div>
        </form>
    </div>
@endsection
