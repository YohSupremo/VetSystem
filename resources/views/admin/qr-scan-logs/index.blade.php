@extends('admin.dashboard')

@section('page-title', 'QR Scan Logs')
@section('page-description', 'Review historical QR scan activity for cages and pets')

@section('content')
<style>
    .qr-page {
        max-width: 1400px;
        margin: 0 auto;
    }

    .qr-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
    }

    .qr-toolbar h3 {
        margin: 0;
        font-family: 'Fredoka', sans-serif;
        font-size: 28px;
        color: var(--dark-text);
    }

    .qr-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 10px;
        width: 100%;
        margin-top: 10px;
    }

    .qr-filters input,
    .qr-filters select {
        padding: 10px 12px;
        border-radius: 12px;
        border: 2px solid var(--soft-gray);
        background: var(--white);
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
    }

    .btn-filter,
    .btn-clear {
        padding: 10px 14px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-filter {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        color: #fff;
    }

    .btn-clear {
        background: var(--white);
        color: var(--dark-text);
        border: 2px solid var(--soft-gray);
    }

    .qr-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .qr-table thead th {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 12px;
        color: var(--light-text);
        padding: 0 14px 8px 14px;
        text-align: left;
    }

    .qr-table tbody tr {
        background: var(--white);
        box-shadow: var(--shadow-soft);
        border-radius: 16px;
    }

    .qr-table tbody td {
        padding: 14px;
        font-size: 14px;
        color: var(--dark-text);
        vertical-align: top;
    }

    .scan-type {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .scan-type.pet {
        background: rgba(59, 130, 246, 0.15);
        color: #2563EB;
    }

    .scan-type.cage {
        background: rgba(168, 85, 247, 0.16);
        color: #7E22CE;
    }

    .muted-sm {
        color: var(--light-text);
        font-size: 12px;
    }

    .empty-card {
        background: var(--white);
        border-radius: 18px;
        padding: 36px;
        text-align: center;
        box-shadow: var(--shadow-soft);
    }

    .empty-card i {
        font-size: 36px;
        color: var(--light-text);
        margin-bottom: 10px;
    }
</style>

<div class="qr-page">
    <div class="qr-toolbar">
        <div>
            <h3>QR Scan Logs</h3>
            <p class="text-muted mb-0">Track who scanned what, when, and where.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.qr-scan-logs.index') }}" class="qr-filters mb-3">
        <select name="scan_type">
            <option value="">All Types</option>
            <option value="pet" @if(request('scan_type') === 'pet') selected @endif>Pet</option>
            <option value="cage" @if(request('scan_type') === 'cage') selected @endif>Cage</option>
        </select>

        <select name="scanned_by">
            <option value="">All Users</option>
            @foreach($users as $user)
                @php
                    $displayName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    $displayText = $displayName !== '' ? $displayName : ($user->username ?? ('User #' . $user->id));
                @endphp
                <option value="{{ $user->id }}" @if((string) request('scanned_by') === (string) $user->id) selected @endif>
                    {{ $displayText }}
                </option>
            @endforeach
        </select>

        <input type="text" name="pet_name" value="{{ request('pet_name') }}" placeholder="Pet name">
        <input type="text" name="cage_code" value="{{ request('cage_code') }}" placeholder="Cage code/name">
        <input type="date" name="from_date" value="{{ request('from_date') }}" title="From date">
        <input type="date" name="to_date" value="{{ request('to_date') }}" title="To date">

        <button type="submit" class="btn-filter">Filter</button>
        <a href="{{ route('admin.qr-scan-logs.index') }}" class="btn-clear">Clear</a>
    </form>

    @if($logs->isEmpty())
        <div class="empty-card">
            <i class="fas fa-qrcode"></i>
            <h4>No scan logs found</h4>
            <p class="text-muted">No records match the current filter settings.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="qr-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Type</th>
                        <th>Pet</th>
                        <th>Cage</th>
                        <th>Scanned By</th>
                        <th>Location</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $scanner = $log->scannedBy;
                            $scannerName = trim(($scanner->first_name ?? '') . ' ' . ($scanner->last_name ?? ''));
                            $cageLabel = optional($log->cage)->cage_code ?? optional($log->cage)->name ?? 'N/A';
                        @endphp
                        <tr>
                            <td>
                                <div>{{ optional($log->scan_timestamp)->format('M d, Y') }}</div>
                                <div class="muted-sm">{{ optional($log->scan_timestamp)->format('h:i A') }}</div>
                            </td>
                            <td>
                                <span class="scan-type {{ $log->scan_type }}">
                                    <i class="fas {{ $log->scan_type === 'pet' ? 'fa-paw' : 'fa-border-all' }}"></i>
                                    {{ $log->scan_type }}
                                </span>
                            </td>
                            <td>
                                <div><strong>{{ optional($log->pet)->name ?? 'N/A' }}</strong></div>
                                <div class="muted-sm">{{ optional($log->pet)->registration_number ?? '' }}</div>
                            </td>
                            <td>{{ $cageLabel }}</td>
                            <td>
                                <div>{{ $scannerName !== '' ? $scannerName : (optional($scanner)->username ?? 'Unknown') }}</div>
                                <div class="muted-sm">ID: {{ $log->scanned_by ?? 'N/A' }}</div>
                            </td>
                            <td>{{ $log->location ?: 'N/A' }}</td>
                            <td>{{ $log->notes ?: 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--light-text);">
                                No scan logs found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
