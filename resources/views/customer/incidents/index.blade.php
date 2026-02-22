@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Incident Reports - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
}

.incident-table {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.incident-table::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.incident-table table {
    background: transparent;
    margin: 0;
}

.incident-table th {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
    padding: 1rem;
}

.incident-table td {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: #000;
    padding: 1rem;
    vertical-align: middle;
}

.incident-table tbody tr:hover td {
    background: rgba(255, 255, 255, 0.1);
}

.status-badge {
    padding: 0.4rem 0.875rem;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
}

.status-open {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgba(239, 68, 68, 0.9);
}

.status-investigating {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.3);
    color: rgba(245, 158, 11, 0.9);
}

.status-resolved {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
    color: rgba(16, 185, 129, 0.9);
}

.status-closed {
    background: rgba(107, 114, 128, 0.2);
    border-color: rgba(107, 114, 128, 0.3);
    color: rgba(107, 114, 128, 0.9);
}

.btn-report-incident {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition-smooth);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-report-incident:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
    color: white;
    text-decoration: none;
}

.btn-view {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    color: #000;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--transition-smooth);
}

.btn-view:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
}

.empty-state::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 4px 10px rgba(147, 51, 234, 0.3));
}

.empty-title {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
}

.empty-description {
    color: #333;
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

.alert {
    background: rgba(16, 185, 129, 0.2);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-left: 4px solid rgba(16, 185, 129, 0.5);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #000;
    padding: 1.125rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    font-weight: 600;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
}

.alert-danger {
    background: rgba(239, 68, 68, 0.2);
    border-left-color: rgba(239, 68, 68, 0.5);
    border-color: rgba(239, 68, 68, 0.3);
    color: #000;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@media (max-width: 768px) {
    .incident-table {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .btn-view {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .empty-state {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="customer-container">
    <main class="customer-main">
        <!-- Page Header -->
        <div class="page-header mb-5 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Incident Reports</h1>
                <p class="page-subtitle">Report accidents or urgent medical incidents for your pets.</p>
            </div>
            <a href="{{ route('customer.incidents.create') }}" class="btn-report-incident">
                <i class="fas fa-exclamation-triangle me-2"></i>Report Incident
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($incidents->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <h2 class="empty-title">No incident reports yet</h2>
                <p class="empty-description">Create an incident report if your pet needs urgent attention.</p>
                <a href="{{ route('customer.incidents.create') }}" class="btn-report-incident">
                    <i class="fas fa-plus me-2"></i>Report Incident
                </a>
            </div>
        @else
            <div class="incident-table">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Incident #</th>
                            <th>Date</th>
                            <th>Pet</th>
                            <th>Type</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidents as $incident)
                            <tr>
                                <td>{{ $incident->incident_number }}</td>
                                <td>{{ optional($incident->incident_date)->format('M d, Y h:i A') }}</td>
                                <td>{{ $incident->pet->name ?? 'N/A' }}</td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $incident->incident_type) }}</td>
                                <td class="text-capitalize">{{ $incident->severity }}</td>
                                <td>
                                    @if($incident->status == 'open')
                                        <span class="status-badge status-open">{{ $incident->status }}</span>
                                    @elseif($incident->status == 'investigating')
                                        <span class="status-badge status-investigating">{{ $incident->status }}</span>
                                    @elseif($incident->status == 'resolved')
                                        <span class="status-badge status-resolved">{{ $incident->status }}</span>
                                    @elseif($incident->status == 'closed')
                                        <span class="status-badge status-closed">{{ $incident->status }}</span>
                                    @else
                                        <span class="status-badge">{{ $incident->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('customer.incidents.show', $incident->id) }}" class="btn-view">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </main>
</div>
@endsection
