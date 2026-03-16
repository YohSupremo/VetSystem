@extends('admin.dashboard')

@section('page-title', 'Operational Report')
@section('page-description', 'Appointments and schedule performance')

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

    .chart-wrapper {
        position: relative;
        height: 320px;
    }
</style>

<div class="content-header report-hero">
    <div class="header-title">
        <h1><i class="fas fa-calendar-check"></i> Operational Report</h1>
        <p>Analyze appointment volume and trends</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <a href="{{ route('admin.reports.export', ['reportType' => 'appointment', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('admin.reports.export', ['reportType' => 'appointment', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-primary">
            <i class="fas fa-file-export"></i> Export CSV
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
            <div class="text-muted small">Total Appointments</div>
            <div class="h4 mb-0">{{ $totalAppointments }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Completed</div>
            <div class="h4 mb-0">{{ $completedAppointments }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Cancelled</div>
            <div class="h4 mb-0">{{ $cancelledAppointments }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">No Shows</div>
            <div class="h4 mb-0">{{ $noShowAppointments }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Appointment Status Distribution</h5>
            <div class="chart-wrapper">
                {!! $appointmentStatusChart->container() !!}
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Appointments by Type</h5>
            <div class="chart-wrapper">
                {!! $appointmentsByTypeChart->container() !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Daily Appointment Trend</h5>
            <div class="chart-wrapper">
                {!! $dailyTrendsChart->container() !!}
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Peak Hours Chart</h5>
            <div class="chart-wrapper">
                {!! $peakHoursChart->container() !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Appointments by Type</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="text-end">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointmentsByType as $row)
                            <tr>
                                <td class="text-capitalize">{{ $row->type }}</td>
                                <td class="text-end">{{ $row->count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No appointment data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Peak Hours</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Hour</th>
                            <th class="text-end">Appointments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peakHours as $row)
                            <tr>
                                <td>{{ $row->hour }}:00</td>
                                <td class="text-end">{{ $row->count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No peak hour data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="report-card">
    <h5 class="mb-3">Daily Trends</h5>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-end">Appointments</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailyTrends as $row)
                    <tr>
                        <td>{{ $row->date }}</td>
                        <td class="text-end">{{ $row->count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted">No trend data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
{!! $appointmentStatusChart->script() !!}
{!! $appointmentsByTypeChart->script() !!}
{!! $dailyTrendsChart->script() !!}
{!! $peakHoursChart->script() !!}
@endsection
