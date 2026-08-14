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

        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Commission (%)
            </label>

            <input
                type="number"
                name="commission_percentage"
                value="{{ old('commission_percentage', $client->commission_percentage ?? 0) }}"
                class="w-full border rounded p-3"
                min="0"
                max="100"
                step="0.01"
                placeholder="0.00"
            >

        </div>

        <div class="mb-6">
            <div class="bg-gray-50 p-4 rounded mb-3">
                <strong>Login:</strong> {{ $client->user ? $client->user->email : 'No login created for this client yet.' }}
            </div>

            <div class="border rounded p-4 bg-white">
                <h3 class="text-lg font-semibold mb-3">Admin Password Update</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Set a new password for this client account so the client can log in if they forgot their password.
                </p>

                <form action="{{ route('clients.update-password', $client) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-semibold mb-2">New Password</label>
                        <input
                            type="password"
                            name="password"
                            class="w-full border rounded p-3"
                            placeholder="Enter new password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold mb-2">Confirm Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full border rounded p-3"
                            placeholder="Confirm new password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        >
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700">
                        Update Client Password
                    </button>
                </form>
            </div>
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