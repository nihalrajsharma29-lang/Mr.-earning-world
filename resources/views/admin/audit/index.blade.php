@extends('layouts.app')

@section('title', 'Audit Log')
@section('page-heading', 'Admin Audit Log')

@section('content')
    <div class="max-w-full mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Admin Audit Log</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                    <input type="text" name="action" value="{{ request('action') }}" placeholder="Action" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manager ID</label>
                    <input type="text" name="admin_id" value="{{ request('admin_id') }}" placeholder="Manager ID" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                    <input type="text" name="client_id" value="{{ request('client_id') }}" placeholder="Client ID" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 font-medium">Filter</button>
                    <a href="{{ route('admin.audit') }}" class="bg-gray-200 text-gray-800 px-4 py-2.5 rounded-lg hover:bg-gray-300 font-medium flex items-center justify-center">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Time</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Manager</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Client</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Details</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $log->id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $log->admin?->name ?? 'Unknown' }}
                                    @if($log->admin_id)
                                        <span class="text-gray-500">({{ $log->admin_id }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $log->client?->name ?? 'N/A' }}
                                    @if($log->client_id)
                                        <span class="text-gray-500">({{ $log->client_id }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 max-w-xl">{{ $log->details ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $log->ip ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">No audit log entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
