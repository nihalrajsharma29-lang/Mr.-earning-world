@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-4">Admin Audit Log</h1>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="action" value="{{ request('action') }}" placeholder="Action" class="border rounded p-2">
    <input type="text" name="admin_id" value="{{ request('admin_id') }}" placeholder="Admin ID" class="border rounded p-2">
    <input type="text" name="client_id" value="{{ request('client_id') }}" placeholder="Client ID" class="border rounded p-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    <a href="{{ route('admin.audit') }}" class="bg-gray-200 px-4 py-2 rounded">Reset</a>
</form>

<div class="bg-white shadow rounded">
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Time</th>
                    <th class="px-4 py-2">Admin</th>
                    <th class="px-4 py-2">Client</th>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Details</th>
                    <th class="px-4 py-2">IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $log->id }}</td>
                        <td class="px-4 py-2">{{ $log->created_at }}</td>
                        <td class="px-4 py-2">{{ $log->admin?->name }} ({{ $log->admin_id }})</td>
                        <td class="px-4 py-2">{{ $log->client?->name }} ({{ $log->client_id }})</td>
                        <td class="px-4 py-2">{{ $log->action }}</td>
                        <td class="px-4 py-2">{{ $log->details }}</td>
                        <td class="px-4 py-2">{{ $log->ip }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
