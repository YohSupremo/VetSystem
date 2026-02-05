@extends('admin.dashboard')

@section('content')
<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div class="header-title">
        <h1 class="mb-2"><i class="fas fa-user-md"></i> Staff Management</h1>
        <p class="text-muted mb-0">Manage veterinarians, technicians, and staff members</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Staff Member
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <form class="d-flex gap-2" method="GET" action="{{ route('admin.staff.filter') }}">
                    <input type="search" name="q" class="form-control" placeholder="Search by name or email" value="{{ request('q') }}" style="max-width: 300px;">
                    <select name="position" class="form-select" style="max-width: 200px;">
                        <option value="">All Positions</option>
                        <option value="admin" {{ request('position') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="veterinarian" {{ request('position') == 'veterinarian' ? 'selected' : '' }}>Veterinarian</option>
                        <option value="receptionist" {{ request('position') == 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                        <option value="pharmacist" {{ request('position') == 'pharmacist' ? 'selected' : '' }}>Pharmacist</option>
                        <option value="groomer" {{ request('position') == 'groomer' ? 'selected' : '' }}>Groomer</option>
                        <option value="boarding" {{ request('position') == 'boarding' ? 'selected' : '' }}>Boarding</option>
                        <option value="pet_owner" {{ request('position') == 'pet_owner' ? 'selected' : '' }}>Pet Owner</option>
                    </select>
                    <button class="btn btn-outline-secondary" type="submit" >
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </form>
            </div>
            <div class="col-md-4 text-end d-flex align-items-center justify-content-end">
                <small class="text-muted">Showing results: <strong>{{ isset($staff) ? $staff->count() : 0 }}</strong></small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;" class="text-center">#</th>
                        <th style="width: 20%;">Name</th>
                        <th style="width: 15%;">Position</th>
                        <th style="width: 25%;">Email</th>
                        <th style="width: 12%;">Phone</th>
                        <th style="width: 10%;" class="text-center">Status</th>
                        <th style="width: 13%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff ?? [] as $member)
                    <tr>
                        <td class="text-center">{{ $member->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-bold text-dark">{{ $member->first_name }} {{ $member->last_name }}</div>
                                    @if($member->title)
                                    <div class="text-muted small mt-1">{{ $member->title }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                // Define colors with hex values for consistency
                                $roleStyles = [
                                    'admin' => ['bg' => '#dc3545', 'text' => '#ffffff'],           // Red
                                    'veterinarian' => ['bg' => '#0d6efd', 'text' => '#ffffff'],    // Blue
                                    'receptionist' => ['bg' => '#0dcaf0', 'text' => '#000000'],    // Cyan
                                    'pharmacist' => ['bg' => '#198754', 'text' => '#ffffff'],      // Green
                                    'pet_owner' => ['bg' => '#6c757d', 'text' => '#ffffff'],       // Gray
                                    'boarding' => ['bg' => '#ffc107', 'text' => '#000000'],        // Yellow
                                    'groomer' => ['bg' => '#6f42c1', 'text' => '#ffffff']          // Purple
                                ];
                                
                                // Get and normalize role
                                $rawRole = $member->role ?? '';
                                $normalizedRole = strtolower(trim($rawRole));
                                
                                // Get colors
                                $colors = $roleStyles[$normalizedRole] ?? ['bg' => '#6c757d', 'text' => '#ffffff'];
                                
                                // Format display name
                                $displayRole = ucwords(str_replace('_', ' ', $rawRole));
                            @endphp
                            @if($member->role)
                                <span class="role-badge" style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; padding: 0.5rem 1rem; border-radius: 50rem; font-size: 0.8rem; font-weight: 600; display: inline-block; min-width: 100px; text-align: center;">
                                    {{ $displayRole }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $member->email }}</td>
                        <td class="text-muted">{{ $member->contact_number ?? '-' }}</td>
                        <td class="text-center">
                            @if(optional($member)->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admin.staff.info', ['id' => $member->id])}}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.staff.edit', ['id' => $member->id])}}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.staff.destroy', $member->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block text-muted opacity-25"></i>
                            <p class="mb-0">No staff members found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($staff) && method_exists($staff, 'links'))
        <div class="mt-4">
            {!! $staff->links() !!}
        </div>
        @endif
    </div>
</div>

<style>
    /* Table styling */
    .table > :not(caption) > * > * {
        padding: 1.2rem 1rem;
        vertical-align: middle;
    }
    
    .table thead th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Button styling */
    .btn-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    /* Card styling */
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    /* Role badge hover effect */
    .role-badge {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .role-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
</style>

@endsection