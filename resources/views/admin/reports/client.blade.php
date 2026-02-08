@extends('admin.dashboard')

@section('page-title', 'Customer Report')
@section('page-description', 'Client activity and engagement')

@section('content')
<style>
    .report-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .report-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.9rem 1rem;
        background: #f8fafc;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 0.75rem;
    }
</style>

<div class="content-header report-hero">
    <div class="header-title">
        <h1><i class="fas fa-users"></i> Customer Report</h1>
        <p>Measure client growth and utilization</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
</div>

<div class="report-card">
    <form class="row g-3 align-items-end" method="GET">
        <div class="col-md-4">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">View</label>
            <select name="report_type" class="form-select">
                <option value="summary" {{ $reportType === 'summary' ? 'selected' : '' }}>Summary</option>
                <option value="detailed" {{ $reportType === 'detailed' ? 'selected' : '' }}>Detailed</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100"><i class="fas fa-filter"></i> Apply</button>
        </div>
    </form>
</div>

<div class="report-card">
    <div class="stat-grid">
        <div class="stat-card">
            <div class="text-muted small">Total Clients</div>
            <div class="h4 mb-0">{{ $totalClients }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">New Clients</div>
            <div class="h4 mb-0">{{ $newClients }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Active Clients</div>
            <div class="h4 mb-0">{{ $activeClients }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="report-card">
            <h5 class="mb-3">Top Clients by Revenue</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Email</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topClients as $client)
                            <tr>
                                <td>{{ trim($client->first_name . ' ' . $client->last_name) }}</td>
                                <td>{{ $client->email }}</td>
                                <td class="text-end">PHP {{ number_format($client->total_revenue ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No client revenue data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="report-card">
            <h5 class="mb-3">Client Acquisition</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-end">New Clients</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientAcquisition as $row)
                            <tr>
                                <td>{{ $row->month }}</td>
                                <td class="text-end">{{ $row->count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No acquisition data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="report-card">
            <h5 class="mb-3">Pets per Client</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Pets</th>
                            <th class="text-end">Clients</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($petsPerClient as $row)
                            <tr>
                                <td>{{ $row->pet_count }}</td>
                                <td class="text-end">{{ $row->client_count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No distribution data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
