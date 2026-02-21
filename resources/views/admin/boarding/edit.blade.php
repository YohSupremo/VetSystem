@extends('admin.dashboard')

@section('page-title', 'Edit Boarding Reservation')
@section('page-description', 'Update boarding reservation details')

@section('content')
<style>
    :root {
        --primary: #FF7E7E;
        --primary-dark: #FF6B6B;
        --bg: #F9FAFB;
        --card: #FFFFFF;
        --text: #1F2937;
        --text-light: #6B7280;
        --border: #E5E7EB;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --info: #3B82F6;
    }

    .edit-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .breadcrumb a:hover {
        color: var(--primary-dark);
    }

    .breadcrumb-separator {
        color: var(--text-light);
    }

    .breadcrumb-current {
        color: var(--text-light);
    }

    /* Page Header */
    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-title i {
        color: var(--primary);
    }

    .page-subtitle {
        color: var(--text-light);
        font-size: 1rem;
    }

    /* Alert */
    .alert {
        padding: 1.25rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid;
    }

    .alert-danger {
        background: linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%);
        border-color: #FCA5A5;
        color: #991B1B;
    }

    .alert-danger strong {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .alert-danger li {
        margin: 0.25rem 0;
    }

    /* Form Card */
    .form-card {
        background: var(--card);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    /* Form Section */
    .form-section {
        padding: 2rem;
        border-bottom: 1px solid var(--border);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section.feeding {
        background: linear-gradient(135deg, #ECFDF5 0%, #FFFFFF 100%);
    }

    .form-section.medication {
        background: linear-gradient(135deg, #FEF2F2 0%, #FFFFFF 100%);
    }

    .form-section.diet {
        background: linear-gradient(135deg, #FFFBEB 0%, #FFFFFF 100%);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid;
    }

    .form-section.feeding .section-header {
        border-bottom-color: #A7F3D0;
    }

    .form-section.medication .section-header {
        border-bottom-color: #FCA5A5;
    }

    .form-section.diet .section-header {
        border-bottom-color: #FDE68A;
    }

    .section-header {
        border-bottom-color: var(--border);
    }

    .section-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .form-section.feeding .section-icon {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
    }

    .form-section.medication .section-icon {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        color: white;
    }

    .form-section.diet .section-icon {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: white;
    }

    .section-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text);
    }

    /* Form Group */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .required {
        color: var(--danger);
        margin-left: 0.25rem;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem;
        border: 2px solid var(--border);
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s;
        font-family: inherit;
        background: white;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(255, 126, 126, 0.1);
        outline: none;
    }

    .form-control:disabled {
        background: var(--bg);
        color: var(--text-light);
        cursor: not-allowed;
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-hint {
        font-size: 0.875rem;
        color: var(--text-light);
        margin-top: 0.5rem;
        display: block;
    }

    .error-message {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
        font-weight: 500;
    }

    /* Form Row */
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    /* Form Actions */
    .form-actions {
        padding: 2rem;
        background: var(--bg);
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn {
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 126, 126, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 126, 126, 0.4);
    }

    .btn-secondary {
        background: white;
        color: var(--text);
        border: 2px solid var(--border);
    }

    .btn-secondary:hover {
        background: var(--bg);
        border-color: var(--text-light);
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--danger) 0%, #DC2626 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }

    /* Pet Preview */
    .pet-preview {
        background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .pet-avatar {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .pet-info h4 {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 0.5rem;
    }

    .pet-info p {
        font-size: 0.95rem;
        color: var(--text-light);
        margin: 0.25rem 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .edit-container {
            padding: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .form-section {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .pet-preview {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="edit-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.boarding.index') }}">
            <i class="fas fa-home"></i>
            <span>Boardings</span>
        </a>
        <span class="breadcrumb-separator">
            <i class="fas fa-chevron-right"></i>
        </span>
        <span class="breadcrumb-current">Edit Boarding #{{ $boarding->id }}</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-edit"></i>
            Edit Boarding Record
        </h1>
        <p class="page-subtitle">Update boarding details and care instructions</p>
    </div>


    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('admin.boarding.update', $boarding->id) }}">
            @csrf
            @method('PUT')

            <!-- Pet Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div class="section-title">Pet Information</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Pet (Cannot be changed)</label>
                    <input type="text" class="form-control" disabled value="{{ $boarding->pet->name ?? 'N/A' }}">
                    <span class="form-hint">To assign a different pet, create a new boarding record</span>
                </div>

                <!-- Pet Preview -->
                <div class="pet-preview">
                    @php $pet = $boarding->pet; @endphp
                    <img src="{{ $pet ? $pet->photo_url : 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\" viewBox=\"0 0 200 200\"><rect fill=\"#f0f0f0\" width=\"200\" height=\"200\"/><text x=\"50%\" y=\"50%\" font-size=\"80\" text-anchor=\"middle\" dominant-baseline=\"middle\" fill=\"#ccc\">🐾</text></svg>') }}" 
                         alt="Pet" class="pet-avatar" 
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+PHJlY3QgZmlsbD0iI2YwZjBmMCIgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1zaXplPSI4MCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iI2NjYyI+8J+QrjwvdGV4dD48L3N2Zz4='">
                    <div class="pet-info">
                        <h4>{{ $pet->name ?? 'N/A' }}</h4>
                        <p><strong>Breed:</strong> {{ $pet->breed ?? 'N/A' }}</p>
                        <p><strong>Owner:</strong> {{ $pet->owner->user->first_name ?? 'N/A' }} {{ $pet->owner->user->last_name ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Boarding Dates Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="section-title">Boarding Schedule</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="start_date">Check-in Date</label>
                        <input type="date" name="start_date" id="start_date" 
                               value="{{ old('start_date', $boarding->start_date) }}" 
                               class="form-control @error('start_date') is-invalid @enderror">
                        @error('start_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="form-hint">Leave empty to keep existing date: {{ $boarding->start_date }}</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="end_date">Check-out Date</label>
                        <input type="date" name="end_date" id="end_date" 
                               value="{{ old('end_date', $boarding->end_date) }}" 
                               class="form-control @error('end_date') is-invalid @enderror">
                        @error('end_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="form-hint">Leave empty to keep existing date: {{ $boarding->end_date }}</span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="check_in_time">Check-in Time</label>
                        <input type="time" name="check_in_time" id="check_in_time" 
                               value="{{ old('check_in_time', $boarding->check_in_time ? \Carbon\Carbon::parse($boarding->check_in_time)->format('H:i') : '09:00') }}"
                               class="form-control">
                        <span class="form-hint">Preferred check-in time</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="check_out_time">Check-out Time</label>
                        <input type="time" name="check_out_time" id="check_out_time" 
                               value="{{ old('check_out_time', $boarding->check_out_time ? \Carbon\Carbon::parse($boarding->check_out_time)->format('H:i') : '17:00') }}"
                               class="form-control">
                        <span class="form-hint">Preferred check-out time</span>
                    </div>
                </div>
            </div>

            <!-- Cage Assignment Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="section-title">Cage Assignment</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cage_id">
                        Assign Cage
                        <span class="required">*</span>
                    </label>
                    <select name="cage_id" id="cage_id" class="form-control @error('cage_id') is-invalid @enderror">
                        <option value="">-- Select Available Cage --</option>
                        @if(isset($cages))
                            @foreach($cages as $cage)
                                <option value="{{ $cage->id }}" @selected(old('cage_id', $boarding->cage_id) == $cage->id)>
                                    {{ $cage->cage_code }} - {{ $cage->location }} ({{ ucfirst($cage->size) }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('cage_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Current: {{ $boarding->cage->cage_code ?? 'N/A' }} - {{ $boarding->cage->location ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Feeding Schedule Section -->
            <div class="form-section feeding">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="section-title">Feeding Instructions</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="feeding_schedule">Feeding Schedule</label>
                    <textarea name="feeding_schedule" id="feeding_schedule" 
                              placeholder="e.g., Feed twice daily with premium kibble, 1 cup per meal"
                              class="form-control @error('feeding_schedule') is-invalid @enderror">{{ old('feeding_schedule', $boarding->feeding_schedule) }}</textarea>
                    @error('feeding_schedule')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Describe the general feeding schedule and instructions</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="feeding_times">Feeding Times</label>
                    <input type="text" name="feeding_times" id="feeding_times"
                           value="{{ old('feeding_times', $boarding->feeding_times) }}"
                           placeholder="e.g., 08:00, 13:00, 18:00"
                           class="form-control @error('feeding_times') is-invalid @enderror">
                    @error('feeding_times')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Enter feeding times separated by commas</span>
                </div>
            </div>

            <!-- Special Diet Section -->
            <div class="form-section diet">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-apple-alt"></i>
                    </div>
                    <div class="section-title">Special Diet Notes</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="special_diet_notes">Dietary Requirements</label>
                    <textarea name="special_diet_notes" id="special_diet_notes" 
                              placeholder="e.g., No chicken (allergic), grain-free diet only, sensitive stomach"
                              class="form-control @error('special_diet_notes') is-invalid @enderror">{{ old('special_diet_notes', $boarding->special_diet_notes) }}</textarea>
                    @error('special_diet_notes')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Enter any allergies, dietary restrictions, or special food requirements</span>
                </div>
            </div>

            <!-- Medication Section -->
            <div class="form-section medication">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="section-title">Medication Instructions</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="medication_instructions">Medication Details</label>
                    <textarea name="medication_instructions" id="medication_instructions" 
                              placeholder="e.g., Give 1 tablet of antibiotics with food, apply ear drops twice daily"
                              class="form-control @error('medication_instructions') is-invalid @enderror">{{ old('medication_instructions', $boarding->medication_instructions) }}</textarea>
                    @error('medication_instructions')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Enter medication names, dosages, and administration instructions</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="medication_times">Medication Times</label>
                    <input type="text" name="medication_times" id="medication_times"
                           value="{{ old('medication_times', $boarding->medication_times) }}"
                           placeholder="e.g., 08:30, 20:30"
                           class="form-control @error('medication_times') is-invalid @enderror">
                    @error('medication_times')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Enter medication times separated by commas</span>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="section-title">Additional Information</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="daily_rate">Daily Rate (₱)</label>
                          <input type="number" name="daily_rate" id="daily_rate" min="0" step="0.01"
                           value="{{ old('daily_rate', $boarding->daily_rate) }}"
                           placeholder="500.00"
                           class="form-control @error('daily_rate') is-invalid @enderror">
                    @error('daily_rate')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Enter the daily boarding rate</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="notes">General Notes</label>
                    <textarea name="notes" id="notes" 
                              placeholder="Any additional notes or special instructions..."
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $boarding->notes) }}</textarea>
                    @error('notes')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Enter any other important information about this boarding</span>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.boarding.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Boarding
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Form -->
    <div class="form-card">
        <div class="form-section">
            <div class="section-header">
                <div class="section-icon" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="section-title">Danger Zone</div>
            </div>

            <p style="color: var(--text-light); margin-bottom: 1.5rem;">
                Deleting this boarding record will permanently remove it from the system. This action cannot be undone.
            </p>

            <form method="POST" action="{{ route('admin.boarding.destroy', $boarding->id) }}" onsubmit="return confirm('Are you sure you want to delete this boarding record? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Delete Boarding
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
