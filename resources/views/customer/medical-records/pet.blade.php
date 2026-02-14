@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('customer.medical-records.index') }}" class="text-decoration-none text-muted me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h2 mb-1">{{ $pet->name }}'s Medical History</h1>
                    <p class="text-muted mb-0">Complete timeline of visits and treatments</p>
                </div>
                <div class="ms-auto">
                    @if($pet->photo_path)
                        <img src="{{ asset($pet->photo_path) }}" alt="{{ $pet->name }}" class="rounded-circle shadow-sm" width="60" height="60" style="object-fit: cover;">
                    @else
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                            <i class="fas fa-paw fa-2x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <ul class="nav nav-tabs card-header-tabs" id="recordTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="records-tab" data-bs-toggle="tab" data-bs-target="#records" type="button" role="tab" aria-controls="records" aria-selected="true">
                                Medical Visits
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="vaccinations-tab" data-bs-toggle="tab" data-bs-target="#vaccinations" type="button" role="tab" aria-controls="vaccinations" aria-selected="false">
                                Vaccinations
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab" aria-controls="prescriptions" aria-selected="false">
                                Prescriptions
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4">
                    <div class="tab-content" id="recordTabsContent">
                        <!-- Medical Records Tab -->
                        <div class="tab-pane fade show active" id="records" role="tabpanel" aria-labelledby="records-tab">
                            @if($medicalRecords->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No medical records found for this pet.</p>
                                </div>
                            @else
                                <div class="timeline">
                                    @foreach($medicalRecords as $record)
                                        <div class="border-start border-3 border-primary ps-4 pb-5 ms-2 position-relative">
                                            <div class="position-absolute top-0 start-0 translate-middle bg-primary rounded-circle shadow-sm" style="width: 16px; height: 16px; margin-top: 5px;"></div>
                                            
                                            <div class="card border-0 bg-light mb-2">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="fw-bold mb-1">{{ $record->diagnosis ?: 'Checkup / Consultation' }}</h5>
                                                            <div class="text-muted small">
                                                                <i class="far fa-calendar-alt me-1"></i> {{ date('F d, Y', strtotime($record->visit_date)) }}
                                                                @if($record->weight)
                                                                    <span class="mx-2">•</span>
                                                                    <i class="fas fa-weight me-1"></i> {{ $record->weight }} kg
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('customer.medical-records.show', ['petId' => $pet->id, 'recordId' => $record->id]) }}" class="btn btn-sm btn-outline-primary">
                                                            Details
                                                        </a>
                                                    </div>
                                                    
                                                    <p class="mb-2">{{ Str::limit($record->treatment, 150) }}</p>
                                                    
                                                    @if($record->veterinarian)
                                                        <div class="d-flex align-items-center mt-3 pt-3 border-top border-white">
                                                            <div class="small text-muted">Attending Vet:</div>
                                                            <div class="ms-2 fw-bold small">Dr. {{ $record->veterinarian->first_name }} {{ $record->veterinarian->last_name }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Vaccinations Tab -->
                        <div class="tab-pane fade" id="vaccinations" role="tabpanel" aria-labelledby="vaccinations-tab">
                            @if($vaccinations->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No vaccination history found.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Vaccine</th>
                                                <th>Date Administered</th>
                                                <th>Next Due</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vaccinations as $vac)
                                                <tr>
                                                    <td class="fw-bold">{{ $vac->vaccine->vaccine_name ?? 'Unknown Vaccine' }}</td>
                                                    <td>{{ date('M d, Y', strtotime($vac->administered_date)) }}</td>
                                                    <td>
                                                        @if($vac->next_due_date)
                                                            <span class="badge {{ $vac->next_due_date < now() ? 'bg-danger' : 'bg-success' }}">
                                                                {{ date('M d, Y', strtotime($vac->next_due_date)) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $vac->remarks ?: '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Prescriptions Tab -->
                        <div class="tab-pane fade" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab">
                            @if($prescriptions->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No details found.</p>
                                </div>
                            @else
                                <div class="row g-3">
                                    @foreach($prescriptions as $presc)
                                        <div class="col-md-6">
                                            <div class="card h-100 border bg-light">
                                                <div class="card-body">
                                                    <h6 class="fw-bold">{{ $presc->medication_name }}</h6>
                                                    <p class="small text-muted mb-2">{{ date('M d, Y', strtotime($presc->created_at)) }}</p>
                                                    <hr class="my-2">
                                                    <div class="input-group input-group-sm mb-2">
                                                        <span class="input-group-text bg-white">Dosage</span>
                                                        <input type="text" class="form-control bg-white" value="{{ $presc->dosage }}" readonly>
                                                    </div>
                                                    <div class="input-group input-group-sm mb-2">
                                                        <span class="input-group-text bg-white">Frequency</span>
                                                        <input type="text" class="form-control bg-white" value="{{ $presc->frequency }}" readonly>
                                                    </div>
                                                    <div class="small">
                                                        <strong>Duration:</strong> {{ $presc->duration }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
