@extends('admin.dashboard')

@section('page-title', 'Appointment #'.$appointment->id)
@section('page-description', 'Detailed view of the appointment itinerary')

@section('content')
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .card {
            background: var(--white);
            border-radius: 18px;
            padding: 26px;
            box-shadow: var(--shadow-soft);
        }

        .card h3 {
            margin-top: 0;
            font-size: 18px;
            font-family: 'Fredoka', sans-serif;
            margin-bottom: 16px;
            color: var(--dark-text);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 13px;
            color: var(--light-text);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark-text);
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 12px;
            font-weight: 600;
            background: rgba(74, 144, 226, 0.15);
            color: var(--primary-blue);
        }

        .badge-status.warning {
            background: rgba(255, 140, 66, 0.18);
            color: var(--primary-orange);
        }

        .badge-status.success {
            background: rgba(95, 208, 104, 0.18);
            color: var(--accent-green);
        }

        .badge-status.danger {
            background: rgba(255, 107, 157, 0.18);
            color: var(--accent-pink);
        }

        .notes-block {
            background: rgba(255, 140, 66, 0.08);
            border-radius: 14px;
            padding: 16px;
            margin-top: 10px;
            font-size: 14px;
            color: var(--dark-text);
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
        }

        .actions form {
            margin: 0;
        }

        .history-list {
            display: grid;
            gap: 12px;
        }

        .history-item {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 14px;
            padding: 16px;
            box-shadow: var(--shadow-soft);
        }

        @media (max-width: 992px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary" style="margin-bottom: 20px;">
        <i class="fas fa-arrow-left"></i> Back to list
    </a>

    <div class="detail-grid">
        <div class="card">
            <h3><i class="fas fa-calendar-check"></i> Appointment Summary</h3>

            <div class="info-row">
                <div class="info-label">Scheduled for</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y g:i A') }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Pet</div>
                <div class="info-value">
                    {{ $appointment->pet->name ?? 'Unnamed Pet' }}
                    <span style="font-size: 12px; color: var(--light-text); margin-left: 6px;">
                        {{ ucfirst($appointment->pet->species ?? 'N/A') }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Owner</div>
                <div class="info-value">
                    @if($appointment->pet && $appointment->pet->owner && $appointment->pet->owner->user)
                        {{ $appointment->pet->owner->user->first_name }} {{ $appointment->pet->owner->user->last_name }}
                    @else
                        N/A
                    @endif
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Visit Type</div>
                <div class="info-value">
                    <span class="badge-status warning">
                        <i class="fas fa-tag"></i>
                        {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    @php
                        $badge = match($appointment->status) {
                            'completed' => 'success',
                            'cancelled', 'no_show' => 'danger',
                            default => 'warning',
                        };
                    @endphp
                    <span class="badge-status {{ $badge }}">
                        <i class="fas fa-info-circle"></i>
                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Assigned Veterinarian</div>
                <div class="info-value">
                    @if($appointment->veterinarian)
                        Dr. {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}
                    @else
                        <span style="color: var(--primary-orange);">Unassigned</span>
                    @endif
                </div>
            </div>

            @if(!$appointment->veterinarian || $appointment->status === 'pending')
            <div class="info-row">
                <div class="info-label">Assign to Staff</div>
                <div class="info-value">
                    <form action="{{ route('admin.appointments.assign', $appointment->id) }}" method="POST" style="display: flex; gap: 8px; align-items: center;">
                        @csrf
                        <select name="veterinarian_id" class="form-control" style="flex: 1; padding: 6px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px;" required>
                            <option value="">Select Staff...</option>
                            @php
                                $requiredRole = match($appointment->type) {
                                    'consultation', 'vaccination', 'surgery', 'follow_up', 'emergency' => 'veterinarian',
                                    'grooming' => 'groomer',
                                    'boarding' => 'boarding',
                                    default => null,
                                };
                            @endphp
                            @if($requiredRole)
                                @php
                                    $availableStaff = \App\Models\User::where('role', $requiredRole)
                                        ->where('is_active', 1)
                                        ->orderBy('first_name')
                                        ->get();
                                @endphp
                                @foreach($availableStaff as $staff)
                                    <option value="{{ $staff->id }}" {{ $appointment->veterinarian_id == $staff->id ? 'selected' : '' }}>
                                        Dr. {{ $staff->first_name }} {{ $staff->last_name }} ({{ ucfirst($staff->role) }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">
                            <i class="fas fa-user-plus"></i> Assign
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="info-row">
                <div class="info-label">Created on</div>
                <div class="info-value">{{ $appointment->created_at_formatted }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Last updated</div>
                <div class="info-value">{{ $appointment->updated_at_formatted }}</div>
            </div>

            <div>
                <div class="info-label" style="margin-top: 20px;">Notes</div>
                @if($appointment->notes)
                    <div class="notes-block">{!! nl2br(e($appointment->notes)) !!}</div>
                @else
                    <p style="font-size: 13px; color: var(--light-text); margin-top: 8px;">No notes recorded.</p>
                @endif
            </div>

            <div class="actions">
                <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                    <button type="button" class="btn btn-secondary" style="background: #ff9800; color: white;" 
                            onclick="showCancelModal()">
                        <i class="fas fa-ban"></i> Cancel Appointment
                    </button>
                @endif
                
                <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST"
                      onsubmit="return confirm('Delete this appointment permanently? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" style="background: #ff6b6b; color: white;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-clipboard-list"></i> Quick Reference</h3>
            <div class="history-list">
                <div class="history-item">
                    <strong>Owner Contact</strong>
                    <p style="margin-top: 6px; color: var(--light-text);">
                        @if($appointment->pet && $appointment->pet->owner && $appointment->pet->owner->user)
                            {{ $appointment->pet->owner->user->first_name }} {{ $appointment->pet->owner->user->last_name }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div class="history-item">
                    <strong>Pet Identifier</strong>
                    <p style="margin-top: 6px; color: var(--light-text);">
                        ID: {{ $appointment->pet_id ?? 'N/A' }}
                    </p>
                </div>
                <div class="history-item">
                    <strong>Veterinarian ID</strong>
                    <p style="margin-top: 6px; color: var(--light-text);">
                        {{ $appointment->veterinarian_id ?? 'Not assigned' }}
                        @if($appointment->veterinarian)
                             <br>Dr. {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Appointment Modal -->
    <div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 18px; padding: 30px; max-width: 500px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <h3 style="margin-top: 0; color: #ff9800;"><i class="fas fa-exclamation-triangle"></i> Cancel Appointment</h3>
            <p style="color: var(--light-text); margin-bottom: 20px;">
                Are you sure you want to cancel this appointment? Please provide a reason for cancellation (optional).
            </p>
            
            <form action="{{ route('admin.appointments.cancel', $appointment->id) }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="cancellation_reason" style="display: block; margin-bottom: 8px; font-weight: 600;">Cancellation Reason</label>
                    <textarea 
                        id="cancellation_reason" 
                        name="cancellation_reason" 
                        rows="4" 
                        class="form-control" 
                        placeholder="e.g., Veterinarian not available on this date, schedule conflict, etc."
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                    ></textarea>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="hideCancelModal()">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: #ff9800;">
                        <i class="fas fa-ban"></i> Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showCancelModal() {
            document.getElementById('cancelModal').style.display = 'flex';
        }
        
        function hideCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideCancelModal();
            }
        });
    </script>
@endsection
