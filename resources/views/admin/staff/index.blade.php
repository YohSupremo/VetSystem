@extends('admin.dashboard')

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-user-md"></i> Staff Management</h1>
        <p>Manage veterinarians, technicians, and staff members</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Staff Member
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center text-muted">No staff members found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
