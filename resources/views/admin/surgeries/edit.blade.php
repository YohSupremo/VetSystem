@extends('admin.dashboard')

@section('page-title', 'Edit Surgery')
@section('page-description', 'Update surgery for ' . ($surgery->pet->name ?? 'pet'))

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-procedures"></i> Edit Surgery</h2>
            <a href="{{ route('admin.surgeries.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.surgeries.update', $surgery->id) }}" method="POST" class="form-main">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Pet & Surgeon Information</h3>
                
                <div class="form-group">
                    <label>Pet</label>
                    <input type="text" class="form-control" value="{{ $surgery->pet->name ?? 'N/A' }}" disabled>
                    <input type="hidden" name="pet_id" value="{{ $surgery->pet_id }}">
                </div>

                <div class="form-group">
                    <label>Surgeon <span class="text-danger">*</span></label>
                    <select name="surgeon_id" class="form-control" required>
                        <option value="">Choose surgeon...</option>
                        @forelse($surgeons as $surgeon)
                            <option value="{{ $surgeon->id }}" {{ $surgery->surgeon_id == $surgeon->id ? 'selected' : '' }}>
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
                    <input type="text" name="procedure_name" class="form-control" value="{{ $surgery->procedure_name }}" required>
                    @error('procedure_name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Scheduled Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="scheduled_date" class="form-control" value="{{ $surgery->scheduled_date ? $surgery->scheduled_date->format('Y-m-d\TH:i') : '' }}" required>
                        @error('scheduled_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Anesthesia Type</label>
                        <input type="text" name="anesthesia_type" class="form-control" value="{{ $surgery->anesthesia_type }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="scheduled" {{ $surgery->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ $surgery->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $surgery->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $surgery->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Pre-operative Notes</label>
                    <textarea name="pre_op_notes" class="form-control" rows="3">{{ $surgery->pre_op_notes }}</textarea>
                </div>

                <div class="form-group">
                    <label>Surgery Notes</label>
                    <textarea name="surgery_notes" class="form-control" rows="3">{{ $surgery->surgery_notes }}</textarea>
                </div>

                <div class="form-group">
                    <label>Post-operative Instructions</label>
                    <textarea name="post_op_instructions" class="form-control" rows="3">{{ $surgery->post_op_instructions }}</textarea>
                </div>

                <div class="form-group">
                    <label>Outcome</label>
                    <textarea name="outcome" class="form-control" rows="3">{{ $surgery->outcome }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Surgery
                </button>
                <a href="{{ route('admin.surgeries.show', $surgery->id) }}" class="btn btn-secondary">Cancel</a>
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
    border-bottom: 2px solid #9B7EDE;
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
    border-color: #9B7EDE;
    box-shadow: 0 0 0 3px rgba(155, 126, 222, 0.1);
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
