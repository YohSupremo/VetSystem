@extends('admin.dashboard')

@section('page-title', 'Add to Queue')
@section('page-description', 'Create a new queued appointment')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Create Queued Appointment</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.queue.store') }}">
            @csrf

            <div class="form-group">
                <label for="pet_id">Pet</label>
                <select name="pet_id" id="pet_id" class="form-control" required>
                    <option value="">Select a pet</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }} @if($pet->owner && $pet->owner->user) - {{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="veterinarian_id">Veterinarian</label>
                <select name="veterinarian_id" id="veterinarian_id" class="form-control" required>
                    <option value="">Select veterinarian</option>
                    @foreach($veterinarians as $vet)
                        <option value="{{ $vet->id }}" {{ old('veterinarian_id') == $vet->id ? 'selected' : '' }}>
                            {{ $vet->first_name }} {{ $vet->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="appointment_date">Date</label>
                    <input type="date" name="appointment_date" id="appointment_date" class="form-control"
                           value="{{ old('appointment_date', $today) }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                           value="{{ old('start_time') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control"
                           value="{{ old('end_time') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="type">Appointment Type</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="">Select type</option>
                    @foreach(['checkup','vaccination','surgery','dental','grooming','other'] as $type)
                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="reason">Reason</label>
                <textarea name="reason" id="reason" rows="3" class="form-control">{{ old('reason') }}</textarea>
            </div>

            <div class="form-group">
                <label for="notes">Internal Notes</label>
                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.queue.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Queue
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add to Queue
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

