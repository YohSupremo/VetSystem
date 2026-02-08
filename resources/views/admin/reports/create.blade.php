@extends('admin.dashboard')

@section('page-title', 'Generate Report')
@section('page-description', 'Create a new report configuration')

@section('content')
<style>
    .reports-hero {
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
        padding: 1.5rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.65rem 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15);
    }
</style>

<div class="content-header reports-hero">
    <div class="header-title">
        <h1><i class="fas fa-chart-line"></i> Generate Report</h1>
        <p>Create a report configuration to reuse analytics ranges</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
</div>

<form action="{{ route('admin.reports.store') }}" method="POST">
    @csrf
    <div class="report-card">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="title" class="form-label">Report Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Monthly Revenue Summary" required>
                @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="report_type" class="form-label">Report Type</label>
                <select id="report_type" name="report_type" class="form-select" required>
                    <option value="">Select type</option>
                    @foreach($reportTypes as $type)
                        <option value="{{ $type }}" {{ old('report_type') == $type ? 'selected' : '' }}>
                            {{ $reportTypeLabels[$type] ?? ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                @error('report_type')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', now()->subMonth()->toDateString()) }}" required>
                @error('start_date')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', now()->toDateString()) }}" required>
                @error('end_date')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Report</button>
    </div>
</form>
@endsection
