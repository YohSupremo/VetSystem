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
    </div>
</div>

<!-- Empty State (Initially Shown) -->
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
    `;
    document.getElementById('stats-cards').innerHTML = statsHtml;
}

function displayCharts(charts) {
    // Destroy existing charts
    if (visitsChart) visitsChart.destroy();
    if (treatmentsChart) treatmentsChart.destroy();

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
