@extends('admin.dashboard')

@push('styles')
<style>
    .show-container { max-width: 760px; margin: 2rem auto; }
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
        color: #2c3e50;
    }
    .page-header p { color:#6c757d; margin:0 0 1.25rem; }
    .detail-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
        margin-top: 1rem;
        margin-bottom: 0.15rem;
    }
    .detail-value { font-size: 1rem; font-weight: 600; color: #2c3e50; }
    .actions { display:flex; gap:.75rem; margin-top: 1.5rem; flex-wrap: wrap; }
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
        color: #fff;
    }
    .btn-primary { background: linear-gradient(135deg,#1565c0,#0d47a1); }
    .btn-secondary { background:#6c757d; }
    .btn-danger { background:#dc3545; }
</style>
@endpush

@section('content')
<div class="show-container">
    <div class="page-header">
        <h1><i class="fas fa-vials"></i> Lab Test Details</h1>
        <p>View information for this lab test.</p>
    </div>

    <div class="detail-card">
        <div class="detail-label">Test Name</div>
        <div class="detail-value">{{ $labTest->test_name }}</div>

        <div class="detail-label">Category</div>
        <div class="detail-value">{{ ucfirst($labTest->category) }}</div>

        <div class="detail-label">Standard Price</div>
        <div class="detail-value">
            {{ $labTest->standard_price !== null ? '₱' . number_format($labTest->standard_price, 2) : 'Not set' }}
        </div>

        <div class="detail-label">Description</div>
        <div class="detail-value">{{ $labTest->description ?: 'No description provided.' }}</div>

        <div class="detail-label">Requisitions</div>
        <div class="detail-value">{{ $labTest->lab_requisitions_count ?? 0 }}</div>

        <div class="actions">
            <a href="{{ route('admin.laboratory.tests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.laboratory.tests.edit', $labTest->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.laboratory.tests.destroy', $labTest->id) }}" class="delete-form">
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
            if (!confirm('Delete this lab test?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush
@endsection

