@extends('admin.dashboard')

@section('page-title', 'Edit Queue Entry')
@section('page-description', 'Update status or notes for this queued appointment')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Queued Appointment #{{ $appointment->id }}</h3>
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

        <form method="POST" action="{{ route('admin.queue.status.update', $appointment) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    @foreach(['scheduled','in_progress','completed','cancelled','no_show'] as $status)
                        <option value="{{ $status }}" {{ old('status', $appointment->status) === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="notes">Internal Notes</label>
                <textarea name="notes" id="notes" rows="4" class="form-control">{{ old('notes', $appointment->notes) }}</textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.queue.show', $appointment) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Details
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

