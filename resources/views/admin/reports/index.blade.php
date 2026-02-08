@extends('admin.dashboard')

@section('page-title', 'Reports & Analytics')
@section('page-description', 'View and generate clinic reports and analytics')

@section('content')
<style>
    .reports-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .stats-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .stats-card h3 {
        font-size: 2rem;
        margin: 0;
        font-weight: 700;
        color: #0f172a;
    }

    .stats-card p {
        margin: 0.35rem 0 0;
        color: #64748b;
        font-weight: 500;
    }

    .stats-card .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1d4ed8;
        background: rgba(37, 99, 235, 0.1);
        font-size: 1.1rem;
    }

    .table-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .table-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
    }

    .table thead th {
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
        text-align: center;
    }

    .table tbody td {
        text-align: center;
        vertical-align: middle;
    }

    .table colgroup col.title-col { width: 28%; }
    .table colgroup col.type-col { width: 14%; }
    .table colgroup col.range-col { width: 20%; }
    .table colgroup col.user-col { width: 18%; }
    .table colgroup col.status-col { width: 10%; }
    .table colgroup col.actions-col { width: 10%; }

    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.02em;
    }

    .action-group {
        display: flex;
        gap: 0.35rem;
        justify-content: center;
        flex-wrap: nowrap;
    }

    .action-group .btn {
        min-width: 36px;
        border-radius: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5f5;
        margin-bottom: 20px;
        display: block;
    }
</style>

<div class="content-header reports-hero">
    <div class="header-title">
        <h1><i class="fas fa-chart-bar"></i> Reports</h1>
        <p>View and generate clinic reports and analytics</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate Report
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $totalReports }}</h3>
                    <p>Total Reports</p>
                </div>
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $financialReports }}</h3>
                    <p>Financial</p>
                </div>
                <div class="stat-icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #ecfdf3 0%, #dcfce7 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $medicalReports }}</h3>
                    <p>Medical</p>
                </div>
                <div class="stat-icon" style="color:#15803d;background:rgba(34,197,94,0.12);"><i class="fas fa-heartbeat"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $inventoryReports }}</h3>
                    <p>Inventory</p>
                </div>
                <div class="stat-icon" style="color:#c2410c;background:rgba(234,88,12,0.12);"><i class="fas fa-warehouse"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div>
            <h5 class="mb-0">Generated Reports</h5>
            <small class="text-muted">Showing {{ $reports->count() }} of {{ $reports->total() }}</small>
        </div>
    </div>
    @if($reports->isEmpty())
        <div class="empty-state">
            <i class="fas fa-chart-pie"></i>
            <h3>No reports yet</h3>
            <p>Generate a report to track clinic performance.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <colgroup>
                    <col class="title-col">
                    <col class="type-col">
                    <col class="range-col">
                    <col class="user-col">
                    <col class="status-col">
                    <col class="actions-col">
                </colgroup>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Date Range</th>
                        <th>Generated By</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        @php
                            $statusClass = $report->status === 'ready' ? 'bg-success text-white' : 'bg-secondary text-white';
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $report->title }}</strong>
                                <div class="text-muted small">{{ $report->created_at?->format('M d, Y') }}</div>
                            </td>
                            <td>{{ $reportTypeLabels[$report->report_type] ?? ucfirst($report->report_type) }}</td>
                            <td>{{ $report->start_date?->format('M d, Y') }} - {{ $report->end_date?->format('M d, Y') }}</td>
                            <td>{{ $report->generatedBy?->first_name ?? 'System' }}</td>
                            <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($report->status) }}</span></td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.reports.edit', $report->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.reports.' . $report->report_type, ['start_date' => $report->start_date, 'end_date' => $report->end_date]) }}" class="btn btn-sm btn-outline-success" title="Open">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this report?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
