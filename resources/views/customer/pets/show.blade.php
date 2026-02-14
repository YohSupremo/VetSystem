@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar: Pet Profile -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3 position-relative d-inline-block">
                        @if($pet->photo_path)
                            <img src="{{ asset($pet->photo_path) }}" alt="{{ $pet->name }}" class="rounded-circle shadow-sm" width="150" height="150" style="object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 150px; height: 150px;">
                                <i class="fas fa-paw fa-4x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h2 class="fw-bold mb-1">{{ $pet->name }}</h2>
                    <p class="text-muted mb-3">{{ $pet->species }} • {{ $pet->breed }}</p>
                    
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <div class="text-center px-2">
                            <div class="fw-bold fs-5">{{ $pet->age }}</div>
                            <div class="small text-muted">Age</div>
                        </div>
                        <div class="vr"></div>
                        <div class="text-center px-2">
                            <div class="fw-bold fs-5">{{ $pet->gender }}</div>
                            <div class="small text-muted">Gender</div>
                        </div>
                        <div class="vr"></div>
                        <div class="text-center px-2">
                            <div class="fw-bold fs-5">{{ $pet->weight }} kg</div>
                            <div class="small text-muted">Weight</div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.pets.edit', $pet->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('customer.appointments.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white fw-bold py-3">Details</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex justify-content-between">
                            <span class="text-muted">Birth Date</span>
                            <span class="fw-bold">{{ date('M d, Y', strtotime($pet->dob)) }}</span>
                        </li>
                        <li class="mb-3 d-flex justify-content-between">
                            <span class="text-muted">Color</span>
                            <span class="fw-bold">{{ $pet->color ?: 'N/A' }}</span>
                        </li>
                        <li class="mb-3 d-flex justify-content-between">
                            <span class="text-muted">Microchip/Reg #</span>
                            <span class="fw-bold">{{ $pet->registration_number ?: 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Main Content: History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0">Recent Medical History</h5>
                    <a href="{{ route('customer.medical-records.pet', $pet->id) }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
                </div>
                <div class="card-body">
                    @if($pet->medicalRecords->isEmpty())
                        <p class="text-muted text-center py-4">No medical records found.</p>
                    @else
                        <div class="timeline">
                            @foreach($pet->medicalRecords->take(3) as $record)
                                <div class="border-start border-3 border-primary ps-3 pb-4 ms-2 position-relative">
                                    <div class="position-absolute top-0 start-0 translate-middle bg-primary rounded-circle" style="width: 12px; height: 12px;"></div>
                                    <div class="text-muted small mb-1">{{ date('M d, Y', strtotime($record->visit_date)) }}</div>
                                    <h6 class="fw-bold">{{ $record->diagnosis ?: 'Checkup' }}</h6>
                                    <p class="mb-1 text-muted small">{{ Str::limit($record->treatment, 100) }}</p>
                                    @if($record->veterinarian)
                                        <div class="small text-primary">Dr. {{ $record->veterinarian->last_name }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white fw-bold py-3">Recent Vaccinations</div>
                        <div class="card-body">
                            @if($pet->vaccinations->isEmpty())
                                <p class="text-muted text-center py-3">No vaccinations recorded.</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($pet->vaccinations->take(3) as $vac)
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <div class="fw-bold">{{ $vac->vaccine_name }}</div>
                                                <div class="text-muted small">{{ date('M d, Y', strtotime($vac->administered_date)) }}</div>
                                            </div>
                                            <div class="small text-muted">Next due: {{ $vac->next_due_date ? date('M d, Y', strtotime($vac->next_due_date)) : 'N/A' }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white fw-bold py-3">Active Prescriptions</div>
                        <div class="card-body">
                            @if($pet->prescriptions->isEmpty())
                                <p class="text-muted text-center py-3">No prescriptions found.</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($pet->prescriptions->take(3) as $presc)
                                        <li class="list-group-item px-0">
                                            <div class="fw-bold">{{ $presc->medication_name }}</div>
                                            <div class="small text-muted">{{ $presc->dosage }} - {{ $presc->frequency }}</div>
                                            @if($presc->end_date && $presc->end_date >= now())
                                                <div class="badge bg-success mt-1">Active</div>
                                            @else
                                                <div class="badge bg-secondary mt-1">Completed</div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
