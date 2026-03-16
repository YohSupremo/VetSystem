@extends('admin.dashboard')

@section('page-title', 'Pet Reports')
@section('page-description', 'View detailed reports and analytics for individual pets')

@section('content')
<style>
    .pet-reports-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .pet-selection-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
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
        margin-bottom: 1.5rem;
    }

    .table-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
    }

    .chart-wrapper {
        position: relative;
        height: 320px;
    }

    .pet-info-display {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #4f46e5;
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

    .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    .table thead th {
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
    }

    .badge-status {
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.02em;
    }
</style>

<div class="content-header pet-reports-hero">
    <div class="header-title">
        <h1><i class="fas fa-paw"></i> Pet Reports</h1>
        <p>Analyze medical history and service activity for individual pets</p>
    </div>
</div>

<div class="pet-selection-card">
    <div class="row align-items-center">
        <div class="col-md-6">
            <label for="pet-select" class="form-label fw-bold">
                <i class="fas fa-search"></i> Select Pet
            </label>
            <select class="form-select" id="pet-select" onchange="loadPetReports(this.value)">
                <option value="">Choose a pet to view reports...</option>
                @if(isset($pets))
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" 
                                @if(request('pet_id') == $pet->id) selected @endif>
                            {{ $pet->name }} ({{ $pet->species }}) - {{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-primary" onclick="refreshPetData()" id="refresh-btn" disabled>
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="exportPetReport()" id="export-btn" disabled>
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pet Reports Content (Initially Hidden) -->
<div id="pet-reports-content" style="display: none;">
    <!-- Pet Information Display -->
    <div id="pet-info-display" class="pet-info-display">
        <!-- Pet info will be loaded here -->
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" id="stats-cards">
        <!-- Stats cards will be loaded here -->
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Monthly Vet Visits</h5>
                        <small class="text-muted">Visit trends over time</small>
                    </div>
                </div>
                <div class="p-4">
                    <div class="chart-wrapper">
                        <div id="visits-chart-container"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Treatment Distribution</h5>
                        <small class="text-muted">Types of treatments received</small>
                    </div>
                </div>
                <div class="p-4">
                    <div class="chart-wrapper">
                        <div id="treatments-chart-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Surgery Types</h5>
                        <small class="text-muted">Surgical procedures performed</small>
                    </div>
                </div>
                <div class="p-4">
                    <div class="chart-wrapper">
                        <div id="surgeries-chart-container"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Incident Types</h5>
                        <small class="text-muted">Types of incidents reported</small>
                    </div>
                </div>
                <div class="p-4">
                    <div class="chart-wrapper">
                        <div id="incidents-chart-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="row">
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Recent Medical Records</h5>
                        <small class="text-muted">Latest medical history</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Diagnosis</th>
                                <th>Vet</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="medical-records-tbody">
                            <!-- Medical records will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Medication History</h5>
                        <small class="text-muted">Prescription records</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="prescriptions-tbody">
                            <!-- Prescriptions will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Appointment History</h5>
                        <small class="text-muted">All appointments</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Vet</th>
                            </tr>
                        </thead>
                        <tbody id="appointments-tbody">
                            <!-- Appointments will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Vaccination Records</h5>
                        <small class="text-muted">Immunization history</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Vaccine</th>
                                <th>Next Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="vaccinations-tbody">
                            <!-- Vaccinations will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Surgical History</h5>
                        <small class="text-muted">Surgical procedures performed</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Procedure</th>
                                <th>Surgeon</th>
                                <th>Status</th>
                                <th>Outcome</th>
                            </tr>
                        </thead>
                        <tbody id="surgeries-tbody">
                            <!-- Surgeries will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Incident Reports</h5>
                        <small class="text-muted">Safety incidents and accidents</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Reported By</th>
                            </tr>
                        </thead>
                        <tbody id="incidents-tbody">
                            <!-- Incidents will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Chronic Conditions</h5>
                        <small class="text-muted">Long-term health conditions</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Diagnosed</th>
                                <th>Condition</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Management</th>
                            </tr>
                        </thead>
                        <tbody id="chronic-conditions-tbody">
                            <!-- Chronic conditions will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Allergies & Sensitivities</h5>
                        <small class="text-muted">Known allergies and reactions</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Diagnosed</th>
                                <th>Allergen</th>
                                <th>Severity</th>
                                <th>Reactions</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="allergies-tbody">
                            <!-- Allergies will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Hospitalization History</h5>
                        <small class="text-muted">Cage assignments and boarding</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Cage</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="cage-assignments-tbody">
                            <!-- Cage assignments will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Grooming Services</h5>
                        <small class="text-muted">Grooming appointments and services</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Groomer</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="grooming-appointments-tbody">
                            <!-- Grooming appointments will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="mb-0">Laboratory Test Results</h5>
                        <small class="text-muted">Diagnostic test history and results</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Requested</th>
                                <th>Test Name</th>
                                <th>Status</th>
                                <th>Result Date</th>
                                <th>Results</th>
                            </tr>
                        </thead>
                        <tbody id="lab-tests-tbody">
                            <!-- Lab tests will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<div id="empty-state" class="empty-state">
    <i class="fas fa-paw"></i>
    <h3>No Pet Selected</h3>
    <p class="text-muted">Please select a pet from the dropdown above to view detailed reports and analytics.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentPetId = null;
let visitsChart = null;
let treatmentsChart = null;
let surgeriesChart = null;
let incidentsChart = null;

// Load pet reports when page loads if pet_id is in URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const petId = urlParams.get('pet_id');
    if (petId) {
        loadPetReports(petId);
    }
});

function loadPetReports(petId) {
    if (!petId) {
        document.getElementById('pet-reports-content').style.display = 'none';
        document.getElementById('empty-state').style.display = 'block';
        document.getElementById('refresh-btn').disabled = true;
        document.getElementById('export-btn').disabled = true;
        return;
    }

    currentPetId = petId;
    
    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('pet_id', petId);
    window.history.pushState({}, '', url);

    // Show loading state
    document.getElementById('pet-reports-content').style.display = 'block';
    document.getElementById('empty-state').style.display = 'none';
    document.getElementById('refresh-btn').disabled = false;
    document.getElementById('export-btn').disabled = false;

    // Load pet data via AJAX
    fetch(`/admin/pet-reports/data/${petId}`)
        .then(response => response.json())
        .then(data => {
            displayPetInfo(data.pet);
            displayStatsCards(data.stats);
            displayCharts(data.charts);
            displayTables(data.tables);
        })
        .catch(error => {
            console.error('Error loading pet reports:', error);
            alert('Error loading pet reports. Please try again.');
        });
}

function displayPetInfo(pet) {
    const petInfoHtml = `
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><i class="fas fa-paw"></i> ${pet.name}</h4>
                <p class="mb-0"><strong>Species:</strong> ${pet.species} | <strong>Breed:</strong> ${pet.breed || 'N/A'} | <strong>Age:</strong> ${pet.age || 'N/A'}</p>
                <p class="mb-0"><strong>Owner:</strong> ${pet.owner_name} | <strong>Contact:</strong> ${pet.owner_email}</p>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-success badge-status">Active Patient</span>
            </div>
        </div>
    `;
    document.getElementById('pet-info-display').innerHTML = petInfoHtml;
}

function displayStatsCards(stats) {
    const statsHtml = `
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_visits}</h3>
                        <p>Total Visits</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_prescriptions}</h3>
                        <p>Prescriptions</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-pills"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ecfdf3 0%, #dcfce7 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_vaccinations}</h3>
                        <p>Vaccinations</p>
                    </div>
                    <div class="stat-icon" style="color:#15803d;background:rgba(34,197,94,0.12);"><i class="fas fa-syringe"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_medical_records}</h3>
                        <p>Medical Records</p>
                    </div>
                    <div class="stat-icon" style="color:#c2410c;background:rgba(234,88,12,0.12);"><i class="fas fa-file-medical"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_surgeries}</h3>
                        <p>Surgeries</p>
                    </div>
                    <div class="stat-icon" style="color:#92400e;background:rgba(245,158,11,0.12);"><i class="fas fa-user-md"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_incidents}</h3>
                        <p>Incidents</p>
                    </div>
                    <div class="stat-icon" style="color:#dc2626;background:rgba(220,38,38,0.12);"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_chronic_conditions}</h3>
                        <p>Chronic Conditions</p>
                    </div>
                    <div class="stat-icon" style="color:#7c3aed;background:rgba(124,58,237,0.12);"><i class="fas fa-heartbeat"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_allergies}</h3>
                        <p>Allergies</p>
                    </div>
                    <div class="stat-icon" style="color:#0891b2;background:rgba(8,145,178,0.12);"><i class="fas fa-allergies"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_cage_assignments}</h3>
                        <p>Cage Assignments</p>
                    </div>
                    <div class="stat-icon" style="color:#166534;background:rgba(34,197,94,0.12);"><i class="fas fa-home"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_grooming_appointments}</h3>
                        <p>Grooming</p>
                    </div>
                    <div class="stat-icon" style="color:#a855f7;background:rgba(168,85,247,0.12);"><i class="fas fa-cut"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>${stats.total_lab_tests}</h3>
                        <p>Lab Tests</p>
                    </div>
                    <div class="stat-icon" style="color:#0369a1;background:rgba(3,105,161,0.12);"><i class="fas fa-flask"></i></div>
                </div>
            </div>
        </div>
    `;
    document.getElementById('stats-cards').innerHTML = statsHtml;
}

function displayCharts(charts) {
    // Destroy existing charts
    if (visitsChart) visitsChart.destroy();
    if (treatmentsChart) treatmentsChart.destroy();
    if (surgeriesChart) surgeriesChart.destroy();
    if (incidentsChart) incidentsChart.destroy();

    // Visits Chart
    const visitsCtx = document.createElement('canvas');
    document.getElementById('visits-chart-container').innerHTML = '';
    document.getElementById('visits-chart-container').appendChild(visitsCtx);
    
    visitsChart = new Chart(visitsCtx, {
        type: 'line',
        data: {
            labels: charts.visits.labels,
            datasets: [{
                label: 'Monthly Visits',
                data: charts.visits.data,
                borderColor: 'rgba(79, 70, 229, 1)',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Treatments Chart
    const treatmentsCtx = document.createElement('canvas');
    document.getElementById('treatments-chart-container').innerHTML = '';
    document.getElementById('treatments-chart-container').appendChild(treatmentsCtx);
    
    treatmentsChart = new Chart(treatmentsCtx, {
        type: 'doughnut',
        data: {
            labels: charts.treatments.labels,
            datasets: [{
                data: charts.treatments.data,
                backgroundColor: [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(249, 115, 22, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(59, 130, 246, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Surgeries Chart (if we have surgery data)
    if (charts.surgeries && charts.surgeries.labels && charts.surgeries.labels.length > 0) {
        const surgeriesCtx = document.createElement('canvas');
        document.getElementById('surgeries-chart-container').innerHTML = '';
        document.getElementById('surgeries-chart-container').appendChild(surgeriesCtx);
        
        surgeriesChart = new Chart(surgeriesCtx, {
            type: 'bar',
            data: {
                labels: charts.surgeries.labels,
                datasets: [{
                    label: 'Surgeries by Type',
                    data: charts.surgeries.data,
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Incidents Chart (if we have incident data)
    if (charts.incidents && charts.incidents.labels && charts.incidents.labels.length > 0) {
        const incidentsCtx = document.createElement('canvas');
        document.getElementById('incidents-chart-container').innerHTML = '';
        document.getElementById('incidents-chart-container').appendChild(incidentsCtx);
        
        incidentsChart = new Chart(incidentsCtx, {
            type: 'pie',
            data: {
                labels: charts.incidents.labels,
                datasets: [{
                    data: charts.incidents.data,
                    backgroundColor: [
                        'rgba(220, 38, 38, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

function displayTables(tables) {
    // Medical Records Table
    const medicalRecordsHtml = tables.medical_records.map(record => `
        <tr>
            <td>${new Date(record.visit_date).toLocaleDateString()}</td>
            <td>${record.diagnosis ? record.diagnosis.substring(0, 50) + '...' : 'N/A'}</td>
            <td>${record.vet_name || 'N/A'}</td>
            <td><span class="badge bg-success badge-status">Completed</span></td>
        </tr>
    `).join('');
    document.getElementById('medical-records-tbody').innerHTML = medicalRecordsHtml || '<tr><td colspan="4" class="text-center">No medical records found</td></tr>';

    // Prescriptions Table
    const prescriptionsHtml = tables.prescriptions.map(prescription => `
        <tr>
            <td>${new Date(prescription.created_at).toLocaleDateString()}</td>
            <td>${prescription.medication_name || 'N/A'}</td>
            <td>${prescription.dosage || 'N/A'}</td>
            <td><span class="badge bg-${prescription.status === 'active' ? 'success' : 'secondary'} badge-status">${prescription.status || 'N/A'}</span></td>
        </tr>
    `).join('');
    document.getElementById('prescriptions-tbody').innerHTML = prescriptionsHtml || '<tr><td colspan="4" class="text-center">No prescriptions found</td></tr>';

    // Appointments Table
    const appointmentsHtml = tables.appointments.map(appointment => `
        <tr>
            <td>${new Date(appointment.appointment_date).toLocaleDateString()}</td>
            <td>${appointment.type || 'N/A'}</td>
            <td><span class="badge bg-${appointment.status === 'completed' ? 'success' : appointment.status === 'cancelled' ? 'danger' : 'warning'} badge-status">${appointment.status || 'N/A'}</span></td>
            <td>${appointment.vet_name || 'N/A'}</td>
        </tr>
    `).join('');
    document.getElementById('appointments-tbody').innerHTML = appointmentsHtml || '<tr><td colspan="4" class="text-center">No appointments found</td></tr>';

    // Vaccinations Table
    const vaccinationsHtml = tables.vaccinations.map(vaccination => `
        <tr>
            <td>${new Date(vaccination.administered_date).toLocaleDateString()}</td>
            <td>${vaccination.vaccine_name || 'N/A'}</td>
            <td>${vaccination.next_due_date ? new Date(vaccination.next_due_date).toLocaleDateString() : 'N/A'}</td>
            <td><span class="badge bg-success badge-status">Administered</span></td>
        </tr>
    `).join('');
    document.getElementById('vaccinations-tbody').innerHTML = vaccinationsHtml || '<tr><td colspan="4" class="text-center">No vaccinations found</td></tr>';

    // Surgeries Table
    const surgeriesHtml = tables.surgeries.map(surgery => `
        <tr>
            <td>${new Date(surgery.scheduled_date).toLocaleDateString()}</td>
            <td>${surgery.surgery_type || 'N/A'}</td>
            <td>${surgery.surgeon_name || 'N/A'}</td>
            <td><span class="badge bg-${surgery.status === 'completed' ? 'success' : surgery.status === 'cancelled' ? 'danger' : 'warning'} badge-status">${surgery.status || 'N/A'}</span></td>
            <td>${surgery.outcome || 'N/A'}</td>
        </tr>
    `).join('');
    document.getElementById('surgeries-tbody').innerHTML = surgeriesHtml || '<tr><td colspan="5" class="text-center">No surgeries found</td></tr>';

    // Incidents Table
    const incidentsHtml = tables.incidents.map(incident => `
        <tr>
            <td>${new Date(incident.incident_date).toLocaleDateString()}</td>
            <td>${incident.incident_type || 'N/A'}</td>
            <td><span class="badge bg-${incident.severity === 'high' ? 'danger' : incident.severity === 'medium' ? 'warning' : 'info'} badge-status">${incident.severity || 'N/A'}</span></td>
            <td><span class="badge bg-${incident.status === 'resolved' ? 'success' : 'warning'} badge-status">${incident.status || 'N/A'}</span></td>
            <td>${incident.reported_by || 'N/A'}</td>
        </tr>
    `).join('');
    document.getElementById('incidents-tbody').innerHTML = incidentsHtml || '<tr><td colspan="5" class="text-center">No incidents found</td></tr>';

    // Chronic Conditions Table
    const chronicConditionsHtml = tables.chronic_conditions.map(condition => `
        <tr>
            <td>${new Date(condition.created_at).toLocaleDateString()}</td>
            <td>${condition.condition_name || 'N/A'}</td>
            <td><span class="badge bg-${condition.severity === 'severe' ? 'danger' : condition.severity === 'moderate' ? 'warning' : 'info'} badge-status">${condition.severity || 'N/A'}</span></td>
            <td><span class="badge bg-${condition.status === 'active' ? 'warning' : 'success'} badge-status">${condition.status || 'N/A'}</span></td>
            <td>${condition.management_plan ? condition.management_plan.substring(0, 30) + '...' : 'N/A'}</td>
        </tr>
    `).join('');
    document.getElementById('chronic-conditions-tbody').innerHTML = chronicConditionsHtml || '<tr><td colspan="5" class="text-center">No chronic conditions found</td></tr>';

    // Allergies Table
    const allergiesHtml = tables.allergies.map(allergy => `
        <tr>
            <td>${new Date(allergy.created_at).toLocaleDateString()}</td>
            <td>${allergy.allergen || 'N/A'}</td>
            <td><span class="badge bg-${allergy.severity === 'severe' ? 'danger' : allergy.severity === 'moderate' ? 'warning' : 'info'} badge-status">${allergy.severity || 'N/A'}</span></td>
            <td>${allergy.reaction_symptoms ? allergy.reaction_symptoms.substring(0, 30) + '...' : 'N/A'}</td>
            <td><span class="badge bg-${allergy.status === 'active' ? 'warning' : 'success'} badge-status">${allergy.status || 'N/A'}</span></td>
        </tr>
    `).join('');
    document.getElementById('allergies-tbody').innerHTML = allergiesHtml || '<tr><td colspan="5" class="text-center">No allergies found</td></tr>';

    // Cage Assignments Table
    const cageAssignmentsHtml = tables.cage_assignments.map(assignment => `
        <tr>
            <td>${new Date(assignment.start_date).toLocaleDateString()}</td>
            <td>${assignment.end_date ? new Date(assignment.end_date).toLocaleDateString() : 'Current'}</td>
            <td>${assignment.cage_name || 'N/A'}</td>
            <td>${assignment.reason || 'N/A'}</td>
            <td><span class="badge bg-${assignment.status === 'active' ? 'success' : 'secondary'} badge-status">${assignment.status || 'N/A'}</span></td>
        </tr>
    `).join('');
    document.getElementById('cage-assignments-tbody').innerHTML = cageAssignmentsHtml || '<tr><td colspan="5" class="text-center">No cage assignments found</td></tr>';

    // Grooming Appointments Table
    const groomingAppointmentsHtml = tables.grooming_appointments.map(appointment => `
        <tr>
            <td>${new Date(appointment.appointment_date).toLocaleDateString()}</td>
            <td>${appointment.service_name || 'N/A'}</td>
            <td>${appointment.groomer_name || 'N/A'}</td>
            <td><span class="badge bg-${appointment.status === 'completed' ? 'success' : appointment.status === 'cancelled' ? 'danger' : 'warning'} badge-status">${appointment.status || 'N/A'}</span></td>
            <td>${appointment.special_instructions ? appointment.special_instructions.substring(0, 30) + '...' : 'N/A'}</td>
        </tr>
    `).join('');
    document.getElementById('grooming-appointments-tbody').innerHTML = groomingAppointmentsHtml || '<tr><td colspan="5" class="text-center">No grooming appointments found</td></tr>';

    // Lab Tests Table
    const labTestsHtml = tables.lab_tests.map(test => `
        <tr>
            <td>${new Date(test.requested_date).toLocaleDateString()}</td>
            <td>${test.test_name || 'N/A'}</td>
            <td><span class="badge bg-${test.status === 'completed' ? 'success' : test.status === 'pending' ? 'warning' : 'info'} badge-status">${test.status || 'N/A'}</span></td>
            <td>${test.result_date ? new Date(test.result_date).toLocaleDateString() : 'N/A'}</td>
            <td>${test.results ? test.results.substring(0, 30) + '...' : 'N/A'}</td>
        </tr>
    `).join('');
    document.getElementById('lab-tests-tbody').innerHTML = labTestsHtml || '<tr><td colspan="5" class="text-center">No lab tests found</td></tr>';
}

function refreshPetData() {
    if (currentPetId) {
        loadPetReports(currentPetId);
    }
}

function exportPetReport() {
    if (currentPetId) {
        window.open(`/admin/pet-reports/export/${currentPetId}`, '_blank');
    }
}
</script>
@endsection
