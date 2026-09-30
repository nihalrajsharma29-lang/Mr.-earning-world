@extends('layouts.app')

@section('title', 'Manage Report Pages')
@section('page-heading', 'Manage Report Pages')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-xl font-bold text-gray-900">Add Report Page</h1>
            <p class="mt-1 text-sm text-gray-600">Create a report page for another Excel report type.</p>
            <form method="POST" action="{{ route('admin.report-types.store') }}" class="mt-4 flex flex-wrap gap-3">
                @csrf
                <input name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Page name, e.g. Quality Report" class="min-w-0 flex-1 rounded-md border border-gray-300 px-3 py-2">
                <button class="rounded-md bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Add Page</button>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="font-bold text-gray-900">Report Pages</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($reportTypes as $reportType)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                        <div class="min-w-0 flex-1">
                            <form method="POST" action="{{ route('admin.report-types.update', $reportType) }}" class="flex flex-wrap items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input name="name" value="{{ old('name', $reportType->name) }}" required maxlength="100" aria-label="Report page name" class="min-w-0 flex-1 rounded-md border border-gray-300 px-3 py-2 font-semibold text-gray-900">
                                <button class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Save Name</button>
                            </form>
                            <div class="mt-1 text-sm text-gray-500">{{ $reportType->is_active ? 'Active' : 'Removed' }}{{ $reportType->is_system ? ' · Built-in' : '' }}</div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('admin.report-types.clear-data', $reportType) }}" onsubmit="return confirm('Clear all imported data for this page? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-md border border-amber-200 px-3 py-2 text-sm font-semibold text-amber-800 hover:bg-amber-50">Clear Data</button>
                            </form>
                            @if($reportType->is_active)
                                <form method="POST" action="{{ route('admin.report-types.destroy', $reportType) }}" onsubmit="return confirm('Remove this report page from navigation? Imported reports and column settings will be preserved.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Remove Page</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.report-types.restore', $reportType) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-md border border-blue-200 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">Restore Page</button>
                                </form>
                                <form method="POST" action="{{ route('admin.report-types.force-destroy', $reportType) }}" onsubmit="return confirm('Permanently delete this page, all imported reports, columns, and filename settings? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Permanent Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection