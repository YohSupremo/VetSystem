@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="mb-4">
        <h1 class="h2 mb-1">Report an Incident</h1>
        <p class="text-muted">Provide details so our team can respond quickly.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('customer.incidents.store') }}" class="bg-white p-4 rounded-3 shadow-sm">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Pet</label>
                <select name="pet_id" class="form-select" required>
                    <option value="">Select a pet</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Incident Date & Time</label>
                <input type="datetime-local" name="incident_date" class="form-control" value="{{ old('incident_date') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Incident Type</label>
                <select name="incident_type" class="form-select" required>
                    <option value="">Select type</option>
                    @foreach($incidentTypes as $value => $label)
                        <option value="{{ $value }}" {{ old('incident_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Severity</label>
                <select name="severity" class="form-select" required>
                    <option value="">Select severity</option>
                    @foreach($severityOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('severity') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Where did this occur?" required>
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Immediate Action Taken (optional)</label>
                <textarea name="immediate_action_taken" class="form-control" rows="3">{{ old('immediate_action_taken') }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('customer.incidents.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Submit Report
            </button>
        </div>
    </form>
</div>
@endsection
