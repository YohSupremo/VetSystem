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
            <strong>Created:</strong> {{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, Y g:i A') }}
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
                    <label for="veterinarian_id">Assigned Veterinarian</label>
                    <select name="veterinarian_id" id="veterinarian_id">
                        <option value="">Unassigned</option>
                        @foreach($veterinarians as $vet)
                            <option value="{{ $vet->id }}" {{ old('veterinarian_id', $appointment->veterinarian_id) == $vet->id ? 'selected' : '' }}>
                                Dr. {{ $vet->first_name }} {{ $vet->last_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="inline-hint">Optional — assign later if unsure.</p>
                    @error('veterinarian_id')
                        <div class="inline-hint">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-calendar-alt"></i> Appointment Details</h3>

                <div class="form-group">
                    <label for="appointment_date">Date & Time <span style="color: var(--accent-pink);">*</span></label>
                    <input type="datetime-local" name="appointment_date" id="appointment_date"
                           value="{{ old('appointment_date', $appointment->appointment_date_input) }}" required>
                    @error('appointment_date')
                        <div class="inline-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">Visit Type <span style="color: var(--accent-pink);">*</span></label>
                    <select name="type" id="type" required>
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
@endsection
