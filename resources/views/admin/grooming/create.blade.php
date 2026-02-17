@extends('admin.dashboard')

@section('page-title', 'New Grooming Appointment')
@section('page-description', 'Schedule a new grooming appointment')

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
        background: linear-gradient(135deg,#9c27b0,#6a1b9a);
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
        <h1><i class="fas fa-cut"></i> New Grooming Appointment</h1>
        <p>Create a grooming visit for a pet.</p>
    </div>

    <form method="POST" action="{{ route('admin.grooming.store') }}">
        @csrf

        <div class="form-group">
            <label for="pet_id">Pet</label>
            <select id="pet_id" name="pet_id" class="form-control" required>
                <option value="">Select a pet</option>
                @foreach($pets as $pet)
                    @php $ownerUser = optional($pet->owner)->user; @endphp
                    <option value="{{ $pet->id }}" @selected(old('pet_id') == $pet->id)>
                        {{ $pet->name }} @if($ownerUser) ({{ $ownerUser->first_name }} {{ $ownerUser->last_name }}) @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="service_id">Grooming Service</label>
            <select id="service_id" name="service_id" class="form-control" required>
                <option value="">Select a service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>
                        {{ $service->service_name }} 
                        @if($service->price) - ₱{{ number_format($service->price, 2) }} @endif
                        @if($service->duration_minutes) ({{ $service->duration_minutes }} min) @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="groomer_id">Assign Groomer (optional)</label>
            <select id="groomer_id" name="groomer_id" class="form-control">
                <option value="">No groomer assigned</option>
                @foreach($groomers as $groomer)
                    <option value="{{ $groomer->id }}" @selected(old('groomer_id') == $groomer->id)>
                        {{ $groomer->first_name }} {{ $groomer->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="appointment_date">Date</label>
            <input type="date" id="appointment_date" name="appointment_date" class="form-control"
                   value="{{ old('appointment_date') }}" required>
        </div>

        <div class="form-group">
            <label for="special_instructions">Special Instructions (optional)</label>
            <textarea id="special_instructions" name="special_instructions" rows="3" class="form-control">{{ old('special_instructions') }}</textarea>
        </div>

        <div class="form-group">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.grooming.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Grooming
            </button>
        </div>
    </form>
</div>
@endsection

