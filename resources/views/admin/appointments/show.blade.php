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
                <div class="info-value">{{ $appointment->formatted_date }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Pet</div>
                <div class="info-value">
                    {{ $appointment->pet_name ?? 'Unnamed Pet' }}
                    <span style="font-size: 12px; color: var(--light-text); margin-left: 6px;">
                        {{ ucfirst($appointment->pet_species ?? 'N/A') }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Owner</div>
                <div class="info-value">{{ $appointment->owner_name ?? 'N/A' }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Visit Type</div>
                <div class="info-value">
                    <span class="badge-status warning">
                        <i class="fas fa-tag"></i>
                        {{ $appointment->type_label }}
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
                        {{ $appointment->status_label }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Assigned Veterinarian</div>
                <div class="info-value">{{ $appointment->veterinarian_name ?? 'Unassigned' }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Created on</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, Y g:i A') }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Last updated</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y g:i A') }}</div>
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
                <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST"
                      onsubmit="return confirm('Delete this appointment?');">
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
                        {{ $appointment->owner_name ?? 'N/A' }}
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
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
