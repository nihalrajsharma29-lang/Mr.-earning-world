@extends('layouts.client')

@section('title', 'Bank Card')
@section('page-heading', 'Bank Card')

@push('styles')
<style>
    .bank-card { background: white; border-radius: 18px; border: 1px solid #e5e7eb; box-shadow: 0 14px 38px rgba(15, 23, 42, 0.06); padding: 28px; }
    .subtitle { color: #6b7280; margin: 0 0 24px; }
    .alert { padding: 16px; border-radius: 12px; margin-bottom: 20px; }
    .alert-success { background: #dcfce7; color: #166534; }
    .alert-error { background: #fee2e2; color: #991b1b; }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    .field { display: flex; flex-direction: column; gap: 8px; }
    label { font-weight: 700; color: #111827; }
    input { width: 100%; border: 1px solid #d1d5db; border-radius: 12px; padding: 14px 15px; font-size: 15px; background: #fff; }
    input[readonly] { background: #f9fafb; color: #374151; }
    .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 26px; }
    .btn-primary { background: #2563eb; color: white; padding: 14px 22px; border: none; border-radius: 12px; cursor: pointer; font-weight: 700; }
    .btn-secondary { background: #f3f4f6; color: #111827; padding: 14px 22px; border: none; border-radius: 12px; cursor: pointer; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; }
    .helper-text { color: #6b7280; font-size: 12px; margin-top: 4px; }
    @media (max-width: 800px) { .form-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    @php
        $hasBankDetails = !empty($client?->bank_account_number) || !empty($client?->bank_ifsc_code) || !empty($client?->bank_name) || !empty($client?->bank_address);
        $showForm = ! $hasBankDetails || request()->boolean('edit');
    @endphp

    <div class="bank-card">
        <h1>{{ $hasBankDetails && ! $showForm ? 'Bank Card' : 'Add Bank Card' }}</h1>
        <p class="subtitle">
            {{ $showForm ? 'Add your bank account details for payouts and verification.' : 'Your saved bank details are displayed below and can be updated anytime.' }}
        </p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 18px; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($showForm)
            <form action="{{ route('client.bank-card.store') }}" method="POST" id="bankCardForm">
                @csrf
                <div class="form-grid">
                    <div class="field">
                        <label for="account_holder_name">Account Holder Name</label>
                        <input id="account_holder_name" name="account_holder_name" type="text" value="{{ old('account_holder_name', $client?->bank_account_holder_name ?? '') }}" placeholder="Enter account holder name" required>
                    </div>

                    <div class="field">
                        <label for="account_number">Account Number</label>
                        <input id="account_number" name="account_number" type="text" value="{{ old('account_number', $client?->bank_account_number ?? '') }}" placeholder="Enter account number" required>
                    </div>

                    <div class="field">
                        <label for="confirm_account_number">Confirm Account Number</label>
                        <input id="confirm_account_number" name="confirm_account_number" type="text" value="{{ old('confirm_account_number', $client?->bank_account_number ?? '') }}" placeholder="Re-enter account number" required>
                    </div>

                    <div class="field">
                        <label for="ifsc_code">IFSC Code</label>
                        <input id="ifsc_code" name="ifsc_code" type="text" value="{{ old('ifsc_code', $client?->bank_ifsc_code ?? '') }}" placeholder="e.g. ICIC0001234" maxlength="11" required style="text-transform: uppercase;">
                        <div class="helper-text">Type the IFSC code and the bank name and address will be filled automatically.</div>
                    </div>

                    <div class="field">
                        <label for="bank_name">Bank Name</label>
                        <input id="bank_name" name="bank_name" type="text" value="{{ old('bank_name', $client?->bank_name ?? '') }}" readonly placeholder="Bank name will appear here">
                    </div>

                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="bank_address">Bank Address</label>
                        <input id="bank_address" name="bank_address" type="text" value="{{ old('bank_address', $client?->bank_address ?? '') }}" readonly placeholder="Bank address will appear here">
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn-primary">Save Bank Details</button>
                    <a href="{{ route('client.dashboard') }}" class="btn-secondary">Back to Dashboard</a>
                </div>
            </form>
        @else
            <div class="form-grid" style="margin-top: 12px;">
                <div class="field">
                    <label>Account Holder Name</label>
                    <input type="text" value="{{ $client->bank_account_holder_name ?? '-' }}" readonly>
                </div>

                <div class="field">
                    <label>Account Number</label>
                    <input type="text" value="{{ substr($client->bank_account_number, 0, 4) . str_repeat('*', max(0, strlen($client->bank_account_number) - 4)) }}" readonly>
                </div>

                <div class="field">
                    <label>IFSC Code</label>
                    <input type="text" value="{{ $client->bank_ifsc_code }}" readonly>
                </div>

                <div class="field">
                    <label>Bank Name</label>
                    <input type="text" value="{{ $client->bank_name }}" readonly>
                </div>

                <div class="field" style="grid-column: 1 / -1;">
                    <label>Bank Address</label>
                    <input type="text" value="{{ $client->bank_address }}" readonly>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('client.bank-card', ['edit' => 1]) }}" class="btn-secondary">Edit Details</a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ifscInput = document.getElementById('ifsc_code');
            const bankNameInput = document.getElementById('bank_name');
            const bankAddressInput = document.getElementById('bank_address');

            if (!ifscInput || !bankNameInput || !bankAddressInput) {
                return;
            }

            const fetchBankDetails = function () {
                const code = ifscInput.value.trim();
                if (!code || code.length < 4) {
                    return;
                }

                const url = 'https://ifsc.razorpay.com/' + code.toUpperCase();

                fetch(url)
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('IFSC not found');
                        }

                        return response.json();
                    })
                    .then(function (data) {
                        const bankName = data && data.BANK ? data.BANK : '';
                        const branchAddress = data && data.ADDRESS ? data.ADDRESS : '';

                        if (bankName) {
                            bankNameInput.value = bankName;
                        }

                        if (branchAddress) {
                            bankAddressInput.value = branchAddress;
                        }
                    })
                    .catch(function () {
                        bankNameInput.value = bankNameInput.value || '';
                        bankAddressInput.value = bankAddressInput.value || '';
                    });
            };

            ifscInput.addEventListener('blur', fetchBankDetails);
            ifscInput.addEventListener('change', fetchBankDetails);
        });
    </script>
@endsection
