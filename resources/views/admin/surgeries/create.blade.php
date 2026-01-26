@extends('admin.dashboard')

@section('page-title', 'Create Surgery')
@section('page-description', 'Schedule new surgery for pet')

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-procedures"></i> New Surgery</h2>
            <a href="{{ route('admin.surgeries.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.surgeries.store') }}" method="POST" class="form-main">
            @csrf

            <div class="form-section">
                <h3>Pet & Surgeon Information</h3>
                
                <div class="form-group">
                    <label>Select Pet <span class="text-danger">*</span></label>
                    <select name="pet_id" class="form-control" required>
                        <option value="">Choose a pet...</option>
                        @forelse($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                {{ $pet->name }} - {{ $pet->owner->user->first_name ?? 'Unknown' }}
                            </option>
                        @empty
                            <option value="">No pets available</option>
                        @endforelse
                    </select>
                    @error('pet_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Medical Record (Optional)</label>
                    <select name="medical_record_id" class="form-control">
                        <option value="">Select medical record...</option>
                        @forelse($medicalRecords as $record)
                            <option value="{{ $record->id }}" {{ old('medical_record_id') == $record->id ? 'selected' : '' }}>
                                {{ $record->pet->name }} - {{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}
                            </option>
                        @empty
                            <option value="">No medical records available</option>
                        @endforelse
                    </select>
                </div>

                <div class="form-group">
                    <label>Surgeon <span class="text-danger">*</span></label>
                    <select name="surgeon_id" class="form-control" required>
                        <option value="">Choose surgeon...</option>
                        @forelse($surgeons as $surgeon)
                            <option value="{{ $surgeon->id }}" {{ old('surgeon_id') == $surgeon->id ? 'selected' : '' }}>
                                Dr. {{ $surgeon->first_name }} {{ $surgeon->last_name }}
                            </option>
                        @empty
                            <option value="">No surgeons available</option>
                        @endforelse
                    </select>
                    @error('surgeon_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Surgery Details</h3>
                
                <div class="form-group">
                    <label>Procedure Name <span class="text-danger">*</span></label>
                    <input type="text" name="procedure_name" class="form-control" value="{{ old('procedure_name') }}" placeholder="e.g., Spay/Neuter, Orthopedic Surgery" required>
                    @error('procedure_name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Scheduled Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}" required>
                        @error('scheduled_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Anesthesia Type</label>
                        <input type="text" name="anesthesia_type" class="form-control" value="{{ old('anesthesia_type') }}" placeholder="e.g., General, Local">
                    </div>
                </div>

                <div class="form-group">
                    <label>Pre-operative Notes</label>
                    <textarea name="pre_op_notes" class="form-control" rows="3" placeholder="Pre-op instructions, blood work results, etc.">{{ old('pre_op_notes') }}</textarea>
                </div>

                

                <div class="form-group">
                    <label>Post-operative Instructions</label>
                    <textarea name="post_op_instructions" class="form-control" rows="3" placeholder="Recovery instructions, medication, activity restrictions, etc.">{{ old('post_op_instructions') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Surgery
                </button>
                <a href="{{ route('admin.surgeries.index') }}" class="btn btn-secondary">Cancel</a>
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
    border-bottom: 2px solid var(--accent-pink);
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
