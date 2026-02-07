@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', $pet->name . ' - Medical Records')

@push('styles')
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.customer-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
}

.customer-main {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.pet-header {
    background: linear-gradient(135deg, rgba(167, 139, 250, 0.15), rgba(236, 72, 153, 0.15));
    border-radius: 2rem;
    padding: 2rem;
    margin-bottom: 2rem;
    display: flex;
    gap: 2rem;
    align-items: center;
}

.pet-photo-large {
    width: 150px;
    height: 150px;
    border-radius: 1.5rem;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.2);
}

.pet-header-info h1 {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
}

.pet-stats {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.stat-badge {
    padding: 0.5rem 1rem;
    background: white;
    border-radius: 1rem;
    font-weight: 600;
    box-shadow: 0 2px 10px rgba(147, 51, 234, 0.1);
}

.tabs-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 2rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.nav-tabs {
    border-bottom: 2px solid rgba(167, 139, 250, 0.2);
    margin-bottom: 1.5rem;
}

.nav-tabs .nav-link {
    border: none;
    background: transparent;
    color: #6B7280;
    font-weight: 600;
    padding: 1rem 1.5rem;
    margin-right: 0.5rem;
    border-radius: 1rem 1rem 0 0;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    background: rgba(167, 139, 250, 0.1);
    color: var(--primary-purple);
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
}

.record-card {
    background: white;
    border-radius: 1.5rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 2px 10px rgba(147, 51, 234, 0.08);
    transition: all 0.3s ease;
}

.record-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.15);
}

.record-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(167, 139, 250, 0.1);
}

.record-date {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-purple);
}

.record-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 0.85rem;
}

