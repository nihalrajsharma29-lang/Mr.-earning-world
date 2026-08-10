@extends('layouts.client')

@section('title', 'Add Host')
@section('page-heading', 'Add Host')

@push('styles')
<style>
    .form-card { background: white; border-radius: 16px; padding: 28px; border: 1px solid #e5e7eb; box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06); }
    .subtitle { color: #6b7280; margin-bottom: 24px; }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    .field { display: flex; flex-direction: column; gap: 8px; }
    label { font-weight: 700; color: #111827; }
    input, textarea { border: 1px solid #d1d5db; border-radius: 12px; padding: 14px; font-size: 15px; }
    textarea { min-height: 128px; resize: vertical; }
    .actions { display: flex; gap: 12px; flex-wrap: wrap; }
    .btn-primary { background: #2563eb; color: white; padding: 14px 22px; border: none; border-radius: 12px; cursor: pointer; font-weight: 700; }
    .btn-secondary { background: #f3f4f6; color: #111827; padding: 14px 22px; border: none; border-radius: 12px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
    .alert { padding: 16px; border-radius: 12px; margin-bottom: 20px; }
    .alert-success { background: #dcfce7; color: #166534; }
    .alert-error { background: #fee2e2; color: #991b1b; }
    @media (max-width: 800px) { .form-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    <div class="form-card">
        <h1>Add a New Host</h1>
        <p class="subtitle">Submit host details to request approval from the admin team.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul class="mt-2" style="padding-left: 18px; margin-top: 10px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('client.hosts.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label>Host ID <span style="color:#ef4444">*</span></label>
                    <input type="text" name="customer_id" value="{{ old('customer_id') }}" placeholder="Enter Host ID" required>
                </div>
                <div class="field">
                    <label>Country <span style="color:#ef4444">*</span></label>
                    <select name="country" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $c)
                            <option value="{{ $c }}" {{ old('country') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary">Submit Host</button>
                <a href="{{ route('client.dashboard') }}" class="btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
@endsection
