@extends('admin.dashboard')

@push('styles')
<style>
    .form-container {
        max-width: 760px;
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
        color: #2c3e50;
    }
    .page-header p { color:#6c757d; margin:0 0 1.25rem; }
    .form-group { margin-bottom: 1.1rem; }
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
        color: #fff;
    }
    .btn-primary { background: linear-gradient(135deg,#1565c0,#0d47a1); }
    .btn-secondary { background: #6c757d; }
    .btn-danger { background:#dc3545; margin-left: auto; }
    .delete-form { display:inline; }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1><i class="fas fa-vials"></i> Edit Lab Test</h1>
        <p>Update the test details and pricing.</p>
    </div>

    <form method="POST" action="{{ route('admin.laboratory.tests.update', $labTest->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="test_name">Test Name</label>
            <input type="text" id="test_name" name="test_name" class="form-control" value="{{ old('test_name', $labTest->test_name) }}" required>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" @selected(old('category', $labTest->category) === $cat)>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="standard_price">Standard Price (₱) (optional)</label>
            <input type="number" id="standard_price" name="standard_price" class="form-control" min="0" step="0.01" value="{{ old('standard_price', $labTest->standard_price) }}">
        </div>

        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="4" class="form-control">{{ old('description', $labTest->description) }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.laboratory.tests.show', $labTest->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
        </div>
    </form>

    <div class="form-actions" style="margin-top: .75rem;">
        <form method="POST" action="{{ route('admin.laboratory.tests.destroy', $labTest->id) }}" class="delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
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

