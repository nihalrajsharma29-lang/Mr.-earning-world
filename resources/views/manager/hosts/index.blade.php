@extends('layouts.manager')

@section('title', 'Manager - Host Management')
@section('page-heading', 'Host Management')

@push('styles')
<style>
    .panel { background: white; border: 1px solid #e5e7eb; border-radius: 18px; margin-bottom: 24px; overflow: hidden; }
    .panel-body { padding: 22px; }
    .panel-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
    .panel-text { color: #6b7280; line-height: 1.7; margin-bottom: 18px; }
    .alert { border-radius: 14px; padding: 16px 18px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 980px; }
    th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f9fafb; color: #6b7280; font-weight: 700; }
    .badge { display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 999px; font-weight: 700; font-size: 12px; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .btn { border: none; border-radius: 10px; padding: 10px 14px; cursor: pointer; font-size: 13px; font-weight: 700; }
    .btn-approve { background: #16a34a; color: white; }
    .btn-reject { background: #dc2626; color: white; }
    .reject-form input { min-width: 180px; padding: 10px; border: 1px solid #d1d5db; border-radius: 10px; }
    .copy-host-id { border: 1px solid #d1d5db; border-radius: 6px; padding: 4px 7px; background: #f9fafb; color: #374151; cursor: pointer; font-size: 11px; }
    .copy-host-id:hover { background: #e5e7eb; }
    .host-id-cell { white-space: nowrap; }
    .action-cell { min-width: 260px; }
    .empty { padding: 50px 20px; text-align: center; color: #6b7280; }
    .toolbar { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 18px; align-items: center; }
    .toolbar form { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    @media (max-width: 840px) { .table-wrapper { min-width: 760px; } }
</style>
@endpush

@section('content')
    <div class="panel">
        <div class="panel-body">
            <div class="panel-title">Host Management</div>
            <p class="panel-text">Review hosts and approve or reject them from the manager portal.</p>

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

            <div class="toolbar">
                <form method="GET" action="{{ route('manager.hosts.index') }}">
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <label for="search" style="font-weight: 700; color: #374151;">Search</label>
                        <input id="search" name="search" value="{{ request('search') }}" placeholder="Host ID, client, name" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; min-width: 260px;">
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <label for="status" style="font-weight: 700; color: #374151;">Status</label>
                        <select id="status" name="status" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; min-width: 180px;">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-approve" style="background: #2563eb;">Filter</button>
                </form>

                <div class="bulk-actions" style="display: flex; gap: 8px; align-items: center;">
                    <select id="bulk-reassign-client" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; min-width: 170px;">
                        <option value="">Select Client</option>
                        @foreach($clients as $clientOption)
                            <option value="{{ $clientOption->id }}">{{ $clientOption->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="reassign-selected-btn" class="btn btn-approve" style="background: #0f766e;">Reassign</button>
                </div>

                <form id="bulk-reassign-form" action="{{ route('manager.hosts.reassign.selected') }}" method="POST" style="display: none;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="client_id" id="bulk-reassign-client-input">
                    <div id="bulk-reassign-inputs"></div>
                </form>
            </div>

            @if($hosts->count() > 0)
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-hosts" class="table-checkbox"></th>
                                <th>#</th>
                                <th>Host ID</th>
                                <th>Host</th>
                                <th>Client</th>
                                <th>Country</th>
                                <th>Submitted Date</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hosts as $host)
                                <tr>
                                    <td><input type="checkbox" class="host-select-checkbox table-checkbox" value="{{ $host->id }}"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="host-id-cell">
                                        <strong>{{ $host->customer_id ?? '-' }}</strong>
                                        @if($host->customer_id)
                                            <button type="button" class="copy-host-id" data-copy-id="{{ $host->customer_id }}" title="Copy Host ID">Copy</button>
                                        <td class="action-cell">
                                            <div class="actions">
                                                @if($host->approval_status === 'rejected')
                                                    <form action="{{ route('manager.hosts.destroy', $host) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-reject" onclick="return confirm('Delete this rejected host?')">Delete</button>
                                                    </form>
                                                @else
                                                    @if($host->approval_status !== 'approved')
                                                        <form action="{{ route('manager.hosts.approve', $host) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-approve">Approve</button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('manager.hosts.reject', $host) }}" method="POST" class="reject-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="text" name="rejection_reason" placeholder="Enter reason" required maxlength="1000" aria-label="Rejection reason for {{ $host->customer_id }}">
                                                        <button type="submit" class="btn btn-reject">Reject</button>
                                                    </form>
                                                @endif
                                    </td>
                                        </td>
                                    <td>{{ $host->rejection_reason ?: '-' }}</td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty">
                        <div class="empty-icon">📭</div>
                        <h3>No hosts found</h3>
                        <p>Use the filters above to search by host, client, name, or status.</p>
                    </div>
                @endif
            </div>
        </div>
                                    <td>
                                        <div class="actions">
                                            @if($host->approval_status === 'rejected')
                                                <form action="{{ route('manager.hosts.destroy', $host) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-reject" onclick="return confirm('Delete this rejected host?')">Delete</button>
                                                </form>
                                            @elseif($host->approval_status !== 'approved')
                                                <form action="{{ route('manager.hosts.approve', $host) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-approve">Approve</button>
                                                </form>
@endsection

@push('scripts')
<script>
    const selectAllHosts = document.getElementById('select-all-hosts');
    const hostCheckboxes = document.querySelectorAll('.host-select-checkbox');
    const reassignSelectedBtn = document.getElementById('reassign-selected-btn');
    const bulkReassignClient = document.getElementById('bulk-reassign-client');
    const bulkReassignClientInput = document.getElementById('bulk-reassign-client-input');
    const bulkReassignInputs = document.getElementById('bulk-reassign-inputs');
    const bulkReassignForm = document.getElementById('bulk-reassign-form');

    function getSelectedHostIds() {
        return Array.from(hostCheckboxes)
            .filter(function (checkbox) { return checkbox.checked; })
            .map(function (checkbox) { return checkbox.value; });
    }

    function fillHiddenInputs(container, ids) {
        container.innerHTML = '';
        ids.forEach(function (id) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'host_ids[]';
            input.value = id;
            container.appendChild(input);
        });
    }

    selectAllHosts?.addEventListener('change', function () {
        hostCheckboxes.forEach(function (checkbox) {
            checkbox.checked = selectAllHosts.checked;
        });
    });

    hostCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (selectAllHosts) {
                selectAllHosts.checked = Array.from(hostCheckboxes).every(function (item) { return item.checked; });
            }
        });
    });

    reassignSelectedBtn?.addEventListener('click', function () {
        const selectedIds = getSelectedHostIds();

        if (selectedIds.length === 0) {
            alert('Please select at least one host.');
            return;
        }

        if (!bulkReassignClient?.value) {
            alert('Please select a client.');
            return;
        }

        if (!confirm('Reassign selected hosts to this client?')) {
            return;
        }

        bulkReassignClientInput.value = bulkReassignClient.value;
        fillHiddenInputs(bulkReassignInputs, selectedIds);
        bulkReassignForm?.submit();
    });

    document.querySelectorAll('.copy-host-id').forEach(function (button) {
        button.addEventListener('click', async function () {
            const hostId = button.dataset.copyId;

            try {
                await navigator.clipboard.writeText(hostId);
            } catch (error) {
                const fallbackInput = document.createElement('textarea');
                fallbackInput.value = hostId;
                document.body.appendChild(fallbackInput);
                fallbackInput.select();
                document.execCommand('copy');
                fallbackInput.remove();
            }

            button.textContent = 'Copied';
            setTimeout(function () { button.textContent = 'Copy'; }, 1200);
        });
    });
</script>
@endpush
