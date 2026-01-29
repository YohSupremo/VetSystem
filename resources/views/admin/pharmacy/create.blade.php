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
    .btn-primary {
        background: linear-gradient(135deg,#4e73df,#224abe);
        color: #fff;
    }
    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1><i class="fas fa-capsules"></i> Add Medication</h1>
        <p>Create a new medicine record.</p>
    </div>

    <form method="POST" action="{{ route('admin.pharmacy.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Medication Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="sku">SKU (optional)</label>
            <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku') }}">
        </div>

        <div class="form-group">
            <label for="unit_price">Unit Price</label>
            <input type="number" id="unit_price" name="unit_price" class="form-control" min="0" step="0.01" value="{{ old('unit_price') }}" required>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Medication
            </button>
        </div>
    </form>
</div>
@endsection

