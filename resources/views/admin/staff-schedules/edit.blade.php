@extends('admin.dashboard')

@section('page-title', 'Edit Staff Schedule')
@section('page-description', 'Manage work schedule for ' . $user->first_name . ' ' . $user->last_name)

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-calendar-edit"></i> Edit Schedule</h1>
            <p class="text-muted mb-0">{{ $user->first_name }} {{ $user->last_name }} - {{ ucfirst($user->role) }}</p>
        </div>
        <a href="{{ route('admin.staff-schedules.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Select Work Days and Shifts</h5>
        <small class="text-muted">Morning: 9:00 AM - 5:00 PM | Night: 5:00 PM - 12:00 AM</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.staff-schedules.update', $user->id) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                @foreach($daysOfWeek as $day)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">{{ $day }}</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="schedules[]" 
                                           value="{{ $day }}_morning" 
                                           id="{{ $day }}_morning"
                                           {{ $existingSchedules->has($day . '_morning') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $day }}_morning">
                                        <span class="badge bg-warning">Morning</span> 9:00 AM - 5:00 PM
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="schedules[]" 
                                           value="{{ $day }}_night" 
                                           id="{{ $day }}_night"
                                           {{ $existingSchedules->has($day . '_night') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $day }}_night">
                                        <span class="badge bg-primary">Night</span> 5:00 PM - 12:00 AM
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.staff-schedules.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
