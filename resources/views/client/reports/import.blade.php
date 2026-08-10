@extends('layouts.client')

@section('title', 'Import Daily Reports')
@section('page-heading', 'Import Daily Reports')

@push('styles')
<style>
    .import-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
        max-width: 760px;
        margin: 0 auto;
    }

    .import-card h1 {
        margin: 0 0 10px;
        font-size: 28px;
    }

    .import-card p {
        color: #6b7280;
        margin-bottom: 22px;
        line-height: 1.6;
    }

    .alert {
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
    }

    input[type="file"] {
        width: 100%;
        padding: 14px 12px;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        background: #ffffff;
        font-size: 14px;
    }

    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .btn-primary {
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 22px;
        cursor: pointer;
        font-weight: 700;
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        padding: 12px 20px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #111827;
        text-decoration: none;
        font-weight: 700;
    }

    @media (max-width: 720px) {
        .import-card { padding: 20px; }
        .actions { flex-direction: column; align-items: stretch; }
    }
</style>
@endpush

@section('content')
    <div class="import-card">
        <h1>📥 Import Daily Reports</h1>
        <p>Upload your Excel file to add daily host reports. Supported formats: XLSX, XLS, CSV.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please fix the following issues:</strong>
                <ul style="margin: 10px 0 0 18px; padding: 0; list-style: disc;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('client.daily.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="file">Select Excel File</label>
                <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn-primary">📤 Import Reports</button>
                <a href="{{ route('client.daily.reports') }}" class="btn-secondary">← Back to Reports</a>
            </div>
        </form>
    </div>
@endsection
