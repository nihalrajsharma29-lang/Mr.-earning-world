@extends('layouts.app')

@section('title', 'Report Columns')
@section('page-heading', 'Report Columns')

@section('content')
    <div class="max-w-6xl mx-auto">
        @if(session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-900">Manage Report Columns</h1>
            <p class="mt-2 text-sm text-gray-600">Select the columns that should appear in report tables and Excel exports. Unchecked columns can be restored later.</p>
        </div>

        <form method="POST" action="{{ route('admin.report-columns.update') }}">
            @csrf
            @method('PUT')

            @foreach(['daily_report' => 'Daily Report', 'payment_report' => 'Payment Report', 'violation_records' => 'Violation Report'] as $type => $title)
                <section class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <h2 class="text-lg font-bold text-gray-900">{{ $title }}</h2>
                        <div class="flex items-center gap-4">
                            <button type="button" class="text-sm font-semibold text-blue-600 hover:text-blue-800" onclick="toggleReportColumns('{{ $type }}')">Toggle all</button>
                            <button type="button" class="text-sm font-semibold text-blue-600 hover:text-blue-800" onclick="toggleCreateColumn('{{ $type }}')">+ Create</button>
                        </div>
                    </div>
                    <div id="create-column-{{ $type }}" class="mb-4 hidden rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                            <input name="new_columns[{{ $type }}][label]" placeholder="Column header name" class="rounded-lg border border-gray-300 px-3 py-2">
                            <input name="new_columns[{{ $type }}][key]" placeholder="Source key, e.g. qualityscore" class="rounded-lg border border-gray-300 px-3 py-2">
                            <select name="new_columns[{{ $type }}][type]" class="rounded-lg border border-gray-300 px-3 py-2">
                                <option value="text">Text</option>
                                <option value="integer">Integer</option>
                                <option value="decimal">Decimal</option>
                                <option value="currency">Currency</option>
                                <option value="date">Date</option>
                                <option value="datetime">Date &amp; Time</option>
                                <option value="boolean">Yes / No</option>
                            </select>
                        </div>
                        <div class="mt-3 flex flex-wrap items-end gap-3">
                            <label class="text-sm font-semibold text-gray-700">Header location
                                <select name="new_columns[{{ $type }}][position]" class="ml-2 rounded-lg border border-gray-300 px-3 py-2 font-normal">
                                    @for($position = 1; $position <= count($columnsByType[$type] ?? []) + 1; $position++)
                                        <option value="{{ $position }}">Column {{ $position }}</option>
                                    @endfor
                                </select>
                            </label>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Add Column</button>
                        </div>
                        <p class="mt-2 text-xs text-gray-600">Source key Excel header se match hoga. Spaces/capital letters automatically normalized ho jayenge. Blank source key hone par column name se key banegi.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3" data-report-columns="{{ $type }}">
                        @foreach(($columnsByType[$type] ?? []) as $column)
                            <div class="relative flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-3 hover:bg-gray-50">
                                <input type="checkbox" name="visible[{{ $type }}][]" value="{{ $column->column_key }}" class="report-column-checkbox h-4 w-4 rounded border-gray-300 text-blue-600" {{ $column->is_visible ? 'checked' : '' }}>
                                <input type="text" name="labels[{{ $type }}][{{ $column->id }}]" value="{{ $column->label }}" class="min-w-0 flex-1 rounded border border-gray-300 px-2 py-1 text-sm text-gray-800">
                                <details class="relative">
                                    <summary class="cursor-pointer list-none rounded px-2 py-1 text-lg font-bold leading-none text-gray-500 hover:bg-gray-200 hover:text-gray-900" title="Manage column">&#8942;</summary>
                                    <div class="absolute right-0 z-10 mt-2 w-64 rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                                        <label class="mb-2 block text-xs font-semibold text-gray-600">Source key</label>
                                        <input type="text" name="source_keys[{{ $column->id }}]" value="{{ $column->column_key }}" class="mb-3 w-full rounded border border-gray-300 px-2 py-1 text-sm">
                                        <label class="mb-2 block text-xs font-semibold text-gray-600">Data type</label>
                                        <select name="types[{{ $column->id }}]" class="mb-3 w-full rounded border border-gray-300 px-2 py-1 text-sm">
                                            @foreach(['text' => 'Text', 'integer' => 'Integer', 'decimal' => 'Decimal', 'currency' => 'Currency', 'date' => 'Date', 'datetime' => 'Date & Time', 'boolean' => 'Yes / No'] as $value => $name)
                                                <option value="{{ $value }}" {{ $column->type === $value ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <label class="mb-2 block text-xs font-semibold text-gray-600">Header location</label>
                                        <select name="positions[{{ $column->id }}]" class="mb-3 w-full rounded border border-gray-300 px-2 py-1 text-sm">
                                            @for($position = 1; $position <= count($columnsByType[$type] ?? []); $position++)
                                                <option value="{{ $position }}" {{ $column->position === $position - 1 ? 'selected' : '' }}>Column {{ $position }}</option>
                                            @endfor
                                        </select>
                                        <button type="button" class="w-full rounded bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700" onclick="deleteReportColumn({{ $column->id }}, '{{ addslashes($column->label) }}')">Delete column</button>
                                    </div>
                                </details>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">Save Column Settings</button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function toggleReportColumns(reportType) {
        const checkboxes = document.querySelectorAll('[data-report-columns="' + reportType + '"] input');
        const shouldCheck = Array.from(checkboxes).some(function (checkbox) { return !checkbox.checked; });
        checkboxes.forEach(function (checkbox) { checkbox.checked = shouldCheck; });
    }

    function toggleCreateColumn(reportType) {
        document.getElementById('create-column-' + reportType)?.classList.toggle('hidden');
    }

    function deleteReportColumn(id, label) {
        if (!confirm('Delete the column "' + label + '"?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url('/admin/report-columns') }}/' + id;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush