@extends('admin.dashboard')

@push('styles')
<style>
    .form-container {
        max-width: 700px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.75rem;
    }
    .page-header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
    }
    .page-header p {
        color: #6c757d;
        margin: 0;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.35rem;
    }
    .form-control {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d1d3e2;
        padding: 0.6rem 0.8rem;
    }
    .form-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }
    .btn {
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.4rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-primary { background: linear-gradient(135deg,#4e73df,#224abe); color:#fff; }
    .btn-secondary { background:#6c757d; color:#fff; }
    .btn-danger { background:#dc3545; color:#fff; margin-left:auto; }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1><i class="fas fa-capsules"></i> Edit Medication</h1>
        <p>Update information for this medicine.</p>
    </div>

    <form method="POST" action="{{ route('admin.pharmacy.update', $medication->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Medication Name *</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $medication->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $medication->description) }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="sku">SKU (optional)</label>
            <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku', $medication->sku) }}">
            @error('sku')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="unit_price">Unit Price *</label>
            <input type="number" id="unit_price" name="unit_price" class="form-control" min="0" step="0.01" value="{{ old('unit_price', $medication->unit_price) }}" required>
            @error('unit_price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantity">Current Quantity *</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="0" value="{{ old('quantity', $medication->inventoryStocks->first()->quantity ?? 0) }}" required>
            @error('quantity')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="min_stock">Minimum Stock Level *</label>
            <input type="number" id="min_stock" name="min_stock" class="form-control" min="0" value="{{ old('min_stock', $medication->inventoryStocks->first()->min_stock ?? 10) }}" required>
            @error('min_stock')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="expiry_date">Expiry Date</label>
            <input type="date" id="expiry_date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $medication->inventoryStocks->first()->expiry_date ? $medication->inventoryStocks->first()->expiry_date->format('Y-m-d') : '') }}">
            @error('expiry_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="location">Storage Location</label>
            <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $medication->inventoryStocks->first()->location ?? '') }}" placeholder="e.g., Pharmacy Cabinet A">
            @error('location')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">Medication Image</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Allowed formats: JPG, PNG, GIF. Maximum size: 2MB.</small>
            @if($medication->image_path)
                <div class="mt-2">
                    <img src="{{ asset($medication->image_path) }}" alt="{{ $medication->name }}" style="max-width: 200px; border-radius: 8px; border: 2px solid #e9ecef;">
                    <br><small class="text-muted">Current image</small>
                </div>
            @endif
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.pharmacy.show', $medication->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
        </div>
    </form>
</div>

<!-- Delete Form - Separate from Update Form -->
<form method="POST" action="{{ route('admin.pharmacy.destroy', $medication->id) }}" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this medication?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">
        <i class="fas fa-trash"></i> Delete
    </button>
</form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.delete-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var ok = confirm('Are you sure you want to delete this medication?');
            if (!ok) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush

@section('content')
@endsection

