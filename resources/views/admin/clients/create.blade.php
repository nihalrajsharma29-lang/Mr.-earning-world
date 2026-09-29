@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    Add New Client
</h1>

<div class="mb-4 p-4 rounded border border-yellow-300 bg-yellow-50 text-yellow-900">
    <strong>Note:</strong> Create a client here, or copy an invitation link for a client to complete their own account form.
</div>

@if ($errors->any())
<div class="bg-red-100 text-red-700 p-4 rounded mb-5">
    <ul>
        @foreach ($errors->all() as $error)
            <li>• {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('clients.store') }}" method="POST" class="bg-white p-6 rounded shadow">

    @csrf

    <div class="mb-4">
        <label class="block font-semibold mb-2">Name</label>
        <input
            type="text"
            name="name"
            class="w-full border rounded p-3"
            value="{{ old('name') }}"
        >
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Email</label>
        <input
            type="email"
            name="email"
            class="w-full border rounded p-3"
            value="{{ old('email') }}"
        >
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Phone</label>
        <input
            type="text"
            name="phone"
            class="w-full border rounded p-3"
            value="{{ old('phone') }}"
        >
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Company</label>
        <input
            type="text"
            name="company"
            class="w-full border rounded p-3"
            value="{{ old('company') }}"
        >
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Address</label>
        <textarea
            name="address"
            class="w-full border rounded p-3"
            rows="4"
        >{{ old('address') }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Status</label>

        <select name="status" class="w-full border rounded p-3">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Commission (%)</label>
        <input
            type="number"
            name="commission_percentage"
            class="w-full border rounded p-3"
            value="{{ old('commission_percentage', 0) }}"
            min="0"
            max="100"
            step="0.01"
            placeholder="0.00"
        >
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Account</label>
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="create_user" value="1" checked>
            <span>Create login for client</span>
        </label>
        <label class="inline-flex items-center gap-2 mt-2">
            <input type="checkbox" name="send_reset" value="1" checked>
            <span>Send password reset link to client</span>
        </label>
        <p class="text-sm text-gray-600 mt-3">
            Optional: set a password now and the client can login immediately with it. If you don't set a password, they will receive a reset link to choose one themselves. The client can change their password later from their profile.
        </p>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Password (optional)</label>
        <input
            type="password"
            name="password"
            class="w-full border rounded p-3"
            value=""
            autocomplete="new-password"
            placeholder="Set a login password for the client"
        >
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-2">Confirm Password</label>
        <input
            type="password"
            name="password_confirmation"
            class="w-full border rounded p-3"
            value=""
            autocomplete="new-password"
            placeholder="Confirm password"
        >
    </div>

    <button
        type="submit"
        class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700"
    >
        Save Client
    </button>

    <button
        type="button"
        id="copy-client-invite"
        data-invite-url="{{ $inviteUrl }}"
        class="ml-2 border border-blue-600 text-blue-700 px-6 py-3 rounded hover:bg-blue-50"
    >
        Copy Invite Link
    </button>
    <span id="invite-copy-status" class="ml-2 text-sm text-green-700" role="status" aria-live="polite"></span>

</form>

@push('scripts')
<script>
    document.getElementById('copy-client-invite')?.addEventListener('click', async function () {
        const inviteUrl = this.dataset.inviteUrl;

        try {
            await navigator.clipboard.writeText(inviteUrl);
        } catch (error) {
            const temporaryInput = document.createElement('textarea');
            temporaryInput.value = inviteUrl;
            temporaryInput.style.position = 'fixed';
            temporaryInput.style.opacity = '0';
            document.body.appendChild(temporaryInput);
            temporaryInput.select();
            document.execCommand('copy');
            temporaryInput.remove();
        }

        document.getElementById('invite-copy-status').textContent = 'Invite link copied. Valid for 7 days.';
    });
</script>
@endpush

@endsection