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
        <p>View clinic analytics based on live data</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.financial') }}" class="btn btn-outline-primary">
            <i class="fas fa-coins"></i> Financial
        </a>
        <a href="{{ route('admin.reports.cancelled-invoices') }}" class="btn btn-outline-danger">
            <i class="fas fa-ban"></i> Cancelled
        </a>
        <a href="{{ route('admin.reports.medical') }}" class="btn btn-outline-primary">
            <i class="fas fa-heartbeat"></i> Medical
        </a>
        <a href="{{ route('admin.reports.inventory') }}" class="btn btn-outline-primary">
            <i class="fas fa-warehouse"></i> Inventory
        </a>
        <a href="{{ route('admin.reports.client') }}" class="btn btn-outline-primary">
            <i class="fas fa-users"></i> Customer
        </a>
        <a href="{{ route('admin.reports.appointment') }}" class="btn btn-outline-primary">
            <i class="fas fa-calendar-check"></i> Operational
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $totalInvoices }}</h3>
                    <p>Total Invoices</p>
                </div>
                <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $totalAppointments }}</h3>
                    <p>Appointments</p>
                </div>
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #ecfdf3 0%, #dcfce7 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $totalMedicalRecords }}</h3>
                    <p>Medical Records</p>
                </div>
                <div class="stat-icon" style="color:#15803d;background:rgba(34,197,94,0.12);"><i class="fas fa-heartbeat"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $totalInventoryItems }}</h3>
                    <p>Inventory Items</p>
                </div>
                <div class="stat-icon" style="color:#c2410c;background:rgba(234,88,12,0.12);"><i class="fas fa-warehouse"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div>
            <h5 class="mb-0">Available Reports</h5>
            <small class="text-muted">Reports are generated on demand from live data.</small>
        </div>
    </div>
    <div class="p-4">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.financial') }}" class="btn btn-outline-primary">
                <i class="fas fa-coins"></i> Financial Report
            </a>
            <a href="{{ route('admin.reports.medical') }}" class="btn btn-outline-primary">
                <i class="fas fa-heartbeat"></i> Medical Report
            </a>
            <a href="{{ route('admin.reports.inventory') }}" class="btn btn-outline-primary">
                <i class="fas fa-warehouse"></i> Inventory Report
            </a>
            <a href="{{ route('admin.reports.client') }}" class="btn btn-outline-primary">
                <i class="fas fa-users"></i> Customer Report
            </a>
            <a href="{{ route('admin.reports.appointment') }}" class="btn btn-outline-primary">
                <i class="fas fa-calendar-check"></i> Operational Report
            </a>
        </div>
    </div>
</div>
@endsection
