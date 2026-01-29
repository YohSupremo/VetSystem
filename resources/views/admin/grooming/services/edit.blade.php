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
    .btn-primary { background: linear-gradient(135deg,#9c27b0,#6a1b9a); color:#fff; }
    .btn-secondary { background:#6c757d; color:#fff; }
    .btn-danger { background:#dc3545; color:#fff; margin-left:auto; }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1><i class="fas fa-spa"></i> Edit Grooming Service</h1>
        <p>Update the service information.</p>
    </div>

    <form method="POST" action="{{ route('admin.grooming-services.update', $service->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="service_name">Service Name</label>
            <input type="text" id="service_name" name="service_name" class="form-control" value="{{ old('service_name', $service->service_name) }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $service->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="duration_minutes">Duration (minutes)</label>
            <input type="number" id="duration_minutes" name="duration_minutes" class="form-control" min="1" value="{{ old('duration_minutes', $service->duration_minutes) }}" placeholder="e.g., 60">
        </div>

        <div class="form-group">
            <label for="price">Price (₱)</label>
            <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" value="{{ old('price', $service->price) }}" required>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.grooming-services.show', $service->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <form method="POST" action="{{ route('admin.grooming-services.destroy', $service->id) }}" class="delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.delete-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var ok = confirm('Are you sure you want to delete this service?');
            if (!ok) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush
@endsection
