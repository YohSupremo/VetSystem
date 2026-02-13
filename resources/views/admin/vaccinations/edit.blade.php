@extends('admin.dashboard')

@section('page-title', 'Edit Vaccination')
@section('page-description', 'Update vaccination for ' . ($vaccination->pet->name ?? 'pet'))

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-syringe"></i> Edit Vaccination</h2>
            <a href="{{ route('admin.vaccinations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.vaccinations.update', $vaccination->id) }}" method="POST" class="form-main">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Pet Information</h3>
                
                <div class="form-group">
                    <label>Pet</label>
                    <input type="text" class="form-control" value="{{ $vaccination->pet->name ?? 'N/A' }}" disabled>
                    <input type="hidden" name="pet_id" value="{{ $vaccination->pet_id }}">
                </div>
            </div>

            <div class="form-section">
                <h3>Vaccination Details</h3>
                
                <div class="form-group">
                    <label>Vaccine Name <span class="text-danger">*</span></label>
                    <input type="text" name="vaccine_name" class="form-control" value="{{ $vaccination->vaccine_name }}" required>
                    @error('vaccine_name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Vaccination Date <span class="text-danger">*</span></label>
                        <input type="date" name="administered_date" class="form-control" value="{{ $vaccination->administered_date ? $vaccination->administered_date->format('Y-m-d') : '' }}" required>
                        @error('administered_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Next Due Date</label>
                        <input type="date" name="next_due_date" class="form-control" value="{{ $vaccination->next_due_date ? $vaccination->next_due_date->format('Y-m-d') : '' }}">
                        @error('next_due_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Veterinarian</label>
                    <select name="veterinarian_id" class="form-control">
                        <option value="">Select veterinarian...</option>
                        @forelse($veterinarians as $vet)
                            <option value="{{ $vet->id }}" {{ $vaccination->veterinarian_id == $vet->id ? 'selected' : '' }}>
                                Dr. {{ $vet->first_name }} {{ $vet->last_name }}
                            </option>
                        @empty
                            <option value="">No veterinarians available</option>
                        @endforelse
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number" class="form-control" value="{{ $vaccination->batch_number }}">
                    </div>
                    <div class="form-group">
                        <label>Route of Administration</label>
                        <select name="route_of_administration" class="form-control">
                            <option value="">Select route...</option>
                            <option value="intramuscular" {{ $vaccination->route_of_administration === 'intramuscular' ? 'selected' : '' }}>Intramuscular (IM)</option>
                            <option value="subcutaneous" {{ $vaccination->route_of_administration === 'subcutaneous' ? 'selected' : '' }}>Subcutaneous (SC)</option>
                            <option value="intranasal" {{ $vaccination->route_of_administration === 'intranasal' ? 'selected' : '' }}>Intranasal</option>
                            <option value="oral" {{ $vaccination->route_of_administration === 'oral' ? 'selected' : '' }}>Oral</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Site of Injection</label>
                    <input type="text" name="site_of_injection" class="form-control" value="{{ $vaccination->site_of_injection }}">
                </div>

                <div class="form-group">
                    <label>Adverse Reactions/Effects</label>
                    <textarea name="adverse_reactions" class="form-control" rows="3">{{ $vaccination->adverse_reactions }}</textarea>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $vaccination->notes }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Vaccination
                </button>
                <a href="{{ route('admin.vaccinations.show', $vaccination->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

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

.form-group input:disabled {
    background: #f5f5f5;
    cursor: not-allowed;
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
