@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('customer.medical-records.pet', $pet->id) }}" class="text-decoration-none text-muted me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0">Visit Details</h1>
            </div>

            <div class="card border-0 shadow-sm print-section">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between">
                    <div>
                        <h2 class="h4 fw-bold mb-1">{{ $medicalRecord->diagnosis ?: 'Medical Consultation' }}</h2>
                        <p class="text-muted mb-0">{{ date('l, F d, Y', strtotime($medicalRecord->visit_date)) }}</p>
                    </div>
                    <div class="text-end">
                        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-print-none">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Pet & Vet Info -->
                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-md-6 border-end">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Patient</h6>
                            <div class="d-flex align-items-center">
                                <img src="{{ $pet->photo_url }}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <h5 class="mb-1">{{ $pet->name }}</h5>
                                    <div class="small text-muted">{{ $pet->species }} • {{ $pet->breed }}</div>
                                    @if($medicalRecord->weight)
                                        <div class="badge bg-light text-dark mt-1">Weight: {{ $medicalRecord->weight }} kg</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Veterinarian</h6>
                            @if($medicalRecord->veterinarian)
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">Dr</div>
                                    <div>
                                        <h5 class="mb-1">Dr. {{ $medicalRecord->veterinarian->first_name }} {{ $medicalRecord->veterinarian->last_name }}</h5>
                                        <div class="small text-muted">Licensed Veterinarian</div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">Not specified</p>
                            @endif
                        </div>
                    </div>

                    <!-- Symptoms & Diagnosis -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3"><i class="fas fa-stethoscope me-2"></i>Symptoms & Diagnosis</h5>
                        <div class="bg-light p-3 rounded-3">
                            <div class="mb-3">
                                <span class="fw-bold d-block mb-1">Symptoms Reported:</span>
                                <p class="mb-0">{{ $medicalRecord->symptoms ?: 'None reported' }}</p>
                            </div>
                            <div>
                                <span class="fw-bold d-block mb-1">Diagnosis:</span>
                                <p class="mb-0">{{ $medicalRecord->diagnosis ?: 'Routine Checkup' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Treatment -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3"><i class="fas fa-briefcase-medical me-2"></i>Treatment Plan</h5>
                        <div class="bg-light p-3 rounded-3">
                            <p class="mb-0" style="white-space: pre-line;">{{ $medicalRecord->treatment ?: 'No specific treatment recorded.' }}</p>
                        </div>
                    </div>

                    <!-- Prescriptions -->
                    @if($medicalRecord->prescriptions->isNotEmpty())
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-pills me-2"></i>Prescribed Medications</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Medication</th>
                                            <th>Dosage</th>
                                            <th>Frequency</th>
                                            <th>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($medicalRecord->prescriptions as $presc)
                                            <tr>
                                                <td class="fw-bold">{{ $presc->medication_name }}</td>
                                                <td>{{ $presc->dosage }}</td>
                                                <td>{{ $presc->frequency }}</td>
                                                <td>{{ $presc->duration }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Follow-up -->
                    @if($medicalRecord->follow_up_date)
                        <div class="alert alert-info d-flex align-items-center mt-4">
                            <i class="fas fa-calendar-check fa-2x me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Follow-up Required</h6>
                                <p class="mb-0">Please schedule a follow-up visit on or around <strong>{{ date('F d, Y', strtotime($medicalRecord->follow_up_date)) }}</strong>.</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer bg-light p-4 text-center text-muted small">
                    This is an official medical record generated by {{ $clinicName ?? 'PawCare' }}.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
