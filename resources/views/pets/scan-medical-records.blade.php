@extends('layout.base')

@section('title', $pet->name . ' Medical Record Scan - PawCare')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <button type="button" onclick="window.print()" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-print me-1"></i> Print Page
                </button>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-3">
                        <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" width="110" height="110" class="rounded-3" style="object-fit: cover;">
                        <div class="text-center text-md-start">
                            <h1 class="h4 fw-bold mb-1">{{ $pet->name }} - Medical Record</h1>
                            <div class="text-muted small mb-2">Scanned QR medical record page</div>
                            <div class="small"><strong>Owner:</strong> {{ $pet->owner->user->first_name ?? '' }} {{ $pet->owner->user->last_name ?? '' }}</div>
                            <div class="small"><strong>Species/Breed:</strong> {{ $pet->species }}{{ $pet->breed ? ' / ' . $pet->breed : '' }}</div>
                            <div class="small"><strong>Registration #:</strong> {{ $pet->registration_number ?: 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Complete Medical Records</div>
                <div class="card-body">
                    @if($pet->medicalRecords->isEmpty())
                        <p class="text-muted mb-0">No medical records available yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Complaint</th>
                                        <th>Symptoms</th>
                                        <th>Diagnosis</th>
                                        <th>Treatment</th>
                                        <th>Follow-up</th>
                                        <th>Veterinarian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pet->medicalRecords as $record)
                                        <tr>
                                            <td>{{ $record->visit_date ? \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td style="white-space: pre-line;">{{ $record->complaint ?: 'N/A' }}</td>
                                            <td style="white-space: pre-line;">{{ $record->symptoms ?: 'N/A' }}</td>
                                            <td>{{ $record->diagnosis ?: 'N/A' }}</td>
                                            <td style="white-space: pre-line;">{{ $record->treatment ?: 'N/A' }}</td>
                                            <td>{{ $record->follow_up_date ? \Carbon\Carbon::parse($record->follow_up_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @if($record->veterinarian)
                                                    Dr. {{ $record->veterinarian->first_name }} {{ $record->veterinarian->last_name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Chronic Conditions and Illness</div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3">Chronic Conditions</h6>
                            @if($pet->chronicConditions->isEmpty())
                                <p class="text-muted mb-0">No chronic conditions found.</p>
                            @else
                                @foreach($pet->chronicConditions as $condition)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="fw-semibold">{{ $condition->condition_name }}</div>
                                        <div class="small">Diagnosed: {{ $condition->diagnosed_date ? $condition->diagnosed_date->format('M d, Y') : 'N/A' }}</div>
                                        <div class="small">Status: {{ $condition->is_active ? 'Active' : 'Inactive' }}</div>
                                        <div class="small" style="white-space: pre-line;">Treatment: {{ $condition->ongoing_treatment ?: 'N/A' }}</div>
                                        <div class="small" style="white-space: pre-line;">Notes: {{ $condition->notes ?: 'N/A' }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3">Allergies / Illness Notes</h6>
                            @if($pet->allergies->isEmpty())
                                <p class="text-muted mb-0">No allergies found.</p>
                            @else
                                @foreach($pet->allergies as $allergy)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="fw-semibold">{{ $allergy->allergen }}</div>
                                        <div class="small">Reaction: {{ $allergy->reaction_type ?: 'N/A' }}</div>
                                        <div class="small">Severity: {{ $allergy->severity ?: 'N/A' }}</div>
                                        <div class="small">Diagnosed: {{ $allergy->diagnosed_date ? $allergy->diagnosed_date->format('M d, Y') : 'N/A' }}</div>
                                        <div class="small">Status: {{ $allergy->is_active ? 'Active' : 'Inactive' }}</div>
                                        <div class="small" style="white-space: pre-line;">Notes: {{ $allergy->notes ?: 'N/A' }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white fw-bold">All Prescriptions</div>
                        <div class="card-body">
                            @if($pet->prescriptions->isEmpty())
                                <p class="text-muted mb-0">No prescriptions found.</p>
                            @else
                                @foreach($pet->prescriptions as $prescription)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="fw-semibold">{{ $prescription->medication_name }}</div>
                                        <div class="small text-muted">{{ $prescription->dosage }} - {{ $prescription->frequency }}</div>
                                        <div class="small">Duration: {{ $prescription->duration_days ? $prescription->duration_days . ' days' : 'N/A' }}</div>
                                        <div class="small">Issued: {{ $prescription->created_at?->format('M d, Y') }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white fw-bold">Complete Vaccination History</div>
                        <div class="card-body">
                            @if($pet->vaccinations->isEmpty())
                                <p class="text-muted mb-0">No vaccinations found.</p>
                            @else
                                @foreach($pet->vaccinations as $vaccination)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="fw-semibold">{{ $vaccination->vaccine_name }}</div>
                                        <div class="small">Type: {{ $vaccination->vaccine_type ?: 'N/A' }}</div>
                                        <div class="small">Manufacturer: {{ $vaccination->manufacturer ?: 'N/A' }}</div>
                                        <div class="small">Batch #: {{ $vaccination->batch_number ?: 'N/A' }}</div>
                                        <div class="small">Dose #: {{ $vaccination->dose_number ?: 'N/A' }}</div>
                                        <div class="small">Administered: {{ $vaccination->administered_date ? \Carbon\Carbon::parse($vaccination->administered_date)->format('M d, Y') : 'N/A' }}</div>
                                        <div class="small">Next Due: {{ $vaccination->next_due_date ? \Carbon\Carbon::parse($vaccination->next_due_date)->format('M d, Y') : 'N/A' }}</div>
                                        <div class="small" style="white-space: pre-line;">Notes: {{ $vaccination->notes ?: 'N/A' }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
