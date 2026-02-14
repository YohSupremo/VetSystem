@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('customer.appointments.index') }}" class="text-decoration-none text-muted me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="h3 mb-0">Book Appointment</h1>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.appointments.store') }}" method="POST" id="appointmentForm">
                        @csrf
                        
                        <!-- Pet Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Pet</label>
                            <div class="row g-3">
                                @foreach($pets as $pet)
                                    <div class="col-6 col-md-4">
                                        <input type="radio" class="btn-check" name="pet_id" id="pet_{{ $pet->id }}" value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-light text-dark w-100 p-3 border text-start h-100 d-flex flex-column align-items-center justify-content-center" for="pet_{{ $pet->id }}">
                                            @if($pet->photo_path)
                                                <img src="{{ asset($pet->photo_path) }}" alt="{{ $pet->name }}" class="rounded-circle mb-2" width="60" height="60" style="object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded-circle mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-paw fa-2x text-muted"></i>
                                                </div>
                                            @endif
                                            <span class="fw-bold d-block text-truncate w-100 text-center">{{ $pet->name }}</span>
                                            <small class="text-muted">{{ $pet->species }}</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('pet_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Appointment Type -->
                        <div class="mb-4">
                            <label for="type" class="form-label fw-bold">Appointment Type</label>
                            <select name="type" id="type" class="form-select form-select-lg" required>
                                <option value="">Select reason for visit...</option>
                                @foreach($appointmentTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Selection -->
                        <div class="mb-4">
                            <label for="appointment_date" class="form-label fw-bold">Preferred Date</label>
                            <select name="appointment_date" id="appointment_date" class="form-select" required>
                                <option value="">Select a date...</option>
                                @foreach($availableSlots as $date => $data)
                                    <option value="{{ $date }}" {{ old('appointment_date') == $date ? 'selected' : '' }} data-slots='{{ json_encode($data['slots']) }}'>
                                        {{ $data['date'] }} ({{ $data['day_name'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Time Slot Selection -->
                        <div class="mb-4" id="timeSlotContainer" style="display: none;">
                            <label class="form-label fw-bold">Available Time Slots</label>
                            <div class="row g-2" id="timeSlots">
                                <!-- Slots injected via JS -->
                            </div>
                            <input type="hidden" name="start_time" id="start_time" required>
                            <div class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i> Appointments are typically 30 minutes long.
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">Additional Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Describe symptoms or specific concerns...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Confirm Booking
                            </button>
                            <a href="{{ route('customer.appointments.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateSelect = document.getElementById('appointment_date');
    const timeContainer = document.getElementById('timeSlotContainer');
    const timeSlotsDiv = document.getElementById('timeSlots');
    const timeInput = document.getElementById('start_time');
    
    // Function to render time slots
    function renderTimeSlots(slots) {
        timeSlotsDiv.innerHTML = '';
        
        if (slots.length === 0) {
            timeSlotsDiv.innerHTML = '<div class="col-12 text-muted text-center py-2">No slots available for this date.</div>';
            return;
        }

        slots.forEach(slot => {
            const col = document.createElement('div');
            col.className = 'col-4 col-sm-3';
            
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `btn w-100 ${slot.available ? 'btn-outline-primary' : 'btn-light text-muted'}`;
            btn.textContent = slot.time;
            btn.disabled = !slot.available;
            
            if (slot.available) {
                btn.onclick = function() {
                    // Remove active class from all buttons
                    document.querySelectorAll('#timeSlots button').forEach(b => {
                        b.classList.remove('active', 'btn-primary');
                        b.classList.add('btn-outline-primary');
                    });
                    
                    // Add active class to clicked button
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('active', 'btn-primary');
                    
                    // Set input value
                    timeInput.value = slot.time;
                };
            }
            
            col.appendChild(btn);
            timeSlotsDiv.appendChild(col);
        });
        
        timeContainer.style.display = 'block';
    }
    
    // Handle date change
    dateSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const slots = JSON.parse(selectedOption.dataset.slots);
            renderTimeSlots(slots);
            timeInput.value = ''; // Reset time
        } else {
            timeContainer.style.display = 'none';
        }
    });
    
    // Pre-select if old value exists
    if (dateSelect.value) {
        dateSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
