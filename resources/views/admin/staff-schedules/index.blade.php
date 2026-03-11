@extends('admin.dashboard')

@section('page-title', 'Staff Schedules')
@section('page-description', 'Manage staff work schedules by day and shift')

@section('content')
<style>
    .schedule-table {
        font-size: 0.85rem;
    }
    .schedule-cell {
        text-align: center;
        vertical-align: middle;
        padding: 0.5rem 0.25rem;
    }
    .schedule-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .shift-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .shift-morning {
        background-color: #fef3c7;
        color: #92400e;
    }
    .shift-night {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .staff-name {
        font-weight: 600;
        min-width: 150px;
    }
    .schedule-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }
    .schedule-modal-backdrop.is-visible {
        display: flex;
    }
    .schedule-modal {
        background: #ffffff;
        border-radius: 10px;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
    }
    .schedule-modal-header,
    .schedule-modal-footer {
        padding: 0.9rem 1.1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .schedule-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 600;
    }
    .schedule-modal-body {
        padding: 1rem 1.1rem;
        color: #374151;
    }
    .schedule-modal-footer {
        border-top: 1px solid #e5e7eb;
        border-bottom: none;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }
    .schedule-modal-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        line-height: 1;
        color: #64748b;
    }
</style>

<div class="content-header">
    <h1><i class="fas fa-calendar-alt"></i> Staff Schedules</h1>
    <p class="text-muted">Manage work schedules by day and shift</p>
</div>

<div class="card">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Weekly Schedule</h5>
                <small class="text-muted">
                    <span class="shift-badge shift-morning">Morning</span> {{ $shifts['morning'] }} &nbsp;&nbsp;
                    <span class="shift-badge shift-night">Night</span> {{ $shifts['night'] }}
                </small>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered schedule-table mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="staff-name">Staff Member</th>
                        @foreach($daysOfWeek as $day)
                            <th colspan="2" class="text-center">{{ $day }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th></th>
                        @foreach($daysOfWeek as $day)
                            <th class="text-center schedule-cell"><span class="shift-badge shift-morning">M</span></th>
                            <th class="text-center schedule-cell"><span class="shift-badge shift-night">N</span></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                        <tr>
                            <td class="staff-name">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div>{{ $member->first_name }} {{ $member->last_name }}</div>
                                        <small class="text-muted">{{ ucfirst($member->role) }}</small>
                                    </div>
                                    <a href="{{ route('admin.staff-schedules.edit', $member->id) }}" 
                                       class="btn btn-sm btn-outline-primary ms-auto">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                            @foreach($daysOfWeek as $day)
                                @php
                                    $morningSchedule = $member->staffSchedules->where('day_of_week', $day)->where('shift', 'morning')->first();
                                    $nightSchedule = $member->staffSchedules->where('day_of_week', $day)->where('shift', 'night')->first();
                                @endphp
                                <td class="schedule-cell">
                                    <input type="checkbox" 
                                           class="schedule-checkbox" 
                                           data-user-id="{{ $member->id }}"
                                           data-day="{{ $day }}"
                                           data-shift="morning"
                                           {{ $morningSchedule ? 'checked' : '' }}>
                                </td>
                                <td class="schedule-cell">
                                    <input type="checkbox" 
                                           class="schedule-checkbox" 
                                           data-user-id="{{ $member->id }}"
                                           data-day="{{ $day }}"
                                           data-shift="night"
                                           {{ $nightSchedule ? 'checked' : '' }}>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($daysOfWeek) * 2 + 1 }}" class="text-center py-5">
                                <p class="text-muted mb-0">No staff members found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="schedule-modal-backdrop" id="scheduleLimitBackdrop" aria-hidden="true">
    <div class="schedule-modal" role="dialog" aria-labelledby="scheduleLimitModalLabel" aria-modal="true">
        <div class="schedule-modal-header">
            <span id="scheduleLimitModalLabel">Schedule Limit Reached</span>
            <button type="button" class="schedule-modal-close" id="scheduleLimitClose" aria-label="Close">&times;</button>
        </div>
        <div class="schedule-modal-body">
            <p id="scheduleLimitMessage" class="mb-0">This shift is already at its limit.</p>
        </div>
        <div class="schedule-modal-footer">
            <button type="button" class="btn btn-secondary" id="scheduleLimitOk">OK</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.schedule-checkbox');
    const warningModalEl = document.getElementById('scheduleLimitBackdrop');
    const warningMessageEl = document.getElementById('scheduleLimitMessage');
    const closeBtn = document.getElementById('scheduleLimitClose');
    const okBtn = document.getElementById('scheduleLimitOk');

    const showWarning = (message) => {
        if (warningMessageEl) {
            warningMessageEl.textContent = message || 'This shift is already at its limit.';
        }

        if (warningModalEl) {
            warningModalEl.classList.add('is-visible');
            warningModalEl.setAttribute('aria-hidden', 'false');
        }
    };

    const hideWarning = () => {
        if (warningModalEl) {
            warningModalEl.classList.remove('is-visible');
            warningModalEl.setAttribute('aria-hidden', 'true');
        }
    };

    if (closeBtn) {
        closeBtn.addEventListener('click', hideWarning);
    }

    if (okBtn) {
        okBtn.addEventListener('click', hideWarning);
    }

    if (warningModalEl) {
        warningModalEl.addEventListener('click', (event) => {
            if (event.target === warningModalEl) {
                hideWarning();
            }
        });
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const day = this.dataset.day;
            const shift = this.dataset.shift;
            
            // Send AJAX request to toggle schedule
            fetch('{{ route('admin.staff-schedules.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    user_id: userId,
                    day_of_week: day,
                    shift: shift
                })
            })
            .then(async response => {
                const data = await response.json();

                if (!response.ok || !data.success) {
                    // Revert checkbox if failed
                    this.checked = !this.checked;
                    showWarning(data.message || 'Failed to update schedule');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert checkbox on error
                this.checked = !this.checked;
                showWarning('An error occurred');
            });
        });
    });
});
</script>
@endsection
