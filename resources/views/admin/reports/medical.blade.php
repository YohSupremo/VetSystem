@extends('admin.dashboard')

@section('page-title', 'Medical Report')
@section('page-description', 'Clinical activities and outcomes')

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
        <h1><i class="fas fa-heartbeat"></i> Medical Report</h1>
        <p>Track treatments, diagnoses, and care volume</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <a href="{{ route('admin.reports.export', ['reportType' => 'medical', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('admin.reports.export', ['reportType' => 'medical', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-primary">
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
            <div class="text-muted small">Appointments</div>
            <div class="h4 mb-0">{{ $totalAppointments }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Completed</div>
            <div class="h4 mb-0">{{ $completedAppointments }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Medical Records</div>
            <div class="h4 mb-0">{{ $totalMedicalRecords }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Prescriptions</div>
            <div class="h4 mb-0">{{ $totalPrescriptions }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Vaccinations</div>
            <div class="h4 mb-0">{{ $totalVaccinations }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Surgeries</div>
            <div class="h4 mb-0">{{ $totalSurgeries }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Medical Activity Overview</h5>
            <div class="chart-wrapper">
                {!! $medicalVolumeChart->container() !!}
            </div>
            <h5 class="mt-4 mb-3">Appointment Status Breakdown</h5>
            <div class="chart-wrapper">
                {!! $appointmentStatusChart->container() !!}
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Pet Types Distribution</h5>
            <div class="chart-wrapper">
                {!! $petTypesChart->container() !!}
            </div>
        </div>
    </div>
</div>

<div class="report-card">
    <h5 class="mb-3">Top Diagnoses</h5>
    <div class="chart-wrapper">
        {!! $commonDiagnosesChart->container() !!}
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Common Diagnoses</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Diagnosis</th>
                            <th class="text-end">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commonDiagnoses as $row)
                            <tr>
                                <td>{{ $row->diagnosis }}</td>
                                <td class="text-end">{{ $row->count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No diagnoses logged.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Common Treatments</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Treatment Plan</th>
                            <th class="text-end">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commonTreatments as $row)
                            <tr>
                                <td>{{ $row->treatment_plan }}</td>
                                <td class="text-end">{{ $row->count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No treatments logged.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="report-card">
    <h5 class="mb-3">Pet Types Treated</h5>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Species</th>
                    <th class="text-end">Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($petTypes as $row)
                    <tr>
                        <td>{{ $row->species ?? 'Unknown' }}</td>
                        <td class="text-end">{{ $row->count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted">No data available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
{!! $medicalVolumeChart->script() !!}
{!! $appointmentStatusChart->script() !!}
{!! $commonDiagnosesChart->script() !!}
{!! $petTypesChart->script() !!}
@endsection
