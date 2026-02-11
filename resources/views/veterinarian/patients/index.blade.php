@extends('veterinarian.layout')

@section('title', 'Patients - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Patients</h2>
        <a href="#" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    @if($patients->count() > 0)
        <div class="row g-4">
            @foreach($patients as $patient)
                <div class="col-md-6 col-lg-4">
                    <div class="content-card" style="padding: 1.5rem;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="pet-avatar me-3">🐾</div>
                            <div>
                                <h5 class="mb-1">{{ $patient->name }}</h5>
                                <p class="mb-0 text-muted">{{ $patient->species }} • {{ $patient->breed }}</p>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6"><strong>Age:</strong></div>
                            <div class="col-6">{{ $patient->age }} years</div>
                            
                            <div class="col-6"><strong>Weight:</strong></div>
                            <div class="col-6">{{ $patient->weight }} kg</div>
                            
                            <div class="col-6"><strong>Owner:</strong></div>
                            <div class="col-6">{{ $patient->owner->first_name }} {{ $patient->owner->last_name }}</div>
                            
                            <div class="col-6"><strong>Visits:</strong></div>
                            <div class="col-6">{{ $patient->appointments->count() }}</div>
                        </div>

                        <a href="#" class="btn-action w-100">
                            <i class="fas fa-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $patients->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🐾</div>
            <h3>No patients found</h3>
            <p>You don't have any assigned patients yet.</p>
        </div>
    @endif
</div>
@endsection
