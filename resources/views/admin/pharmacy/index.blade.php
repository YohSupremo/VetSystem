@extends('admin.dashboard')

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-capsules"></i> Pharmacy Management</h1>
        <p>Manage medications and pharmaceutical inventory</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.pharmacy.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Medication
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Medication Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center text-muted">No medications found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
