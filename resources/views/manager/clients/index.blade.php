@extends('layouts.manager')

@section('title', 'Client List')
@section('page-heading', 'Client Management')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Client Management</h1>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('manager.clients.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Search by name, email, phone or company..."
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    🔍 Search
                </button>

                @if(!empty($search))
                    <a href="{{ route('manager.clients.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 font-semibold flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Company</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Hosts</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Commission</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $client->id }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $client->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $client->email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $client->phone }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $client->company ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $client->customers_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if(($client->status ?? 'Active') === 'Active')
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-blue-700">
                                    {{ number_format((float) ($client->commission_percentage ?? 0), 2) }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                    @if(!empty($search))
                                        No clients found for "{{ $search }}"
                                    @else
                                        No Clients Found
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
