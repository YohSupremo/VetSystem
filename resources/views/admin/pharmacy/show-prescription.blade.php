@extends('admin.dashboard')

@section('page-title', 'Prescription Details')
@section('page-description', 'View prescription details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i class="fas fa-prescription-bottle"></i> Prescription Details</h1>
                    <p class="text-muted">View prescription details</p>
                </div>
                <div>
                    <a href="{{ route('admin.pharmacy.prescriptions.edit', $prescription->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.pharmacy.prescriptions') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Prescriptions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Prescription Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Prescription Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Medication Details</h6>
                            <p><strong>Medication:</strong> {{ $prescription->medication }}</p>
                            <p><strong>Dosage:</strong> {{ $prescription->dosage }}</p>
                            <p><strong>Frequency:</strong> {{ $prescription->frequency }}</p>
                            <p><strong>Duration:</strong> {{ $prescription->duration_days }} days</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Status & Timeline</h6>
                            <p>
                                <strong>Status:</strong>
                                @if($prescription->dispensed)
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </p>
                            <p><strong>Created:</strong> {{ $prescription->created_at->format('M j, Y \a\t g:i A') }}</p>
                            <p><strong>Last Updated:</strong> {{ $prescription->updated_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    @if($prescription->instructions)
                        <div class="mt-3">
                            <h6 class="text-muted">Instructions</h6>
                            <p class="mb-0">{{ $prescription->instructions }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Medical Record Details -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Medical Record Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Pet Information</h6>
                            <p><strong>Pet Name:</strong> {{ $prescription->medicalRecord->pet->name }}</p>
                            <p><strong>Species:</strong> {{ $prescription->medicalRecord->pet->species }}</p>
                            <p><strong>Breed:</strong> {{ $prescription->medicalRecord->pet->breed }}</p>
                            <p><strong>Age:</strong> {{ $prescription->medicalRecord->pet->age_years }} years, {{ $prescription->medicalRecord->pet->age_months }} months</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Owner Information</h6>
                            <p><strong>Owner:</strong> {{ $prescription->medicalRecord->pet->owner->user->first_name }} {{ $prescription->medicalRecord->pet->owner->user->last_name }}</p>
                            <p><strong>Email:</strong> {{ $prescription->medicalRecord->pet->owner->user->email }}</p>
                            <p><strong>Phone:</strong> {{ $prescription->medicalRecord->pet->owner->phone }}</p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h6 class="text-muted">Visit Details</h6>
                        <p><strong>Visit Date:</strong> {{ $prescription->medicalRecord->visit_date->format('M j, Y') }}</p>
                        <p><strong>Veterinarian:</strong> {{ $prescription->medicalRecord->veterinarian->user->first_name }} {{ $prescription->medicalRecord->veterinarian->user->last_name }}</p>
                        @if($prescription->medicalRecord->complaint)
                            <p><strong>Complaint:</strong> {{ $prescription->medicalRecord->complaint }}</p>
                        @endif
                        @if($prescription->medicalRecord->diagnosis)
                            <p><strong>Diagnosis:</strong> {{ $prescription->medicalRecord->diagnosis }}</p>
                        @endif
                        @if($prescription->medicalRecord->treatment)
                            <p><strong>Treatment:</strong> {{ $prescription->medicalRecord->treatment }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cogs"></i> Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$prescription->dispensed)
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#dispenseModal">
                                <i class="fas fa-hand-holding-medical"></i> Dispense Medication
                            </button>
                        @else
                            <div class="alert alert-success mb-2">
                                <i class="fas fa-check-circle"></i> 
                                Dispensed on {{ $prescription->dispensed_at->format('M j, Y \a\t g:i A') }}
                            </div>
                        @endif
                        <a href="{{ route('admin.pharmacy.prescriptions.edit', $prescription->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Prescription
                        </a>
                        @if(!$prescription->dispensed)
                            <form method="POST" action="{{ route('admin.pharmacy.prescriptions.destroy', $prescription->id) }}" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this prescription?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> Delete Prescription
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Records -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-link"></i> Related Records</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <a href="{{ route('admin.medical-records.show', $prescription->medicalRecord->id) }}" class="text-decoration-none">
                            <i class="fas fa-file-medical text-primary me-2"></i> View Medical Record
                        </a>
                    </p>
                    <p class="mb-2">
                        <a href="{{ route('admin.pets.show', $prescription->medicalRecord->pet->id) }}" class="text-decoration-none">
                            <i class="fas fa-paw text-success me-2"></i> View Pet Profile
                        </a>
                    </p>
                    <p class="mb-0">
                        <a href="{{ route('admin.pet-owners.show', $prescription->medicalRecord->pet->owner->id) }}" class="text-decoration-none">
                            <i class="fas fa-user text-info me-2"></i> View Owner Profile
                        </a>
                    </p>
                </div>
            </div>

            <!-- Inventory Check -->
            @php
                $inventoryItem = \App\Models\InventoryItem::where('name', 'like', '%' . $prescription->medication . '%')->first();
            @endphp
            @if($inventoryItem)
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-boxes"></i> Inventory Status</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Medication:</strong> {{ $inventoryItem->name }}</p>
                        <p class="mb-1">
                            <strong>Stock:</strong>
                            @if($inventoryItem->total_stock > 10)
                                <span class="text-success">{{ $inventoryItem->total_stock }} units</span>
                            @elseif($inventoryItem->total_stock > 0)
                                <span class="text-warning">{{ $inventoryItem->total_stock }} units</span>
                            @else
                                <span class="text-danger">Out of stock</span>
                            @endif
                        </p>
                        @if($inventoryItem->expiry_date)
                            <p class="mb-0">
                                <strong>Expires:</strong>
                                @if($inventoryItem->expiry_date->isPast())
                                    <span class="text-danger">{{ $inventoryItem->expiry_date->format('M j, Y') }} (Expired)</span>
                                @elseif($inventoryItem->expiry_date->diffInDays() < 30)
                                    <span class="text-warning">{{ $inventoryItem->expiry_date->format('M j, Y') }} (Soon)</span>
                                @else
                                    <span class="text-success">{{ $inventoryItem->expiry_date->format('M j, Y') }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Dispense Medication Modal -->
@if(!$prescription->dispensed)
<div class="modal fade" id="dispenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-hand-holding-medical"></i> Dispense Medication
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.pharmacy.prescriptions.dispense', $prescription->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Patient:</strong> {{ $prescription->medicalRecord->pet->name }}<br>
                        <strong>Medication:</strong> {{ $prescription->medication }}<br>
                        <strong>Dosage:</strong> {{ $prescription->dosage }}
                    </div>

                    <div class="mb-3">
                        <label for="inventory_item_id" class="form-label">Select Inventory Item *</label>
                        <select name="inventory_item_id" id="inventory_item_id" class="form-select" required>
                            <option value="">Choose medication from inventory</option>
                            @foreach($suggestedMedications as $medication)
                                <option value="{{ $medication->id }}" 
                                        data-stock="{{ $medication->total_stock }}"
                                        data-price="{{ $medication->unit_price }}">
                                    {{ $medication->name }} 
                                    ({{ $medication->total_stock }} in stock - ${{ number_format($medication->unit_price, 2) }})
                                    @if($medication->dosage_form) - {{ $medication->dosage_form }} @endif
                                    @if($medication->strength) - {{ $medication->strength }} @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Select the actual medication item to dispense from inventory</div>
                    </div>

                    <div class="mb-3">
                        <label for="quantity_dispensed" class="form-label">Quantity to Dispense *</label>
                        <input type="number" name="quantity_dispensed" id="quantity_dispensed" 
                               class="form-control" min="1" value="{{ $prescription->quantity ?? 1 }}" required>
                        <div class="form-text">Number of units to dispense (tablets, bottles, etc.)</div>
                    </div>

                    <div class="mb-3">
                        <label for="unit_price" class="form-label">Unit Price *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="unit_price" id="unit_price" 
                                   class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-text">Price per unit</div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Dispensing Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Any special instructions or notes for dispensing..."></textarea>
                    </div>

                    <div id="stockWarning" class="alert alert-warning d-none">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="stockWarningText"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Dispense Medication
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('inventory_item_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const stock = selected.dataset.stock;
    const price = selected.dataset.price;
    const stockWarning = document.getElementById('stockWarning');
    const stockWarningText = document.getElementById('stockWarningText');
    
    if (price) {
        document.getElementById('unit_price').value = price;
    }
    
    if (stock && stock > 0) {
        const quantity = document.getElementById('quantity_dispensed').value;
        if (parseInt(quantity) > parseInt(stock)) {
            stockWarning.classList.remove('d-none');
            stockWarningText.textContent = `Warning: Only ${stock} units available in stock, but you want to dispense ${quantity} units.`;
        } else {
            stockWarning.classList.add('d-none');
        }
    } else {
        stockWarning.classList.remove('d-none');
        stockWarningText.textContent = 'Warning: This item is out of stock.';
    }
});

document.getElementById('quantity_dispensed').addEventListener('input', function() {
    const inventorySelect = document.getElementById('inventory_item_id');
    const selected = inventorySelect.options[inventorySelect.selectedIndex];
    const stock = selected.dataset.stock;
    const stockWarning = document.getElementById('stockWarning');
    const stockWarningText = document.getElementById('stockWarningText');
    
    if (stock && stock > 0) {
        const quantity = this.value;
        if (parseInt(quantity) > parseInt(stock)) {
            stockWarning.classList.remove('d-none');
            stockWarningText.textContent = `Warning: Only ${stock} units available in stock, but you want to dispense ${quantity} units.`;
        } else {
            stockWarning.classList.add('d-none');
        }
    }
});
</script>
@endif
@endsection