.badge-completed {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.badge-scheduled {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
}

.badge-pending {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.badge-cancelled {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.record-content {
    display: grid;
    gap: 1rem;
}

.info-row {
    display: flex;
    gap: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: var(--primary-purple);
    min-width: 150px;
}

.info-value {
    color: #374151;
}

.section-box {
    background: rgba(167, 139, 250, 0.05);
    border-radius: 1rem;
    padding: 1rem;
    margin-top: 0.5rem;
}

.empty-message {
    text-align: center;
    padding: 3rem;
    color: #9CA3AF;
}

.btn-back {
    background: rgba(167, 139, 250, 0.1);
    border: none;
    border-radius: 1rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    color: var(--primary-purple);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: rgba(167, 139, 250, 0.2);
    color: var(--primary-purple);
}
</style>
@endpush

@section('content')
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="customer-container">
    <!-- Header -->
    <header class="customer-header">
        <div class="header-content d-flex justify-content-between align-items-center">
            <div class="logo-section d-flex align-items-center gap-3">
                <div class="paw-icon">🐾</div>
                <h1 class="mb-0">PawCare</h1>
            </div>
            <div class="user-menu d-flex align-items-center gap-3">
                <div class="user-info d-flex align-items-center gap-2">
                    <span class="welcome-text">Welcome, {{ $user->first_name }}!</span>
                    <div class="user-avatar">
                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                    </div>
                </div>
                <a href="/logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="customer-main">
        <a href="{{ route('customer.medical-records.index') }}" class="btn-back">
            <span>←</span> Back to All Pets
        </a>

        <!-- Pet Header -->
        <div class="pet-header">
            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="pet-photo-large">
            <div class="pet-header-info">
                <h1>{{ $pet->name }}'s Medical Records</h1>
                <div class="pet-stats">
                    <span class="stat-badge">🐾 {{ ucfirst($pet->species) }}</span>
                    <span class="stat-badge">📅 {{ $pet->age }}</span>
                    <span class="stat-badge">🎂 Born: {{ $pet->birth_date->format('M d, Y') }}</span>
                    @if($pet->breed)
                        <span class="stat-badge">🏷️ {{ $pet->breed }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#appointments" type="button">
                        📅 Appointments ({{ $appointments->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical-records" type="button">
                        🏥 Medical Records ({{ $medicalRecords->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#vaccinations" type="button">
                        💉 Vaccinations ({{ $vaccinations->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button">
                        💊 Prescriptions ({{ $prescriptions->count() }})
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Appointments Tab -->
                <div class="tab-pane fade show active" id="appointments" role="tabpanel">
                    @if($appointments->count() > 0)
                        @foreach($appointments as $appointment)
                            <div class="record-card">
                                <div class="record-header">
                                    <div>
                                        <div class="record-date">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}
                                        </div>
                                        <div style="color: #6B7280; margin-top: 0.25rem;">
                                            {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}
                                        </div>
                                    </div>
                                    <span class="record-badge badge-{{ $appointment->status }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                                <div class="record-content">
                                    <div class="info-row">
                                        <span class="info-label">Type:</span>
                                        <span class="info-value">{{ ucfirst(str_replace('_', ' ', $appointment->type)) }}</span>
                                    </div>
                                    @if($appointment->veterinarian)
                                        <div class="info-row">
                                            <span class="info-label">Veterinarian:</span>
                                            <span class="info-value">Dr. {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}</span>
                                        </div>
                                    @endif
                                    @if($appointment->reason)
                                        <div class="info-row">
                                            <span class="info-label">Reason:</span>
                                            <span class="info-value">{{ $appointment->reason }}</span>
                                        </div>
                                    @endif
                                    @if($appointment->notes)
                                        <div class="section-box" style="background: {{ $appointment->status === 'cancelled' ? 'rgba(254, 202, 202, 0.3)' : 'rgba(167, 139, 250, 0.05)' }};">
                                            <strong>{{ $appointment->status === 'cancelled' ? '⚠️ Cancellation Reason:' : '📝 Notes:' }}</strong>
                                            <p style="margin-top: 0.5rem; white-space: pre-wrap; margin-bottom: 0;">{{ $appointment->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-message">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📅</div>
                            <p>No appointments recorded yet</p>
                        </div>
                    @endif
                </div>

                <!-- Medical Records Tab -->
                <div class="tab-pane fade" id="medical-records" role="tabpanel">
                    @if($medicalRecords->count() > 0)
                        @foreach($medicalRecords as $record)
                            <div class="record-card">
                                <div class="record-header">
                                    <div>
                                        <div class="record-date">
                                            {{ \Carbon\Carbon::parse($record->visit_date)->format('F d, Y') }}
                                        </div>
                                        @if($record->veterinarian)
                                            <div style="color: #6B7280; margin-top: 0.25rem;">
                                                Dr. {{ $record->veterinarian->first_name }} {{ $record->veterinarian->last_name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="record-content">
                                    @if($record->complaint)
                                        <div class="info-row">
                                            <span class="info-label">Chief Complaint:</span>
                                            <span class="info-value">{{ $record->complaint }}</span>
                                        </div>
                                    @endif
                                    @if($record->diagnosis)
                                        <div class="section-box">
                                            <strong style="color: var(--primary-purple);">🔍 Diagnosis:</strong>
                                            <p style="margin-top: 0.5rem; margin-bottom: 0;">{{ $record->diagnosis }}</p>
                                        </div>
                                    @endif
                                    @if($record->treatment_plan)
                                        <div class="section-box">
                                            <strong style="color: var(--primary-purple);">💊 Treatment Plan:</strong>
                                            <p style="margin-top: 0.5rem; margin-bottom: 0;">{{ $record->treatment_plan }}</p>
                                        </div>
                                    @endif
                                    @if($record->examination_notes)
                                        <div class="section-box">
                                            <strong style="color: var(--primary-purple);">📋 Examination Notes:</strong>
                                            <p style="margin-top: 0.5rem; margin-bottom: 0;">{{ $record->examination_notes }}</p>
                                        </div>
                                    @endif
                                    @if($record->vital_signs)
                                        <div class="section-box">
                                            <strong style="color: var(--primary-purple);">💓 Vital Signs:</strong>
                                            <div style="margin-top: 0.5rem;">
                                                @foreach($record->vital_signs as $key => $value)
                                                    <span style="display: inline-block; margin-right: 1rem;">
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if($record->follow_up_date)
                                        <div class="info-row">
                                            <span class="info-label">Follow-up Date:</span>
                                            <span class="info-value">{{ \Carbon\Carbon::parse($record->follow_up_date)->format('F d, Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-message">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🏥</div>
                            <p>No medical records yet</p>
                        </div>
                    @endif
                </div>

                <!-- Vaccinations Tab -->
                <div class="tab-pane fade" id="vaccinations" role="tabpanel">
                    @if($vaccinations->count() > 0)
                        @foreach($vaccinations as $vaccination)
                            <div class="record-card">
                                <div class="record-header">
                                    <div>
                                        <div class="record-date">{{ $vaccination->vaccine_name }}</div>
                                        <div style="color: #6B7280; margin-top: 0.25rem;">
                                            {{ \Carbon\Carbon::parse($vaccination->vaccination_date)->format('F d, Y') }}
                                        </div>
                                    </div>
                                    <span class="record-badge badge-completed">✓ Administered</span>
                                </div>
                                <div class="record-content">
                                    @if($vaccination->batch_number)
                                        <div class="info-row">
                                            <span class="info-label">Batch Number:</span>
                                            <span class="info-value">{{ $vaccination->batch_number }}</span>
                                        </div>
                                    @endif
                                    @if($vaccination->administeredBy)
                                        <div class="info-row">
                                            <span class="info-label">Administered By:</span>
                                            <span class="info-value">Dr. {{ $vaccination->administeredBy->first_name }} {{ $vaccination->administeredBy->last_name }}</span>
                                        </div>
                                    @endif
                                    @if($vaccination->next_due_date)
                                        <div class="info-row">
                                            <span class="info-label">Next Due Date:</span>
                                            <span class="info-value" style="color: {{ \Carbon\Carbon::parse($vaccination->next_due_date)->isPast() ? '#991b1b' : '#065f46' }};">
                                                {{ \Carbon\Carbon::parse($vaccination->next_due_date)->format('F d, Y') }}
                                                @if(\Carbon\Carbon::parse($vaccination->next_due_date)->isPast())
                                                    <strong>(Overdue)</strong>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    @if($vaccination->notes)
                                        <div class="section-box">
                                            <strong>📝 Notes:</strong>
                                            <p style="margin-top: 0.5rem; margin-bottom: 0;">{{ $vaccination->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-message">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">💉</div>
                            <p>No vaccination records yet</p>
                        </div>
                    @endif
                </div>

                <!-- Prescriptions Tab -->
                <div class="tab-pane fade" id="prescriptions" role="tabpanel">
                    @if($prescriptions->count() > 0)
                        @foreach($prescriptions as $prescription)
                            <div class="record-card">
                                <div class="record-header">
                                    <div>
                                        <div class="record-date">{{ $prescription->medication }}</div>
                                        <div style="color: #6B7280; margin-top: 0.25rem;">
                                            Prescribed: {{ \Carbon\Carbon::parse($prescription->created_at)->format('F d, Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="record-content">
                                    <div class="info-row">
                                        <span class="info-label">Dosage:</span>
                                        <span class="info-value">{{ $prescription->dosage }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Frequency:</span>
                                        <span class="info-value">{{ $prescription->frequency }}</span>
                                    </div>
                                    @if($prescription->duration_days)
                                        <div class="info-row">
                                            <span class="info-label">Duration:</span>
                                            <span class="info-value">{{ $prescription->duration_days }} days</span>
                                        </div>
                                    @endif
                                    @if($prescription->prescribedBy)
                                        <div class="info-row">
                                            <span class="info-label">Prescribed By:</span>
                                            <span class="info-value">Dr. {{ $prescription->prescribedBy->first_name }} {{ $prescription->prescribedBy->last_name }}</span>
                                        </div>
                                    @endif
                                    @if($prescription->instructions)
                                        <div class="section-box">
                                            <strong>📋 Instructions:</strong>
                                            <p style="margin-top: 0.5rem; margin-bottom: 0;">{{ $prescription->instructions }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-message">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">💊</div>
                            <p>No prescription records yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
