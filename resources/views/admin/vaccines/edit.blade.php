@extends('admin.dashboard')

@section('page-title', 'Edit Vaccine')
@section('page-description', 'Edit vaccine information')

@section('content')
<div class="form-container">
    <div class="form-header">
        <h2><i class="fas fa-syringe"></i> Edit Vaccine</h2>
        <a href="{{ route('admin.vaccines.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.vaccines.update', $vaccine->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Vaccine Name <span class="text-danger">*</span></label>
            <input type="text" name="vaccine_name" class="form-control" value="{{ old('vaccine_name', $vaccine->vaccine_name) }}" required>
            @error('vaccine_name')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Manufacturer</label>
            <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer', $vaccine->manufacturer) }}">
            @error('manufacturer')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $vaccine->description) }}</textarea>
            @error('description')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label>Status</label>
            <div>
                <input type="hidden" name="is_active" value="0">
                <label style="font-weight: normal;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $vaccine->is_active) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Vaccine
            </button>
            <a href="{{ route('admin.vaccines.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
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

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.text-danger {
    color: #F44336;
    font-size: 13px;
    margin-top: 5px;
    display: block;
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
</style>
@endsection
