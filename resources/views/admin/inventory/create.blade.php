@extends('admin.dashboard')

@section('page-title', 'Add Inventory Item')
@section('page-description', 'Create a new inventory item')

@push('styles')
<style>
    .inventory-page-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .form-section {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        padding: 1.75rem;
        margin-bottom: 2rem;
        position: relative;
    }

    .form-section.basic {
        border-top: 4px solid #6366f1;
    }

    .form-section.stock {
        border-top: 4px solid #22c55e;
    }

    .section-title {
        color: #0f172a;
        font-weight: 600;
        margin: 0;
    }

    .section-subtitle {
        margin: 0.35rem 0 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-section-header .icon-pill {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #eef2ff;
        color: #4338ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.65rem 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15);
    }

    .input-group-text {
        border-radius: 12px 0 0 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endpush

@section('content')
<div class="content-header inventory-page-hero">
    <div class="header-title">
        <h1><i class="fas fa-plus"></i> Add Inventory Item</h1>
        <p>Create a new item in the inventory system</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

<form action="{{ route('admin.inventory.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Basic Information -->
    <div class="form-section basic">
        <div class="form-section-header">
            <div>
                <h3 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h3>
                <p class="section-subtitle">Essential details to identify and classify the item.</p>
            </div>
            <div class="icon-pill"><i class="fas fa-tag"></i></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label">Item Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required>
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
                        <option value="medicine" {{ old('category') == 'medicine' ? 'selected' : '' }}>Medicine</option>
                        <option value="vaccine" {{ old('category') == 'vaccine' ? 'selected' : '' }}>Vaccine</option>
                        <option value="supply" {{ old('category') == 'supply' ? 'selected' : '' }}>Supply</option>
                        <option value="food" {{ old('category') == 'food' ? 'selected' : '' }}>Food</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
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
                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                           id="sku" name="sku" value="{{ old('sku') }}">
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
                               id="unit_price" name="unit_price" value="{{ old('unit_price') }}"
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
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
    <div class="form-section stock">
        <div class="form-section-header">
            <div>
                <h3 class="section-title"><i class="fas fa-warehouse"></i> Stock Information</h3>
                <p class="section-subtitle">Track availability and expiry details.</p>
            </div>
            <div class="icon-pill" style="background:#ecfdf3;color:#15803d;"><i class="fas fa-box"></i></div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity *</label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                           id="quantity" name="quantity" value="{{ old('quantity', 0) }}"
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
                           id="min_stock" name="min_stock" value="{{ old('min_stock', 0) }}"
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
                           id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
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

     <div class="form-section image-upload-section">
        <div class="form-section-header">
            <div>
                <h3 class="section-title"><i class="fas fa-image"></i> Product Image</h3>
                <p class="section-subtitle">Upload an image for inventory item.</p>
            </div>
        </div>
        <div class="form-group">
            <label for="image" class="form-label">Image File</label>
            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB.</small>
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <!-- Form Actions -->
    <div class="form-section">
        <div class="form-actions">
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Inventory Item
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