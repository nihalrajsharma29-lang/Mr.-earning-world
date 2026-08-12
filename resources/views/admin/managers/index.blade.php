@extends('layouts.app')

@section('title', 'Manage Managers')
@section('page-heading', 'Manage Managers')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900">Create Manager</h2>
            <p class="text-sm text-gray-600 mt-2">Create a manager account with login details for the manager portal.</p>

            @if (session('success'))
                <div class="mt-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.managers.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Manager Name</label>
                    <input id="name" name="name" type="text" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Login ID</label>
                    <input id="email" name="email" type="email" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">
                    Create Manager
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-semibold text-gray-900">Existing Managers</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3">Name</th>
                            <th class="text-left px-4 py-3">Login ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($managers as $manager)
                            <tr class="border-t border-gray-200">
                                <td class="px-4 py-3">{{ $manager->name }}</td>
                                <td class="px-4 py-3">{{ $manager->email }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-gray-500">No managers created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
