@extends('admin.dashboard')

@section('page-title', 'Update Appointment')
@section('page-description', 'Modify the details of an existing appointment')

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

        .inline-hint {
            font-size: 12px;
            color: var(--light-text);
            margin-top: 6px;
        }

        .info-card {
            background: rgba(255, 140, 66, 0.08);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 20px;
        }

        .info-card p {
            margin: 0;
            font-size: 13px;
            color: var(--light-text);
        }
    </style>

    <div class="info-card">
        <p>
            <strong>Appointment ID:</strong> #{{ $appointment->id }} •
            <strong>Created:</strong> {{ $appointment->created_at_formatted }}
        </p>
    </div>

    <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-section">
                <h3><i class="fas fa-paw"></i> Patient Details</h3>

                <div class="form-group">
                    <label for="pet_id">Pet <span style="color: var(--accent-pink);">*</span></label>
                    <select name="pet_id" id="pet_id" required>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', $appointment->pet_id) == $pet->id ? 'selected' : '' }}>
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
                    @if(isset($isVeterinarian) && $isVeterinarian)
                        {{-- Veterinarian sees their own name, read-only --}}
                        <input type="text" 
                               value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }} (Veterinarian)" 
                               class="form-control" 
                               readonly 
                               style="background-color: #f5f5f5; cursor: not-allowed;">
                        <input type="hidden" name="veterinarian_id" value="{{ auth()->user()->id }}">
                        <div class="inline-hint">You cannot change the assigned veterinarian.</div>
                    @else
                        {{-- Other users see dropdown --}}
                        <select name="veterinarian_id" id="veterinarian_id">
                            <option value="">Select staff</option>
                            @foreach($assignableStaff as $staff)
                                <option value="{{ $staff->id }}"
                                        data-role="{{ $staff->role }}"
                                        {{ old('veterinarian_id', $appointment->veterinarian_id) == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->first_name }} {{ $staff->last_name }} ({{ ucfirst($staff->role) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="inline-hint" id="assignee_hint">Staff options are filtered by visit type.</div>
                    @endif
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
                           value="{{ old('appointment_date', \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d\TH:i')) }}" required>
                    @error('appointment_date')
                        <div class="inline-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">Visit Type <span style="color: var(--accent-pink);">*</span></label>
                    <select name="type" id="type">
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ old('type', $appointment->type) == $type ? 'selected' : '' }}>
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
                            <option value="{{ $status }}" {{ old('status', $appointment->status) == $status ? 'selected' : '' }}>
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
                           min="0" max="10" value="{{ old('queue_priority', $appointment->queue_priority ?? 0) }}">
                    <div class="inline-hint">Higher priority = seen first (0 = normal, 10 = urgent)</div>
                    @error('queue_priority')
                        <div class="inline-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="4" placeholder="Treatment goals, owner preferences, etc.">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')
                        <div class="inline-hint">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Appointment
            </button>
        </div>
    </form>

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
