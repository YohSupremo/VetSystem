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
    .page-header p {
        color: #6c757d;
        margin: 0 0 1.25rem;
    }
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
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1><i class="fas fa-vials"></i> Add Lab Test</h1>
        <p>Create a new lab test based on your database schema (`lab_tests`).</p>
    </div>

    <form method="POST" action="{{ route('admin.laboratory.tests.store') }}">
        @csrf

        <div class="form-group">
            <label for="test_name">Test Name</label>
            <input type="text" id="test_name" name="test_name" class="form-control" value="{{ old('test_name') }}" required>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="standard_price">Standard Price (₱) (optional)</label>
            <input type="number" id="standard_price" name="standard_price" class="form-control" min="0" step="0.01" value="{{ old('standard_price') }}" placeholder="e.g., 500.00">
        </div>

        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.laboratory.tests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </form>
</div>
@endsection

