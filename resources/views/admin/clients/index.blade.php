@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Client Management
    </h1>

    <a href="{{ route('clients.create') }}"
       class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
        + Add Client
    </a>

</div>

@if(session('success'))

    <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-5">
        {{ session('success') }}
    </div>

@endif

{{-- Search Box --}}
<div class="bg-white p-4 rounded-lg shadow mb-6">

    <form action="{{ route('clients.index') }}" method="GET"
          class="flex gap-3">

        <input
            type="text"
            name="search"
            value="{{ $search ?? '' }}"
            placeholder="Search by name, email, phone or company..."
            class="flex-1 border rounded-lg px-4 py-3"
        >

        <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
        >
            🔍 Search
        </button>

        @if(!empty($search))

            <a
                href="{{ route('clients.index') }}"
                class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600"
            >
                Clear
            </a>

        @endif

    </form>

</div>

<div class="bg-white shadow rounded-lg overflow-hidden">

    <div class="overflow-x-auto">
    <table class="w-full min-w-[1100px]">

        <thead class="bg-gray-200">

            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Phone</th>
                <th class="p-3 text-left">Company</th>
                <th class="p-3 text-left">Hosts</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Commission</th>
                <th class="p-3 text-left">Action</th>
            </tr>

        </thead>

        <tbody>

        @forelse($clients as $client)

            <tr class="border-b hover:bg-gray-50">

                <td class="p-3">
                    {{ $client->id }}
                </td>

                <td class="p-3 font-semibold">
                    {{ $client->name }}
                </td>

                <td class="p-3">
                    {{ $client->email }}
                </td>

                <td class="p-3">
                    {{ $client->phone }}
                </td>

                <td class="p-3">
                    {{ $client->company ?? '-' }}
                </td>

                <td class="p-3 font-semibold">
                    {{ $client->customers_count ?? 0 }}
                </td>

                <td class="p-3">

                    @if($client->status === 'Active')

                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                            Active
                        </span>

                    @else

                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                            Inactive
                        </span>

                    @endif

                </td>

                <td class="p-3">
                    <form action="{{ route('clients.update-commission', $client->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <input
                            type="number"
                            name="commission_percentage"
                            value="{{ old('commission_percentage', $client->commission_percentage ?? 0) }}"
                            min="0"
                            max="100"
                            step="0.01"
                            class="w-24 border rounded px-2 py-1 text-center font-semibold text-blue-700"
                            onchange="this.form.submit()"
                            title="Update commission for {{ $client->name }}"
                        >
                    </form>
                </td>

                <td class="p-3">

                    <div class="flex gap-2">

                        <a
                            href="{{ route('clients.edit', $client->id) }}"
                            class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600"
                        >
                            Edit
                        </a>

                        <form
                            action="{{ route('clients.destroy', $client->id) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this client?');"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                            >
                                Delete
                            </button>

                        </form>

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7" class="text-center p-8 text-gray-500">

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
@endsection