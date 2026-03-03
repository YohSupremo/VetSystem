@extends('admin.dashboard')

@section('page-title', 'Edit Incident')
@section('page-description', 'Update incident ' . $incident->incident_number)

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Edit Incident</h2>
            <a href="{{ route('admin.incidents.show', $incident->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.incidents.update', $incident->id) }}" method="POST" class="form-main">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Basic Information</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Incident Number</label>
                        <input type="text" class="form-control" value="{{ $incident->incident_number }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Incident Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="incident_date" class="form-control" value="{{ old('incident_date', optional($incident->incident_date)->format('Y-m-d\\TH:i')) }}" required>
                        @error('incident_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Type <span class="text-danger">*</span></label>
                        <select name="incident_type" class="form-control" required>
                            @foreach($incidentTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('incident_type', $incident->incident_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('incident_type')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Severity <span class="text-danger">*</span></label>
                        <select name="severity" class="form-control" required>
                            @foreach($severityOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('severity', $incident->severity) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('severity')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            @foreach($statusOptions as $status)
                                <option value="{{ $status }}" {{ old('status', $incident->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Filed By (Reported By) <span class="text-muted small">(Read-only)</span></label>
                        <input type="text" class="form-control" 
                               value="{{ $incident->reportedBy ? trim(($incident->reportedBy->first_name ?? '') . ' ' . ($incident->reportedBy->last_name ?? '')) . ' (' . ucfirst($incident->reportedBy->role) . ')' : 'N/A' }}" 
                               disabled>
                        <input type="hidden" name="reported_by" value="{{ $incident->reported_by }}">
                        <small class="text-muted">Original reporter of this incident.</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Related Records</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pet</label>
                        <select name="pet_id" class="form-control">
                            <option value="">None</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id', $incident->pet_id) == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }}{{ $pet->owner && $pet->owner->user ? ' - ' . trim(($pet->owner->user->first_name ?? '') . ' ' . ($pet->owner->user->last_name ?? '')) : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('pet_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Affected User <span class="text-muted small">(Read-only - The Filer)</span></label>
                        <input type="text" class="form-control" 
                               value="{{ $incident->reportedBy ? trim(($incident->reportedBy->first_name ?? '') . ' ' . ($incident->reportedBy->last_name ?? '')) . ' (' . ucfirst($incident->reportedBy->role) . ')' : 'N/A' }}" 
                               disabled>
                        <input type="hidden" name="affected_user_id" value="{{ $incident->affected_user_id }}">
                        <small class="text-muted">The affected user is the person who filed this incident and cannot be changed.</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Cage</label>
                        <select name="cage_id" class="form-control">
                            <option value="">None</option>
                            @foreach($cages as $cage)
                                <option value="{{ $cage->id }}" {{ old('cage_id', $incident->cage_id) == $cage->id ? 'selected' : '' }}>
                                    {{ $cage->cage_code }}{{ $cage->location ? ' - ' . $cage->location : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('cage_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Location <span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-control" value="{{ old('location', $incident->location) }}" maxlength="150" required>
                        @error('location')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Incident Details</h3>

                <div class="form-group">
                    <label>Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description', $incident->description) }}</textarea>
                    @error('description')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Immediate Action Taken</label>
                    <textarea name="immediate_action_taken" class="form-control" rows="3">{{ old('immediate_action_taken', $incident->immediate_action_taken) }}</textarea>
                    @error('immediate_action_taken')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Root Cause</label>
                    <textarea name="root_cause" class="form-control" rows="3">{{ old('root_cause', $incident->root_cause) }}</textarea>
                    @error('root_cause')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Corrective Action</label>
                    <textarea name="corrective_action" class="form-control" rows="3">{{ old('corrective_action', $incident->corrective_action) }}</textarea>
                    @error('corrective_action')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Incident
                </button>
                <a href="{{ route('admin.incidents.show', $incident->id) }}" class="btn btn-secondary">Cancel</a>
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
