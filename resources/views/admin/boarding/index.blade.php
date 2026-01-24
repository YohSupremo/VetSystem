@extends('admin.dashboard')

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-home"></i> Pet Boarding Management</h1>
        <p>Manage all boarding activities and cage assignments</p>
    </div>
    <div class="header-actions">
        <button class="btn btn-primary" onclick="openModal('newBoardingModal')">
            <i class="fas fa-plus"></i> New Boarding
        </button>
    </div>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-icon" style="background: var(--accent-pink);">
            <i class="fas fa-paw"></i>
        </div>
        <div class="card-info">
            <h3>{{ $currentBoardings ?? 0 }}</h3>
            <p>Current Boardings</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: var(--primary-blue);">
            <i class="fas fa-home"></i>
        </div>
        <div class="card-info">
            <h3>{{ $availableCages ?? 0 }}</h3>
            <p>Available Cages</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: var(--accent-green);">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="card-info">
            <h3>{{ $upcomingCheckouts ?? 0 }}</h3>
            <p>Checkouts Today</p>
        </div>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-list"></i> Current Boardings</h2>
        <div class="section-actions">
            <input type="text" class="search-input" placeholder="Search boardings..." onkeyup="filterTable('boardingsTable', this.value)">
        </div>
    </div>
    
    <div class="table-responsive">
        <table id="boardingsTable" class="data-table">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Cage</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boardings as $boarding)
                <tr>
                    <td>
                        <div class="pet-info">
                            <img src="{{ $boarding->pet->photo_path ?? asset('images/default-pet.jpg') }}" alt="{{ $boarding->pet->name }}" class="pet-avatar">
                            <div>
                                <strong>{{ $boarding->pet->name }}</strong>
                                <span class="text-muted">{{ $boarding->pet->breed }}</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ ($boarding->pet && $boarding->pet->owner && $boarding->pet->owner->user) ? $boarding->pet->owner->user->first_name . ' ' . $boarding->pet->owner->user->last_name : 'Unknown Owner' }}</td>
                    <td>
                        <span class="badge" style="background: #4A90E2;">
                            {{ $boarding->cage->cage_code }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($boarding->start_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($boarding->end_date)->format('M d, Y') }}</td>
                    <td>
                        @php
                            $statusClass = [
                                'active' => 'success',
                                'completed' => 'secondary',
                                'cancelled' => 'danger'
                            ][$boarding->status] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $statusClass }}">
                            {{ ucfirst($boarding->status) }}
                        </span>
                    </td>
                    <td class="actions">
                        <button class="btn-icon" onclick="viewBoarding({{ $boarding->id }})" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-icon" onclick="editBoarding({{ $boarding->id }})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon text-danger" onclick="confirmDelete({{ $boarding->id }})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No boardings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- New Boarding Modal -->
<div class="modal" id="newBoardingModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>New Boarding</h3>
            <button class="close" onclick="closeModal('newBoardingModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="boardingForm">
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
                        <label>Check-in Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>Check-out Date</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Assign Cage</label>
                    <select class="form-control" name="cage_id" required>
                        <option value="">Select Cage</option>
                        @foreach($cages as $cage)
                            <option value="{{ $cage->id }}">
                                {{ $cage->cage_code }} ({{ $cage->location }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Special Instructions</label>
                    <textarea class="form-control" name="special_instructions" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('newBoardingModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Boarding</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterTable(tableId, query) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    }
}

function viewBoarding(id) {
    // Implement view functionality
    window.location.href = `/admin/boarding/${id}`;
}

function editBoarding(id) {
    // Implement edit functionality
    window.location.href = `/admin/boarding/${id}/edit`;
}

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this boarding record?')) {
        // Implement delete functionality
        fetch(`/admin/boarding/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error deleting boarding record');
            }
        });
    }
}

// Form submission
document.getElementById('boardingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/boarding', {
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
            alert('Error creating boarding record');
        }
    });
});
</script>
@endpush

<style>
.pet-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pet-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    color: white;
}

.badge-success { background-color: var(--accent-green); }
.badge-warning { background-color: #FFC107; color: #000; }
.badge-danger { background-color: #F44336; }
.badge-secondary { background-color: #6C757D; }

.actions {
    display: flex;
    gap: 5px;
}

.btn-icon {
    background: none;
    border: none;
    color: var(--light-text);
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: var(--paw-medium);
    color: var(--primary-orange);
}

.btn-icon.text-danger:hover {
    color: #F44336;
}

.text-muted {
    color: var(--light-text);
    font-size: 0.85em;
    display: block;
}
</style>
@endsection
