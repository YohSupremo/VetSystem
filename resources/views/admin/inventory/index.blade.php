@extends('admin.dashboard')

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-boxes"></i> Inventory Management</h1>
        <p>Manage medical supplies and equipment inventory</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Item
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center text-muted">No inventory items found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
