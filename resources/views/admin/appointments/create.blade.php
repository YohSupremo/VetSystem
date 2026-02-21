@extends('admin.dashboard')

@section('page-title', 'Schedule Appointment')
@section('page-description', 'Create a new appointment for a pet')

@section('content')
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .form-section {
            background: var(--white);
            border-radius: 18px;
            padding: 26px;
            box-shadow: var(--shadow-soft);
        }

        .form-section h3 {
            margin-top: 0;
            font-size: 18px;
            font-family: 'Fredoka', sans-serif;
            margin-bottom: 18px;
            color: var(--dark-text);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 8px;
            font-size: 13px;
        }

        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 2px solid var(--soft-gray);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 4px rgba(255, 140, 66, 0.15);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .form-actions .btn {
            flex: 1;
        }

        .empty-state {
            background: var(--white);
            border-radius: 18px;
            padding: 40px 24px;
            text-align: center;
            box-shadow: var(--shadow-soft);
        }

        .empty-state i {
            font-size: 60px;
            color: var(--soft-gray);
            margin-bottom: 16px;
            display: block;
        }

        .inline-hint {
            font-size: 12px;
            color: var(--light-text);
            margin-top: 6px;
        }
    </style>

    @if(!$pets->count())
        <div class="empty-state">
            <i class="fas fa-paw"></i>
            <h3>No pets found</h3>
            <p>Please register a pet before scheduling an appointment.</p>
            <a href="{{ route('admin.pets.create') }}" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-plus"></i> Register Pet
            </a>
        </div>
    @else
        <form action="{{ route('admin.appointments.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-section">
                    <h3><i class="fas fa-paw"></i> Patient Details</h3>

                    <div class="form-group">
                        <label for="pet_id">Pet <span style="color: var(--accent-pink);">*</span></label>
                        <select name="pet_id" id="pet_id" required>
                            <option value="">Select pet</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} — {{ ucfirst($pet->species ?? 'N/A') }}
                                </option>
                            @endforeach
                        </select>
                        @error('pet_id')
                            <div class="inline-hint">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="veterinarian_id">Assigned Staff</label>
                        <select name="veterinarian_id" id="veterinarian_id">
                            <option value="">Select staff</option>
                            @foreach($assignableStaff as $staff)
                                <option value="{{ $staff->id }}"
                                        data-role="{{ $staff->role }}"
                                        {{ old('veterinarian_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->first_name }} {{ $staff->last_name }} ({{ ucfirst($staff->role) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="inline-hint" id="assignee_hint">Staff options are filtered by visit type.</div>
                        @error('veterinarian_id')
                            <div class="inline-hint">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-calendar-alt"></i> Appointment Details</h3>

                    <div class="form-group">
                        <label for="appointment_date">Appointment Date & Time <span style="color: var(--accent-pink);">*</span></label>
                        <input type="datetime-local" name="appointment_date" id="appointment_date"
                               value="{{ old('appointment_date') }}" required>
                        @error('appointment_date')
                            <div class="inline-hint">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Visit Type <span style="color: var(--accent-pink);">*</span></label>
                        <select name="type" id="type">
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="inline-hint">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span style="color: var(--accent-pink);">*</span></label>
                        <select name="status" id="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="inline-hint">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="queue_priority">Queue Priority (0-10)</label>
                        <input type="number" name="queue_priority" id="queue_priority" 
                               min="0" max="10" value="{{ old('queue_priority', 0) }}">
                        <div class="inline-hint">Higher priority = seen first (0 = normal, 10 = urgent)</div>
                        @error('queue_priority')
                            <div class="inline-hint">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" rows="4" placeholder="Treatment goals, owner preferences, etc.">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="inline-hint">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i> Save Appointment
                </button>
            </div>
        </form>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const assigneeSelect = document.getElementById('veterinarian_id');
            const assigneeHint = document.getElementById('assignee_hint');

            const roleMap = {
                consultation: 'veterinarian',
                vaccination: 'veterinarian',
                surgery: 'veterinarian',
                follow_up: 'veterinarian',
                emergency: 'veterinarian',
                grooming: 'groomer',
                boarding: 'boarding'
            };

            const hintMap = {
                veterinarian: 'Showing veterinarians for this visit type.',
                groomer: 'Showing groomers for this visit type.',
                boarding: 'Showing boarding staff for this visit type.',
                all: 'Showing all staff for this visit type.'
            };

            const filterAssignees = () => {
                const selectedType = typeSelect.value;
                const requiredRole = roleMap[selectedType] || null;

                Array.from(assigneeSelect.options).forEach((option, index) => {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }

                    const optionRole = option.dataset.role;
                    const shouldShow = !requiredRole || optionRole === requiredRole;
                    option.hidden = !shouldShow;

                    if (!shouldShow && option.selected) {
                        assigneeSelect.value = '';
                    }
                });

                assigneeHint.textContent = requiredRole ? hintMap[requiredRole] : hintMap.all;
            };

            typeSelect.addEventListener('change', filterAssignees);
            filterAssignees();
        });
    </script>
@endsection
