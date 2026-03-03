@extends('admin.dashboard')

@section('page-title', 'Create Vaccination Record')
@section('page-description', 'Record new vaccination for pet')

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-syringe"></i> New Vaccination</h2>
            <a href="{{ route('admin.vaccinations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.vaccinations.store') }}" method="POST" class="form-main">
            @csrf
            @if(!empty($selectedAppointmentId))
                <input type="hidden" name="appointment_id" value="{{ $selectedAppointmentId }}">
            @endif

            @if(!empty($appointmentContext))
                <div class="form-section" style="margin-bottom: 18px;">
                    <div class="appointment-context-box">
                        <strong>Accepted Appointment:</strong>
                        #{{ $appointmentContext->id }}
                        @if($appointmentContext->appointment_date)
                            • {{ \Carbon\Carbon::parse($appointmentContext->appointment_date)->format('M d, Y h:i A') }}
                        @endif
                    </div>
                </div>
            @endif

            <div class="form-section">
                <h3>Basic Information</h3>
                
                <div class="form-group">
                    <label>Select Pet <span class="text-danger">*</span></label>
                    @if(!empty($appointmentContext))
                        <input type="hidden" name="pet_id" value="{{ old('pet_id', $selectedPetId ?? '') }}">
                    @endif
                    <select name="pet_id" class="form-control" {{ !empty($appointmentContext) ? 'disabled' : '' }}>
                        <option value="">Choose a pet...</option>
                        @forelse($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', $selectedPetId ?? '') == $pet->id ? 'selected' : '' }}>
                                {{ $pet->name }} - {{ $pet->owner->user->first_name ?? 'Unknown' }}
                            </option>
                        @empty
                            <option value="">No pets available</option>
                        @endforelse
                    </select>
                    @error('pet_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div id="allergy-alert-box" class="allergy-alert-box" style="display: none;"></div>

                <div class="form-group">
                    <label>Vaccine <span class="text-danger">*</span></label>
                    <select name="inventory_item_id" id="inventory_item_id" class="form-control">
                        <option value="">Choose a vaccine...</option>
                        @forelse($vaccineItems as $item)
                            <option value="{{ $item->id }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @empty
                            <option value="">No vaccines available in inventory</option>
                        @endforelse
                    </select>
                    @error('inventory_item_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Administration Details</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Vaccination Date <span class="text-danger">*</span></label>
                        <input type="date" name="administered_date" class="form-control" value="{{ old('administered_date', $selectedAdministeredDate ?? date('Y-m-d')) }}">
                        @error('administered_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Veterinarian <span class="text-danger">*</span></label>
                        <select name="administered_by" class="form-control">
                            <option value="">Select veterinarian...</option>
                            @forelse($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('administered_by', $selectedVeterinarianId ?? '') == $vet->id ? 'selected' : '' }}>
                                    Dr. {{ $vet->first_name }} {{ $vet->last_name }}
                                </option>
                            @empty
                                <option value="">No veterinarians available</option>
                            @endforelse
                        </select>
                        @error('administered_by')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Dose Number <span class="text-danger">*</span></label>
                    <select name="dose_number" class="form-control">
                        <option value="">Select dose...</option>
                        <option value="1" {{ old('dose_number') == '1' ? 'selected' : '' }}>1st Dose (Initial)</option>
                        <option value="2" {{ old('dose_number') == '2' ? 'selected' : '' }}>2nd Dose</option>
                        <option value="3" {{ old('dose_number') == '3' ? 'selected' : '' }}>3rd Dose</option>
                        <option value="4" {{ old('dose_number') == '4' ? 'selected' : '' }}>Booster</option>
                    </select>
                    @error('dose_number')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Batch Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number') }}" placeholder="e.g., AB123456">
                    </div>
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                        @error('expiry_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Next Due Date</label>
                    <input type="date" name="next_due_date" class="form-control" value="{{ old('next_due_date') }}" placeholder="YYYY-MM-DD">
                    @error('next_due_date')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Additional Notes</h3>
                
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Additional notes about the vaccination">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Vaccination
                </button>
                <a href="{{ route('admin.vaccinations.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const petSelect = document.querySelector('select[name="pet_id"]');
    const allergyAlertBox = document.getElementById('allergy-alert-box');
    const allergyMap = @json($allergyMap ?? []);
    const administeredDateInput = document.querySelector('input[name="administered_date"]');
    const veterinarianSelect = document.querySelector('select[name="administered_by"]');

    if (!petSelect || !allergyAlertBox) {
        return;
    }

    function renderAllergyAlert(petId) {
        const allergies = allergyMap[String(petId)] || [];

        if (!petId || allergies.length === 0) {
            allergyAlertBox.style.display = 'none';
            allergyAlertBox.innerHTML = '';
            return;
        }

        const items = allergies.map(function (allergy) {
            const severity = allergy.severity ? allergy.severity.toUpperCase() : 'UNKNOWN';
            const reaction = allergy.reaction_type ? ` (${allergy.reaction_type})` : '';
            return `<li><strong>${allergy.allergen}</strong>${reaction} - <span class="severity-inline">${severity}</span></li>`;
        }).join('');

        allergyAlertBox.innerHTML = `
            <div class="allergy-alert-title"><i class="fas fa-exclamation-triangle"></i> Active Allergy Alert</div>
            <ul class="allergy-alert-list">${items}</ul>
        `;
        allergyAlertBox.style.display = 'block';
    }

    petSelect.addEventListener('change', function () {
        renderAllergyAlert(this.value);
    });

    renderAllergyAlert(petSelect.value);

    // Handle veterinarian dropdown update when date changes
    if (administeredDateInput && veterinarianSelect) {
        function updateVeterinarians(dateStr) {
            const currentValue = veterinarianSelect.value;
            
            fetch(`{{ route('admin.vaccinations.available-veterinarians') }}?date=${dateStr}`)
                .then(response => response.json())
                .then(data => {
                    const selectedVetId = '{{ old("administered_by", $selectedVeterinarianId ?? "") }}';
                    
                    // Clear all options except the first
                    veterinarianSelect.innerHTML = '<option value="">Select veterinarian...</option>';
                    
                    if (!data.veterinarians || data.veterinarians.length === 0) {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No veterinarians available for this date';
                        option.disabled = true;
                        veterinarianSelect.appendChild(option);
                        return;
                    }
                    
                    data.veterinarians.forEach(vet => {
                        const option = document.createElement('option');
                        option.value = vet.id;
                        option.textContent = vet.name;
                        if (selectedVetId && vet.id == selectedVetId) {
                            option.selected = true;
                        }
                        veterinarianSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching veterinarians:', error);
                });
        }

        administeredDateInput.addEventListener('change', function () {
            if (this.value) {
                updateVeterinarians(this.value);
            }
        });

        // Load initial veterinarians on page load
        if (administeredDateInput.value) {
            updateVeterinarians(administeredDateInput.value);
        }
    }
});
</script>

<style>
.form-container {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.form-header h2 {
    margin: 0;
    color: var(--primary-orange);
}

.form-section {
    margin-bottom: 30px;
}

.appointment-context-box {
    background: #FFF8E1;
    border: 1px solid #FFE082;
    color: #7A5200;
    padding: 12px 14px;
    border-radius: 8px;
    font-size: 13px;
}

.form-section h3 {
    color: var(--dark-text);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #FF6B9D;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.allergy-alert-box {
    margin-bottom: 18px;
    background: #FFF4E5;
    border: 1px solid #FFD8A8;
    border-left: 4px solid #F59E0B;
    border-radius: 8px;
    padding: 12px 14px;
}

.allergy-alert-title {
    font-weight: 700;
    color: #92400E;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.allergy-alert-list {
    margin: 0;
    padding-left: 20px;
    color: #7C2D12;
}

.severity-inline {
    font-weight: 700;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark-text);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.text-danger {
    color: #F44336;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-primary {
    background: var(--primary-orange);
    color: white;
}

.btn-primary:hover {
    background: #E85A2D;
}

.btn-secondary {
    background: #6C757D;
    color: white;
}

.btn-secondary:hover {
    background: #5A6268;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>
@endsection

