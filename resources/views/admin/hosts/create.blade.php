@extends(auth()->user()->role === 'manager' ? 'layouts.manager' : 'layouts.app')

@section('title', 'Add Host')
@section('page-heading', 'Add Host')

@push('styles')
<style>
    .form-card { background: white; border: 1px solid #e5e7eb; border-radius: 18px; padding: 28px; max-width: 900px; }
    .subtitle { color: #6b7280; margin: 8px 0 24px; }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    .field { display: flex; flex-direction: column; gap: 8px; }
    label { font-weight: 700; color: #111827; }
    input, textarea, select { border: 1px solid #d1d5db; border-radius: 10px; padding: 12px; font-size: 15px; }
    textarea { min-height: 140px; resize: vertical; }
    .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; }
    .btn { border: 0; border-radius: 10px; padding: 12px 18px; cursor: pointer; font-weight: 700; text-decoration: none; }
    .btn-primary { background: #2563eb; color: white; }
    .btn-secondary { background: #f3f4f6; color: #111827; }
    .alert { border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; }
    .alert-success { background: #dcfce7; color: #166534; }
    .alert-error { background: #fee2e2; color: #991b1b; }
    @media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    <div class="form-card">
        <h1 class="text-2xl font-bold">Add Host for Client</h1>
        <p class="subtitle">Admin or manager can add one or multiple hosts directly under the selected client.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px; list-style: disc;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route(auth()->user()->role === 'manager' ? 'manager.hosts.store' : 'admin.hosts.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label for="client_id">Client <span style="color:#ef4444">*</span></label>
                    <select id="client_id" name="client_id" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="country">Country <span style="color:#ef4444">*</span></label>
                    <select id="country" name="country" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ old('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field" style="grid-column: 1 / -1;">
                    <label for="customer_ids">Host IDs <span style="color:#ef4444">*</span></label>
                    <textarea id="customer_ids" name="customer_ids" placeholder="Enter Host IDs (comma or new line separated)" required>{{ old('customer_ids') }}</textarea>
                    <small style="color:#6b7280;">Maximum 50 IDs per submission. New hosts added by admin or manager are approved automatically.</small>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Add Hosts</button>
                <a href="{{ route(auth()->user()->role === 'manager' ? 'manager.dashboard' : 'admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
@endsection