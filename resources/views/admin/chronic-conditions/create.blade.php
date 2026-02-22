@extends('admin.dashboard')

@section('page-title', 'Create Chronic Condition')
@section('page-description', 'Add a chronic condition record')

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-plus-circle"></i> Add Chronic Condition</h2>
            <a href="{{ route('admin.chronic-conditions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.chronic-conditions.store') }}" method="POST" class="form-main">
            @csrf

            <div class="form-group">
                <label>Pet <span class="text-danger">*</span></label>
                <select name="pet_id" class="form-control">
                    <option value="">Choose a pet...</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ old('pet_id', $selectedPetId) == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }} - {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('pet_id')<span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Condition Name <span class="text-danger">*</span></label>
                <input type="text" name="condition_name" class="form-control" value="{{ old('condition_name') }}" placeholder="e.g. Diabetes Mellitus">
                @error('condition_name')<span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Diagnosed Date</label>
                <input type="date" name="diagnosed_date" class="form-control" value="{{ old('diagnosed_date') }}">
                @error('diagnosed_date')<span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Ongoing Treatment</label>
                <textarea name="ongoing_treatment" class="form-control" rows="4" placeholder="Current treatment details...">{{ old('ongoing_treatment') }}</textarea>
                @error('ongoing_treatment')<span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="4" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="toggle-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <span>Active Condition</span>
                </label>
                @error('is_active')<span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Condition
                </button>
                <a href="{{ route('admin.chronic-conditions.index') }}" class="btn btn-secondary">Cancel</a>
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

.toggle-label {
    display: inline-flex !important;
    align-items: center;
    gap: 10px;
    margin-bottom: 0 !important;
    cursor: pointer;
}

.form-group input[type="checkbox"] {
    width: auto;
    margin: 0;
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
</style>
@endsection
