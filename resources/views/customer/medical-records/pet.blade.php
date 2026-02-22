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
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="rounded-circle shadow-sm" width="60" height="60" style="object-fit: cover;">
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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="chronic-allergies-tab" data-bs-toggle="tab" data-bs-target="#chronic-allergies" type="button" role="tab" aria-controls="chronic-allergies" aria-selected="false">
                                Chronic & Allergies
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="surgeries-tab" data-bs-toggle="tab" data-bs-target="#surgeries" type="button" role="tab" aria-controls="surgeries" aria-selected="false">
                                Surgeries
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="labs-tab" data-bs-toggle="tab" data-bs-target="#labs" type="button" role="tab" aria-controls="labs" aria-selected="false">
                                Laboratory Tests
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
                                                    <td class="fw-bold">{{ $vac->inventoryItem->name ?? 'Unknown Vaccine' }}</td>
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
                        
                        <!-- Chronic Conditions & Allergies Tab -->
                        <div class="tab-pane fade" id="chronic-allergies" role="tabpanel" aria-labelledby="chronic-allergies-tab">
                            <div class="row">
                                <!-- Chronic Conditions -->
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Chronic Conditions</h5>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addChronicConditionModal">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                    @if($chronicConditions->isEmpty())
                                        <div class="text-center py-4">
                                            <p class="text-muted">No chronic conditions recorded.</p>
                                        </div>
                                    @else
                                        @foreach($chronicConditions as $condition)
                                            <div class="card border mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0">{{ $condition->condition_name }}</h6>
                                                        <div>
                                                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editChronicCondition({{ $condition->id }}, '{{ $condition->condition_name }}', '{{ $condition->diagnosed_date }}', '{{ addslashes($condition->ongoing_treatment ?? '') }}', '{{ addslashes($condition->notes ?? '') }}')" data-bs-toggle="modal" data-bs-target="#editChronicConditionModal">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form method="POST" action="{{ route('customer.medical-records.chronic-conditions.destroy', ['petId' => $pet->id, 'conditionId' => $condition->id]) }}" class="d-inline" onsubmit="return confirm('Delete this chronic condition?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="small text-muted mb-2">
                                                        <i class="far fa-calendar-alt"></i> Diagnosed: {{ $condition->diagnosed_date->format('M d, Y') }}
                                                    </div>
                                                    @if($condition->ongoing_treatment)
                                                        <div class="small mb-2">
                                                            <strong>Treatment:</strong> {{ $condition->ongoing_treatment }}
                                                        </div>
                                                    @endif
                                                    @if($condition->notes)
                                                        <div class="small">
                                                            <strong>Notes:</strong> {{ $condition->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <!-- Pet Allergies -->
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Allergies</h5>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAllergyModal">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                    @if($allergies->isEmpty())
                                        <div class="text-center py-4">
                                            <p class="text-muted">No allergies recorded.</p>
                                        </div>
                                    @else
                                        @foreach($allergies as $allergy)
                                            <div class="card border mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0">{{ $allergy->allergen }}</h6>
                                                        <div>
                                                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editAllergy({{ $allergy->id }}, '{{ $allergy->allergen }}', '{{ $allergy->reaction_type ?? '' }}', '{{ $allergy->severity ?? 'mild' }}', '{{ $allergy->diagnosed_date }}', '{{ addslashes($allergy->notes ?? '') }}')" data-bs-toggle="modal" data-bs-target="#editAllergyModal">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form method="POST" action="{{ route('customer.medical-records.allergies.destroy', ['petId' => $pet->id, 'allergyId' => $allergy->id]) }}" class="d-inline" onsubmit="return confirm('Delete this allergy?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="small text-muted mb-2">
                                                        <i class="far fa-calendar-alt"></i> Diagnosed: {{ $allergy->diagnosed_date->format('M d, Y') }}
                                                        @if($allergy->severity)
                                                            <span class="badge bg-{{ $allergy->severity === 'severe' ? 'danger' : ($allergy->severity === 'moderate' ? 'warning' : 'info') }} ms-2">
                                                                {{ ucfirst($allergy->severity) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($allergy->reaction_type)
                                                        <div class="small mb-2">
                                                            <strong>Reaction:</strong> {{ $allergy->reaction_type }}
                                                        </div>
                                                    @endif
                                                    @if($allergy->notes)
                                                        <div class="small">
                                                            <strong>Notes:</strong> {{ $allergy->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Surgeries Tab -->
                        <div class="tab-pane fade" id="surgeries" role="tabpanel" aria-labelledby="surgeries-tab">
                            @if($surgeries->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No surgery records found.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Procedure</th>
                                                <th>Date</th>
                                                <th>Surgeon</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($surgeries as $surgery)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold">{{ $surgery->surgeryType->name ?? 'N/A' }}</div>
                                                        @if($surgery->anesthesia_type)
                                                            <div class="small text-muted">{{ $surgery->anesthesia_type }}</div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $surgery->scheduled_date ? $surgery->scheduled_date->format('M d, Y H:i A') : '-' }}</td>
                                                    <td>{{ $surgery->surgeon ? 'Dr. ' . $surgery->surgeon->first_name . ' ' . $surgery->surgeon->last_name : '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $surgery->status === 'completed' ? 'success' : ($surgery->status === 'cancelled' ? 'danger' : 'info') }}">
                                                            {{ ucfirst($surgery->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ Str::limit($surgery->notes ?? '-', 50) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Laboratory Tests Tab -->
                        <div class="tab-pane fade" id="labs" role="tabpanel" aria-labelledby="labs-tab">
                            @if($labTests->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No laboratory test records found.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Test Name</th>
                                                <th>Test Date</th>
                                                <th>Results</th>
                                                <th>Ordered By</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($labTests as $test)
                                                <tr>
                                                    <td class="fw-bold">{{ $test->test->test_name ?? 'N/A' }}</td>
                                                    <td>{{ $test->requested_date ? $test->requested_date->format('M d, Y') : '-' }}</td>
                                                    <td>{{ Str::limit($test->results ?? 'Pending', 50) }}</td>
                                                    <td>{{ $test->requestedBy ? 'Dr. ' . $test->requestedBy->first_name . ' ' . $test->requestedBy->last_name : '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $test->status === 'completed' ? 'success' : ($test->status === 'pending' ? 'warning' : 'info') }}">
                                                            {{ ucfirst($test->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Chronic Condition Modal -->
<div class="modal fade" id="addChronicConditionModal" tabindex="-1" aria-labelledby="addChronicConditionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.medical-records.chronic-conditions.store', $pet->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addChronicConditionModalLabel">Add Chronic Condition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="condition_name" class="form-label">Condition Name*</label>
                        <input type="text" class="form-control" id="condition_name" name="condition_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="diagnosed_date" class="form-label">Diagnosed Date*</label>
                        <input type="date" class="form-control" id="diagnosed_date" name="diagnosed_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="ongoing_treatment" class="form-label">Ongoing Treatment</label>
                        <textarea class="form-control" id="ongoing_treatment" name="ongoing_treatment" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Chronic Condition Modal -->
<div class="modal fade" id="editChronicConditionModal" tabindex="-1" aria-labelledby="editChronicConditionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editChronicConditionForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editChronicConditionModalLabel">Edit Chronic Condition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_condition_name" class="form-label">Condition Name*</label>
                        <input type="text" class="form-control" id="edit_condition_name" name="condition_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_diagnosed_date" class="form-label">Diagnosed Date*</label>
                        <input type="date" class="form-control" id="edit_diagnosed_date" name="diagnosed_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_ongoing_treatment" class="form-label">Ongoing Treatment</label>
                        <textarea class="form-control" id="edit_ongoing_treatment" name="ongoing_treatment" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Allergy Modal -->
<div class="modal fade" id="addAllergyModal" tabindex="-1" aria-labelledby="addAllergyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.medical-records.allergies.store', $pet->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addAllergyModalLabel">Add Allergy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="allergen" class="form-label">Allergen*</label>
                        <input type="text" class="form-control" id="allergen" name="allergen" required>
                    </div>
                    <div class="mb-3">
                        <label for="reaction_type" class="form-label">Reaction Type</label>
                        <input type="text" class="form-control" id="reaction_type" name="reaction_type" placeholder="e.g., Skin rash, Vomiting, etc.">
                    </div>
                    <div class="mb-3">
                        <label for="severity" class="form-label">Severity</label>
                        <select class="form-select" id="severity" name="severity">
                            <option value="mild">Mild</option>
                            <option value="moderate">Moderate</option>
                            <option value="severe">Severe</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="allergy_diagnosed_date" class="form-label">Diagnosed Date*</label>
                        <input type="date" class="form-control" id="allergy_diagnosed_date" name="diagnosed_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="allergy_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="allergy_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Allergy Modal -->
<div class="modal fade" id="editAllergyModal" tabindex="-1" aria-labelledby="editAllergyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editAllergyForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editAllergyModalLabel">Edit Allergy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_allergen" class="form-label">Allergen*</label>
                        <input type="text" class="form-control" id="edit_allergen" name="allergen" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_reaction_type" class="form-label">Reaction Type</label>
                        <input type="text" class="form-control" id="edit_reaction_type" name="reaction_type">
                    </div>
                    <div class="mb-3">
                        <label for="edit_severity" class="form-label">Severity</label>
                        <select class="form-select" id="edit_severity" name="severity">
                            <option value="mild">Mild</option>
                            <option value="moderate">Moderate</option>
                            <option value="severe">Severe</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_allergy_diagnosed_date" class="form-label">Diagnosed Date*</label>
                        <input type="date" class="form-control" id="edit_allergy_diagnosed_date" name="diagnosed_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_allergy_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_allergy_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editChronicCondition(id, name, diagnosedDate, treatment, notes) {
    document.getElementById('edit_condition_name').value = name;
    document.getElementById('edit_diagnosed_date').value = diagnosedDate;
    document.getElementById('edit_ongoing_treatment').value = treatment;
    document.getElementById('edit_notes').value = notes;
    document.getElementById('editChronicConditionForm').action = '/customer/medical-records/pets/{{ $pet->id }}/chronic-conditions/' + id;
}

function editAllergy(id, allergen, reactionType, severity, diagnosedDate, notes) {
    document.getElementById('edit_allergen').value = allergen;
    document.getElementById('edit_reaction_type').value = reactionType;
    document.getElementById('edit_severity').value = severity;
    document.getElementById('edit_allergy_diagnosed_date').value = diagnosedDate;
    document.getElementById('edit_allergy_notes').value = notes;
    document.getElementById('editAllergyForm').action = '/customer/medical-records/pets/{{ $pet->id }}/allergies/' + id;
}
</script>
@endsection
