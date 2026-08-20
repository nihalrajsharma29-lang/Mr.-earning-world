@extends(auth()->user()->role === 'manager' ? 'layouts.manager' : 'layouts.app')

@section('title', 'Skipped Host IDs')
@section('page-heading', 'Skipped Host IDs')

@push('styles')
<style>
    .panel { background: white; border: 1px solid #e5e7eb; border-radius: 18px; overflow: hidden; }
    .panel-body { padding: 22px; }
    .panel-title { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
    .panel-text { color: #6b7280; line-height: 1.6; margin-bottom: 20px; }
    .toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: end; margin-bottom: 20px; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label { font-weight: 700; color: #374151; font-size: 13px; }
    .field input, .field select { min-width: 190px; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; background: white; }
    .field input { min-width: 260px; }
    .btn { border: 0; border-radius: 10px; padding: 10px 16px; cursor: pointer; font-weight: 700; text-decoration: none; }
    .btn-primary { background: #2563eb; color: white; }
    .btn-secondary { background: #f3f4f6; color: #374151; }
    .table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 850px; }
    th, td { padding: 13px 14px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f9fafb; color: #6b7280; font-weight: 700; }
    .badge { display: inline-flex; padding: 5px 9px; border-radius: 999px; font-size: 12px; font-weight: 700; }
    .badge-daily { background: #dbeafe; color: #1e40af; }
    .badge-payment { background: #dcfce7; color: #166534; }
    .badge-violation { background: #fef3c7; color: #92400e; }
    .empty { padding: 45px 20px; text-align: center; color: #6b7280; }
    .pagination { margin-top: 18px; }
    .table-checkbox { width: 16px; height: 16px; cursor: pointer; }
</style>
@endpush

@section('content')
    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Skipped Host IDs</div>
            <p class="panel-text">Assign skipped host IDs to the correct client. After assignment, the host will be removed from this list.</p>

            @if(session('success'))
                <div style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 16px; margin-bottom: 18px;">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 16px; margin-bottom: 18px;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="GET" action="{{ route(auth()->user()->role === 'manager' ? 'manager.skipped-import-ids.index' : 'admin.skipped-import-ids.index') }}" class="toolbar">
                <div class="field">
                    <label for="search">Search Host ID</label>
                    <input id="search" name="search" value="{{ request('search') }}" placeholder="Enter Host ID">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route(auth()->user()->role === 'manager' ? 'manager.skipped-import-ids.index' : 'admin.skipped-import-ids.index') }}" class="btn btn-secondary">Clear</a>
                <select id="bulk-assign-client" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; min-width: 170px;">
                    <option value="">Select Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
                <button type="button" id="assign-selected-btn" class="btn btn-primary">Assign</button>
                <button type="button" id="delete-selected-btn" class="btn" style="background: #991b1b; color: white;">Delete</button>
            </form>

            <form id="bulk-assign-form" action="{{ route(auth()->user()->role === 'manager' ? 'manager.skipped-import-ids.reassign.selected' : 'admin.skipped-import-ids.reassign.selected') }}" method="POST" style="display: none;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="client_id" id="bulk-assign-client-input">
                <div id="bulk-assign-inputs"></div>
            </form>

            <form id="bulk-delete-form" action="{{ route(auth()->user()->role === 'manager' ? 'manager.skipped-import-ids.destroy.selected' : 'admin.skipped-import-ids.destroy.selected') }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
                <div id="bulk-delete-inputs"></div>
            </form>

            @if($skippedIds->count() > 0)
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-skipped" class="table-checkbox"></th>
                                <th>Host ID</th>
                                <th>Report Type</th>
                                <th>Reason</th>
                                <th>Skipped On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($skippedIds as $skippedId)
                                @php
                                    $badgeClass = match ($skippedId->report_type) {
                                        'payment_report' => 'badge-payment',
                                        'violation_records' => 'badge-violation',
                                        default => 'badge-daily',
                                    };
                                @endphp
                                <tr>
                                    <td><input type="checkbox" class="skipped-select-checkbox table-checkbox" value="{{ $skippedId->id }}"></td>
                                    <td><strong>{{ $skippedId->host_id }}</strong></td>
                                    <td><span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', ucwords($skippedId->report_type, '_')) }}</span></td>
                                    <td>{{ $skippedId->reason ?? '-' }}</td>
                                    <td>{{ $skippedId->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination">{{ $skippedIds->links() }}</div>
            @else
                <div class="empty">No skipped import IDs found.</div>
            @endif
        </div>
            <div class="p-4">
                {{ $skippedIds->links() }}
            </div>
    </div>
@endsection

@push('scripts')
<script>
    const selectAllSkipped = document.getElementById('select-all-skipped');
    const skippedCheckboxes = document.querySelectorAll('.skipped-select-checkbox');
    const assignSelectedBtn = document.getElementById('assign-selected-btn');
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    const bulkAssignClient = document.getElementById('bulk-assign-client');
    const bulkAssignClientInput = document.getElementById('bulk-assign-client-input');
    const bulkAssignInputs = document.getElementById('bulk-assign-inputs');
    const bulkAssignForm = document.getElementById('bulk-assign-form');
    const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

    function getSelectedSkippedIds() {
        return Array.from(skippedCheckboxes)
            .filter(function (checkbox) { return checkbox.checked; })
            .map(function (checkbox) { return checkbox.value; });
    }

    function fillSkippedInputs(container, ids) {
        container.innerHTML = '';
        ids.forEach(function (id) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'skipped_ids[]';
            input.value = id;
            container.appendChild(input);
        });
    }

    selectAllSkipped?.addEventListener('change', function () {
        skippedCheckboxes.forEach(function (checkbox) {
            checkbox.checked = selectAllSkipped.checked;
        });
    });

    skippedCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (selectAllSkipped) {
                selectAllSkipped.checked = Array.from(skippedCheckboxes).length > 0
                    && Array.from(skippedCheckboxes).every(function (item) { return item.checked; });
            }
        });
    });

    assignSelectedBtn?.addEventListener('click', function () {
        const selectedIds = getSelectedSkippedIds();

        if (selectedIds.length === 0) {
            alert('Please select at least one skipped Host ID.');
            return;
        }

        if (!bulkAssignClient?.value) {
            alert('Please select a client.');
            return;
        }

        if (!confirm('Assign selected Host IDs to this client?')) {
            return;
        }

        bulkAssignClientInput.value = bulkAssignClient.value;
        fillSkippedInputs(bulkAssignInputs, selectedIds);
        bulkAssignForm?.submit();
    });

    deleteSelectedBtn?.addEventListener('click', function () {
        const selectedIds = getSelectedSkippedIds();

        if (selectedIds.length === 0) {
            alert('Please select at least one skipped Host ID.');
            return;
        }

        if (!confirm('Delete selected skipped Host IDs?')) {
            return;
        }

        fillSkippedInputs(bulkDeleteInputs, selectedIds);
        bulkDeleteForm?.submit();
    });
</script>
@endpush