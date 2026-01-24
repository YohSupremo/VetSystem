@extends('admin.dashboard')

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-cut"></i> Grooming Services</h1>
        <p>Manage pet grooming appointments and services</p>
    </div>
    <div class="header-actions">
        <button class="btn btn-primary" onclick="openModal('newGroomingModal')">
            <i class="fas fa-plus"></i> New Grooming
        </button>
    </div>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-icon" style="background: var(--accent-purple);">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="card-info">
            <h3>{{ $todayAppointments ?? 0 }}</h3>
            <p>Today's Appointments</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: var(--primary-orange);">
            <i class="fas fa-spa"></i>
        </div>
        <div class="card-info">
            <h3>{{ $servicesCount ?? 0 }}</h3>
            <p>Services Available</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: var(--accent-green);">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="card-info">
            <h3>{{ $groomersCount ?? 0 }}</h3>
            <p>Groomers</p>
        </div>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-list"></i> Today's Grooming Schedule</h2>
        <div class="section-actions">
            <input type="text" class="search-input" placeholder="Search appointments..." onkeyup="filterTable('groomingTable', this.value)">
        </div>
    </div>
    
    <div class="table-responsive">
        <table id="groomingTable" class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Services</th>
                    <th>Groomer</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                <tr>
                    <td>
                        <strong>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('h:i A') }}</strong>
                    </td>
                    <td>
                        <div class="pet-info">
                            <img src="{{ $appointment->pet->photo_path ?? asset('images/default-pet.jpg') }}" alt="{{ $appointment->pet->name }}" class="pet-avatar">
                            <span>{{ $appointment->pet->name }}</span>
                        </div>
                    </td>
                    <td>{{ $appointment->pet->owner->user->first_name }} {{ $appointment->pet->owner->user->last_name }}</td>
                    <td>
                        @foreach($appointment->services as $service)
                            <span class="badge" style="background: #9B7EDE;">
                                {{ $service->name }}
                            </span>
                        @endforeach
                    </td>
                    <td>{{ $appointment->groomer->user->first_name ?? 'Not Assigned' }}</td>
                    <td>
                        @php
                            $statusClass = [
                                'scheduled' => 'info',
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'no_show' => 'secondary'
                            ][$appointment->status] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $statusClass }}">
                            {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                        </span>
                    </td>
                    <td class="actions">
                        @if($appointment->status === 'scheduled')
                            <button class="btn-icon" onclick="startGrooming({{ $appointment->id }})" title="Start Grooming">
                                <i class="fas fa-play"></i>
                            </button>
                        @elseif($appointment->status === 'in_progress')
                            <button class="btn-icon" onclick="completeGrooming({{ $appointment->id }})" title="Complete">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif
                        <button class="btn-icon" onclick="viewGrooming({{ $appointment->id }})" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-icon text-danger" onclick="cancelGrooming({{ $appointment->id }})" title="Cancel">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No grooming appointments scheduled for today</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Services Section -->
<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-spa"></i> Grooming Services</h2>
        <button class="btn btn-outline" onclick="openModal('newServiceModal')">
            <i class="fas fa-plus"></i> Add Service
        </button>
    </div>
    
    <div class="services-grid">
        @forelse($services as $service)
        <div class="service-card">
            <div class="service-icon">
                <i class="fas {{ $service->icon ?? 'fa-paw' }}"></i>
            </div>
            <h3>{{ $service->name }}</h3>
            <p class="service-description">{{ $service->description }}</p>
            <div class="service-footer">
                <span class="service-price">${{ number_format($service->price, 2) }}</span>
                <div class="service-actions">
                    <button class="btn-icon" onclick="editService({{ $service->id }})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon text-danger" onclick="deleteService({{ $service->id }})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-spa"></i>
            <p>No grooming services found</p>
            <button class="btn btn-primary" onclick="openModal('newServiceModal')">
                Add Your First Service
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- New Grooming Modal -->
<div class="modal" id="newGroomingModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>New Grooming Appointment</h3>
            <button class="close" onclick="closeModal('newGroomingModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="groomingForm">
                @csrf
                <div class="form-group">
                    <label>Pet</label>
                    <select class="form-control" name="pet_id" required>
                        <option value="">Select Pet</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}">
                                {{ $pet->name }} ({{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="appointment_date" required>
                    </div>
                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" class="form-control" name="appointment_time" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Services</label>
                    <select class="form-control select2-multiple" name="services[]" multiple="multiple" required>
                        @foreach($allServices as $service)
                            <option value="{{ $service->id }}">
                                {{ $service->name }} (${{ number_format($service->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Assign Groomer</label>
                    <select class="form-control" name="groomer_id" required>
                        <option value="">Select Groomer</option>
                        @foreach($groomers as $groomer)
                            <option value="{{ $groomer->id }}">
                                {{ $groomer->user->first_name }} {{ $groomer->user->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" name="notes" rows="2"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('newGroomingModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Service Modal -->
<div class="modal" id="newServiceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Service</h3>
            <button class="close" onclick="closeModal('newServiceModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="serviceForm">
                @csrf
                <div class="form-group">
                    <label>Service Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="description" rows="2"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <input type="number" min="5" step="5" class="form-control" name="duration" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Icon</label>
                    <select class="form-control" name="icon">
                        <option value="fa-paw">Paw</option>
                        <option value="fa-bath">Bath</option>
                        <option value="fa-cut">Scissors</option>
                        <option value="fa-spa">Spa</option>
                        <option value="fa-bone">Bone</option>
                        <option value="fa-paw">Paw Prints</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('newServiceModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize Select2 for multiple service selection
$(document).ready(function() {
    $('.select2-multiple').select2({
        placeholder: "Select services",
        allowClear: true
    });
});

function filterTable(tableId, query) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    }
}

function startGrooming(id) {
    if (confirm('Start grooming session for this pet?')) {
        updateGroomingStatus(id, 'in_progress');
    }
}

function completeGrooming(id) {
    if (confirm('Mark this grooming as completed?')) {
        updateGroomingStatus(id, 'completed');
    }
}

function cancelGrooming(id) {
    if (confirm('Cancel this grooming appointment?')) {
        updateGroomingStatus(id, 'cancelled');
    }
}

function updateGroomingStatus(id, status) {
    fetch(`/admin/grooming/${id}/status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error updating grooming status');
        }
    });
}

function viewGrooming(id) {
    window.location.href = `/admin/grooming/${id}`;
}

function editService(id) {
    // Fetch service data and populate form
    fetch(`/admin/grooming/services/${id}/edit`)
        .then(response => response.json())
        .then(service => {
            // Populate form
            const form = document.getElementById('serviceForm');
            form.elements['name'].value = service.name;
            form.elements['description'].value = service.description;
            form.elements['price'].value = service.price;
            form.elements['duration'].value = service.duration;
            form.elements['icon'].value = service.icon || 'fa-paw';
            
            // Change form action to update
            form.action = `/admin/grooming/services/${id}`;
            form.method = 'PUT';
            
            // Update modal title
            document.querySelector('#newServiceModal h3').textContent = 'Edit Service';
            
            // Open modal
            openModal('newServiceModal');
        });
}

function deleteService(id) {
    if (confirm('Are you sure you want to delete this service?')) {
        fetch(`/admin/grooming/services/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error deleting service');
            }
        });
    }
}

// Form submission for new grooming appointment
document.getElementById('groomingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/grooming', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error creating grooming appointment');
        }
    });
});

// Form submission for service
document.getElementById('serviceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const method = form.method === 'PUT' ? 'PUT' : 'POST';
    const url = method === 'PUT' ? form.action : '/admin/grooming/services';
    
    fetch(url, {
        method: method,
        body: new FormData(form),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error saving service');
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.service-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: var(--shadow-soft);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.service-icon {
    width: 60px;
    height: 60px;
    background: var(--paw-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    color: var(--accent-purple);
    font-size: 24px;
}

.service-card h3 {
    margin: 0 0 10px 0;
    color: var(--dark-text);
}

.service-description {
    color: var(--light-text);
    flex-grow: 1;
    margin-bottom: 15px;
}

.service-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.service-price {
    font-weight: 700;
    color: var(--accent-purple);
    font-size: 1.2em;
}

.service-actions {
    display: flex;
    gap: 8px;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow-soft);
}

.empty-state i {
    font-size: 48px;
    color: var(--light-text);
    margin-bottom: 15px;
    opacity: 0.6;
}

.empty-state p {
    color: var(--light-text);
    margin-bottom: 20px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .service-card {
        max-width: 100%;
    }
}
</style>
@endpush
@endsection
