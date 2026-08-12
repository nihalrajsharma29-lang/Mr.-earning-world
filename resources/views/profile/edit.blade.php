@extends('layouts.app')

@section('title', 'Manage Account')
@section('page-heading', 'Manage Account')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900">Admin Account Management</h2>
            <p class="text-sm text-gray-600 mt-2">
                Update your admin name, login ID, and password to keep your dashboard secure.
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-semibold text-gray-900">Manager Accounts</h3>
            <p class="text-sm text-gray-600 mt-2">Create and manage manager login details from here.</p>

            @if (session('success_manager'))
                <div class="mt-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success_manager') }}
                </div>
            @endif

            @if (session('error_manager'))
                <div class="mt-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error_manager') }}
                </div>
            @endif

            <button type="button" id="toggle-manager-form" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">
                Create Manager
            </button>

            <div id="manager-create-form" class="hidden mt-6 border border-gray-200 rounded-xl p-5 bg-gray-50">
                <form method="POST" action="{{ route('admin.managers.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="manager-name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input id="manager-name" name="name" type="text" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="manager-email" class="block text-sm font-medium text-gray-700">Login Id</label>
                        <input id="manager-email" name="email" type="email" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="manager-password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="manager-password" name="password" type="password" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="manager-password-confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="manager-password-confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">
                            Create Manager
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-8">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Existing Managers</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left px-4 py-3">Name</th>
                                <th class="text-left px-4 py-3">Login ID</th>
                                <th class="text-left px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(App\Models\User::where('role', 'manager')->latest()->get() as $manager)
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3">{{ $manager->name }}</td>
                                    <td class="px-4 py-3">{{ $manager->email }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" class="bg-yellow-500 text-white px-3 py-1.5 rounded-md text-sm hover:bg-yellow-600" onclick="document.getElementById('edit-manager-{{ $manager->id }}').classList.toggle('hidden')">
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.managers.destroy', $manager) }}" onsubmit="return confirm('Are you sure you want to delete this manager?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-red-700">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>

                                        <div id="edit-manager-{{ $manager->id }}" class="hidden mt-4 border border-gray-200 rounded-lg p-4 bg-gray-50">
                                            <form method="POST" action="{{ route('admin.managers.update', $manager) }}" class="space-y-3">
                                                @csrf
                                                @method('PUT')

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                                    <input type="text" name="name" value="{{ $manager->name }}" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Login Id</label>
                                                    <input type="email" name="email" value="{{ $manager->email }}" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                                                    <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Leave blank to keep existing password" />
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                                    <input type="password" name="password_confirmation" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                                </div>

                                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">
                                                    Save Changes
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-gray-500">No managers created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trigger = document.getElementById('toggle-manager-form');
            const formBlock = document.getElementById('manager-create-form');

            if (trigger && formBlock) {
                trigger.addEventListener('click', function () {
                    formBlock.classList.toggle('hidden');
                });
            }
        });
    </script>
@endsection
