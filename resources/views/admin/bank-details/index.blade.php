@extends('layouts.app')

@section('title', 'Bank Details')
@section('page-heading', 'Bank Details')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bank Details</h1>
                <p class="text-sm text-gray-500">Client bank information submitted from the client portal.</p>
            </div>
            <a href="{{ route('admin.bank-details.export') }}" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                Export to Excel
            </a>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-5 rounded-lg bg-green-100 border border-green-200 text-green-700 px-4 py-3">
                {{ session('success') }}
            </div>
            <div class="p-4">
                {{ $clients->links() }}
            </div>
        @endif

        @if($clients->isEmpty())
            <div class="p-10 text-center text-gray-500">
                No client bank details available yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Weekly Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Account Holder Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Account Number</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">IFSC</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Bank Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Address</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Total Agent Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    @if($client->payment_weekly_date)
                                        {{ \Illuminate\Support\Carbon::parse($client->payment_weekly_date)->format('d-M-Y') }} to {{ \Illuminate\Support\Carbon::parse($client->payment_weekly_date)->addDays(6)->format('d-M-Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $client->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                    {{ $client->bank_account_holder_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium break-all">
                                    {{ $client->bank_account_number ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $client->bank_ifsc_code ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $client->bank_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 max-w-md">
                                    {{ $client->bank_address ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    ${{ number_format((float) ($client->total_agent_salary ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('admin.bank-details.transfer-status', $client) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="transfer_status" onchange="this.form.submit()" class="rounded-md border-gray-300 text-xs font-medium {{ $client->transfer_status === 'transferred' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}" title="Transfer status">
                                                <option value="pending" {{ ($client->transfer_status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="transferred" {{ $client->transfer_status === 'transferred' ? 'selected' : '' }}>Transferred</option>
                                            </select>
                                        </form>
                                        @if($client->bank_account_number)
                                            <button
                                                type="button"
                                                class="copy-account-btn inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-2 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                                data-account-holder="{{ $client->bank_account_holder_name ?? '' }}"
                                                data-account-number="{{ $client->bank_account_number ?? '' }}"
                                                data-ifsc="{{ $client->bank_ifsc_code ?? '' }}"
                                                data-client-name="{{ $client->name ?? '' }}"
                                                data-total-agent-salary="{{ number_format((float) ($client->total_agent_salary ?? 0), 2) }}"
                                                data-weekly-date="{{ $client->payment_weekly_date ? \Illuminate\Support\Carbon::parse($client->payment_weekly_date)->format('d-M-Y') . ' to ' . \Illuminate\Support\Carbon::parse($client->payment_weekly_date)->addDays(6)->format('d-M-Y') : '-' }}"
                                                title="Copy account details"
                                            >
                                                Copy
                                            </button>
                                        @endif

                                        <form action="{{ route('admin.bank-details.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Delete this client bank details?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.copy-account-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    const accountHolder = button.getAttribute('data-account-holder') || '';
                    const accountNumber = button.getAttribute('data-account-number') || '';
                    const ifsc = button.getAttribute('data-ifsc') || '';
                    const clientName = button.getAttribute('data-client-name') || '';
                    const totalAgentSalary = button.getAttribute('data-total-agent-salary') || '0.00';
                    const weeklyDate = button.getAttribute('data-weekly-date') || '-';

                    const text = [
                        'Account Holder Name - ' + accountHolder,
                        'Account Number - ' + accountNumber,
                        'IFSC - ' + ifsc,
                        'Client Name - ' + clientName,
                        'Weekly Date - ' + weeklyDate,
                        'Total Agent Salary - $' + totalAgentSalary
                    ].join('\n');

                    navigator.clipboard.writeText(text)
                        .then(function () {
                            const previousText = button.textContent;
                            button.textContent = 'Copied';
                            button.disabled = true;

                            setTimeout(function () {
                                button.textContent = previousText;
                                button.disabled = false;
                            }, 1200);
                        })
                        .catch(function () {
                            button.textContent = 'Failed';
                            setTimeout(function () {
                                button.textContent = 'Copy';
                            }, 1200);
                        });
                });
            });
        });
    </script>
@endsection
