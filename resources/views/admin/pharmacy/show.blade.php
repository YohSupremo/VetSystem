@extends('admin.dashboard')

@push('styles')
<style>
    .show-container {
        max-width: 700px;
        margin: 2rem auto;
    }
    .detail-card {
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
    .detail-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
        margin-top: 1rem;
        margin-bottom: 0.15rem;
    }
    .detail-value {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }
    .btn {
        border-radius: 8px;
        padding: 0.6rem 1.4rem;
        border: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-primary { background: linear-gradient(135deg,#4e73df,#224abe); color:#fff; }
    .btn-secondary { background:#6c757d; color:#fff; }
    .btn-danger { background:#dc3545; color:#fff; }
</style>
@endpush

@section('content')
<div class="show-container">
    <div class="page-header">
        <h1><i class="fas fa-capsules"></i> Medication Details</h1>
        <p>View information about this medicine.</p>
    </div>

    <div class="detail-card">
        <div class="detail-label">Name</div>
        <div class="detail-value">{{ $medication->name }}</div>

        <div class="detail-label">SKU</div>
        <div class="detail-value">{{ $medication->sku ?? 'Not set' }}</div>

        <div class="detail-label">Unit Price</div>
        <div class="detail-value">
            {{ $medication->unit_price !== null ? number_format($medication->unit_price, 2) : 'Not set' }}
        </div>

        <div class="detail-label">Category</div>
        <div class="detail-value">{{ ucfirst($medication->category) }}</div>

        <div class="actions">
            <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.pharmacy.edit', $medication->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.pharmacy.destroy', $medication->id) }}" class="delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

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
@endsection

