@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    Edit Client
</h1>

@if ($errors->any())

    <div class="bg-red-100 text-red-700 p-4 rounded mb-5">

        <ul>
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>

    </div>

@endif

<div class="bg-white p-6 rounded-lg shadow">

    <form action="{{ route('clients.update', $client->id) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="mb-4">

            <label class="block font-semibold mb-2">
                Name
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $client->name) }}"
                class="w-full border rounded p-3"
                required
            >

        </div>

        <div class="mb-4">

            <label class="block font-semibold mb-2">
                Email
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email', $client->email) }}"
                class="w-full border rounded p-3"
                required
            >

        </div>

        <div class="mb-4">

            <label class="block font-semibold mb-2">
                Phone
            </label>

            <input
                type="text"
                name="phone"
                value="{{ old('phone', $client->phone) }}"
                class="w-full border rounded p-3"
                required
            >

        </div>

        <div class="mb-4">

            <label class="block font-semibold mb-2">
                Company
            </label>

            <input
                type="text"
                name="company"
                value="{{ old('company', $client->company) }}"
                class="w-full border rounded p-3"
            >

        </div>

        <div class="mb-4">

            <label class="block font-semibold mb-2">
                Address
            </label>

            <textarea
                name="address"
                rows="4"
                class="w-full border rounded p-3"
            >{{ old('address', $client->address) }}</textarea>

        </div>

        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Status
            </label>

            <select
                name="status"
                class="w-full border rounded p-3"
            >

                <option value="Active"
                    {{ old('status', $client->status) === 'Active' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="Inactive"
                    {{ old('status', $client->status) === 'Inactive' ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>

        </div>

        {{-- Account actions: send reset / create login --}}
        <div class="mb-4">
            @if($client->user)
                <div class="bg-gray-50 p-4 rounded mb-3">
                    <strong>Login:</strong> {{ $client->user->email }}
                </div>

                <div class="flex gap-2 mb-4">
                    <form action="{{ route('clients.send-reset', $client) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Send Reset Link</button>
                    </form>

                    <form action="{{ route('clients.regen-password', $client) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Regenerate + Send Reset</button>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 p-4 rounded mb-3">
                    No login created for this client yet.
                </div>

                <form action="{{ route('clients.send-reset', $client) }}" method="POST" class="mb-4">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Create Login & Send Reset Link</button>
                </form>
            @endif
        </div>

        <div class="flex gap-3">

            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700"
            >
                Update Client
            </button>

            <a
                href="{{ route('clients.index') }}"
                class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection