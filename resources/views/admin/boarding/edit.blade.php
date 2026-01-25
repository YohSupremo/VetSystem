@extends('admin.dashboard')

@push('styles')
<style>
    .edit-container {
        max-width: 900px;
        margin: 2rem auto;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header p {
        color: #6c757d;
        margin: 0;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #4e73df;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title i {
        color: #4e73df;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-group label .required {
        color: #dc3545;
        margin-left: 0.25rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        outline: none;
    }

    .form-group input:disabled,
    .form-group select:disabled,
    .form-group textarea:disabled {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .form-row .form-group {
        margin-bottom: 0;
    }

    .form-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.35rem;
        display: block;
    }

    .pet-preview {
        background: #f8f9fa;
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 0.75rem;
        display: block;
    }

    .pet-preview-item {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .pet-preview-image {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        background: white;
    }

    .pet-preview-info {
        flex: 1;
    }

    .pet-preview-info h4 {
        margin: 0 0 0.5rem;
        color: #2c3e50;
        font-size: 1rem;
    }

    .pet-preview-info p {
        margin: 0.25rem 0;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e9ecef;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.75rem;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        color: white;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
        margin-left: auto;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        color: white;
    }

    .breadcrumb {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .breadcrumb a {
        color: #4e73df;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb a:hover {
        color: #224abe;
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: #6c757d;
    }

    .breadcrumb-current {
        color: #6c757d;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-danger {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .alert-danger li {
        margin: 0.25rem 0;
    }

    .cage-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 0.75rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .cage-stat {
        padding: 0.75rem;
        background: white;
        border-radius: 6px;
        border-left: 3px solid #4e73df;
    }

    .cage-stat label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        margin: 0;
    }

    .cage-stat value {
        display: block;
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-top: 0.25rem;
    }

    .delete-form {
        display: inline;
    }

    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-completed {
        background: #e2e3e5;
        color: #383d41;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

@section('content')
<div class="edit-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.boarding.index') }}"><i class="fas fa-home"></i> Boardings</a>
        <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
        <span class="breadcrumb-current">Edit Boarding #{{ $boarding->id ?? 'N/A' }}</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-edit"></i> Edit Boarding Record
        </h1>
        <p>Update the boarding details for this pet</p>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('admin.boarding.update', $boarding->id ?? 0) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Pet Information Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-paw"></i> Pet Information
                </div>

                <div class="form-group">
                    <label for="pet_id">
                        Pet
                    </label>
                    <input type="text" class="form-control" disabled value="{{ $boarding->petAssigned->name ?? 'N/A' }}">
                    <span class="form-hint">Pet information cannot be changed. Create a new boarding for a different pet.</span>
                </div>

                <!-- Pet Preview -->
                <div class="pet-preview">
                    <div class="pet-preview-item">
                        <img src="{{ $boarding->petAssigned->photo_path ?? asset('images/default-pet.jpg') }}" alt="Pet" class="pet-preview-image">
                        <div class="pet-preview-info">
                            <h4>{{ $boarding->petAssigned->name ?? 'N/A' }}</h4>
                            <p><strong>Breed:</strong> {{ $boarding->petAssigned->breed ?? 'N/A' }}</p>
                            <p><strong>Owner:</strong> {{ $boarding->petAssigned->owner->user->first_name ?? 'N/A' }} {{ $boarding->petAssigned->owner->user->last_name ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boarding Dates Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-calendar-alt"></i> Boarding Dates
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">
                            Check-in Date
                            <span class="required">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" required 
                               value="{{ old('start_date', $boarding->start_date ?? '') }}" 
                               class="@error('start_date') is-invalid @enderror">
                        @error('start_date')
                            <span class="form-hint" style="color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_date">
                            Check-out Date
                            <span class="required">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" required 
                               value="{{ old('end_date', $boarding->end_date ?? '') }}" 
                               class="@error('end_date') is-invalid @enderror">
                        @error('end_date')
                            <span class="form-hint" style="color: #dc3545;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="check_in_time">Check-in Time</label>
                        <input type="time" name="check_in_time" id="check_in_time" 
                               value="{{ old('check_in_time', '09:00') }}">
                        <span class="form-hint">Preferred check-in time (for reference only)</span>
                    </div>

                    <div class="form-group">
                        <label for="check_out_time">Check-out Time</label>
                        <input type="time" name="check_out_time" id="check_out_time" 
                               value="{{ old('check_out_time', '17:00') }}">
                        <span class="form-hint">Preferred check-out time (for reference only)</span>
                    </div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-info-circle"></i> Boarding Status
                </div>

                <div class="form-group">
                    <label>Current Status</label>
                    @php
                        $isActive = $boarding->isActive();
                        $statusText = $isActive ? 'Active' : (now()->toDateString() > $boarding->end_date ? 'Completed' : 'Upcoming');
                        $statusClass = $isActive ? 'active' : (now()->toDateString() > $boarding->end_date ? 'completed' : 'active');
                    @endphp
                    <div class="status-badge status-{{ $statusClass }}">
                        Status: <strong>{{ $statusText }}</strong>
                    </div>
                    <span class="form-hint">Status is automatically calculated based on dates. Update dates to change status.</span>
                </div>
            </div>

            <!-- Cage Assignment Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-home"></i> Cage Assignment
                </div>

                <div class="form-group">
                    <label for="cage_id">
                        Assign Cage
                        <span class="required">*</span>
                    </label>
                    <select name="cage_id" id="cage_id" required class="@error('cage_id') is-invalid @enderror">
                        <option value="">-- Select Available Cage --</option>
                        @if(isset($cages))
                            @foreach($cages as $cage)
                                <option value="{{ $cage->id }}" @selected(old('cage_id', $boarding->cage_id ?? '') == $cage->id)>
                                    {{ $cage->cage_code }} - {{ $cage->location }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('cage_id')
                        <span class="form-hint" style="color: #dc3545;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Cage Info Display -->
                <div class="cage-info">
                    <div class="cage-stat">
                        <label>Current Cage Code</label>
                        <value>{{ $boarding->cageAssigned->cage_code ?? 'N/A' }}</value>
                    </div>
                    <div class="cage-stat">
                        <label>Location</label>
                        <value>{{ $boarding->cageAssigned->location ?? 'N/A' }}</value>
                    </div>
                    <div class="cage-stat">
                        <label>Status</label>
                        <value>{{ ucfirst($boarding->cageAssigned->status ?? 'N/A') }}</value>
                    </div>
                </div>
            </div>

            <!-- Medication Notes Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-pills"></i> Medication Notes
                </div>

                <div class="form-group">
                    <label for="medication_notes">Medication Instructions</label>
                    <textarea name="medication_notes" id="medication_notes" 
                              placeholder="Enter medication instructions, dosage, frequency, etc."
                              class="@error('medication_notes') is-invalid @enderror">{{ old('medication_notes', $boarding->medicationInstruction->instructions ?? '') }}</textarea>
                    @error('medication_notes')
                        <span class="form-hint" style="color: #dc3545;">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Enter any medication instructions or notes for this pet</span>
                </div>
            </div>

            <!-- Feeding Schedule Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-utensils"></i> Feeding Schedule
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="morning_feed_time">Morning Feed Time</label>
                        @php
                            $schedule = $boarding->feedingSchedule->schedule ?? '';
                            $times = $schedule && $schedule !== 'As_Needed' ? explode(',', $schedule) : [];
                            $morningTime = $times[0] ?? '';
                        @endphp
                        <input type="time" name="morning_feed_time" id="morning_feed_time" 
                               value="{{ old('morning_feed_time', $morningTime) }}">
                        <span class="form-hint">Leave empty if not feeding at this time</span>
                    </div>

                    <div class="form-group">
                        <label for="afternoon_feed_time">Afternoon Feed Time</label>
                        @php
                            $afternoonTime = $times[1] ?? '';
                        @endphp
                        <input type="time" name="afternoon_feed_time" id="afternoon_feed_time" 
                               value="{{ old('afternoon_feed_time', $afternoonTime) }}">
                        <span class="form-hint">Leave empty if not feeding at this time</span>
                    </div>

                    <div class="form-group">
                        <label for="evening_feed_time">Evening Feed Time</label>
                        @php
                            $eveningTime = $times[2] ?? '';
                        @endphp
                        <input type="time" name="evening_feed_time" id="evening_feed_time" 
                               value="{{ old('evening_feed_time', $eveningTime) }}">
                        <span class="form-hint">Leave empty if not feeding at this time</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="feeding_notes">Feeding Notes</label>
                    <textarea name="feeding_notes" id="feeding_notes" 
                              placeholder="For more than 3 feeding times per day, as-needed feeding, or any special feeding instructions..."
                              class="@error('feeding_notes') is-invalid @enderror"
                              style="min-height: 100px;">{{ old('feeding_notes', $boarding->feedingSchedule->notes ?? '') }}</textarea>
                    @error('feeding_notes')
                        <span class="form-hint" style="color: #dc3545;">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Use this field for additional feeding details, meal portions, frequency notes, or special feeding requirements</span>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.boarding.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Boarding
                </button>
                <form method="POST" action="{{ route('admin.boarding.destroy', $boarding->id ?? 0) }}" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this boarding record? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Boarding
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
