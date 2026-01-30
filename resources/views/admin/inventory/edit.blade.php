@extends('admin.dashboard')

@section('page-title', 'Edit Inventory Item')
@section('page-description', 'Update inventory item information')

@push('styles')
<style>
    .form-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .section-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-edit"></i> Edit Inventory Item</h1>
        <p>Update information for {{ $item->name }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.inventory.show', $item->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-eye"></i> View Item
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary ms-2">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

<form action="{{ route('admin.inventory.update', $item->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Basic Information -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h3>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label">Item Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name', $item->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="category" class="form-label">Category *</label>
                    <select class="form-select @error('category') is-invalid @enderror"
                            id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="medicine" {{ old('category', $item->category) == 'medicine' ? 'selected' : '' }}>Medicine</option>
                        <option value="vaccine" {{ old('category', $item->category) == 'vaccine' ? 'selected' : '' }}>Vaccine</option>
                        <option value="supply" {{ old('category', $item->category) == 'supply' ? 'selected' : '' }}>Supply</option>
                        <option value="food" {{ old('category', $item->category) == 'food' ? 'selected' : '' }}>Food</option>
                        <option value="other" {{ old('category', $item->category) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                           id="sku" name="sku" value="{{ old('sku', $item->sku) }}">
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="unit_price" class="form-label">Unit Price *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control @error('unit_price') is-invalid @enderror"
                               id="unit_price" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}"
                               step="0.01" min="0" required>
                    </div>
                    @error('unit_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select class="form-select @error('supplier_id') is-invalid @enderror"
                            id="supplier_id" name="supplier_id">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $item->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Information -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-warehouse"></i> Stock Information</h3>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity *</label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                           id="quantity" name="quantity" value="{{ old('quantity', $item->quantity) }}"
                           min="0" required>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="min_stock" class="form-label">Minimum Stock *</label>
                    <input type="number" class="form-control @error('min_stock') is-invalid @enderror"
                           id="min_stock" name="min_stock" value="{{ old('min_stock', $item->min_stock) }}"
                           min="0" required>
                    @error('min_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="expiry_date" class="form-label">Expiry Date</label>
                    <input type="date" class="form-control @error('expiry_date') is-invalid @enderror"
                           id="expiry_date" name="expiry_date" 
                           value="{{ old('expiry_date', $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '') }}">
                    <small class="form-text text-muted" id="expiry_date_help">
                        Only applicable for Medicine, Vaccine, and Food
                    </small>
                    @error('expiry_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-section">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Inventory Item
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const expiryDateInput = document.getElementById('expiry_date');
    const expiryDateHelp = document.getElementById('expiry_date_help');
    
    function toggleExpiryDate() {
        const category = categorySelect.value;
        const requiresExpiry = ['medicine', 'vaccine', 'food'].includes(category);
        
        if (requiresExpiry) {
            expiryDateInput.removeAttribute('disabled');
            expiryDateHelp.textContent = 'Required for Medicine, Vaccine, and Food';
        } else {
            expiryDateInput.setAttribute('disabled', 'disabled');
            expiryDateInput.value = '';
            expiryDateHelp.textContent = 'Not applicable for this category';
        }
    }
    
    categorySelect.addEventListener('change', toggleExpiryDate);
    toggleExpiryDate(); // Run on page load
});
</script>
@endpush
