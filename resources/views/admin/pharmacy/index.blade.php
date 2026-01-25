@extends('admin.dashboard')

@push('styles')
<style>
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .header-title h1 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0 0 0.5rem;
        color: #2c3e50;
    }
    
    .header-title p {
        color: #6c757d;
        margin: 0;
    }
    
    .header-title h1 i {
        margin-right: 0.75rem;
        color: #4e73df;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        color: white;
        text-decoration: none;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        color: white;
    }
    
    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 6px;
    }
    
    .btn-outline-primary {
        color: #4e73df;
        border: 1px solid #4e73df;
        background: transparent;
    }
    
    .btn-outline-primary:hover {
        background: #4e73df;
        color: white;
    }
    
    .btn-outline-danger {
        color: #e74a3b;
        border: 1px solid #e74a3b;
        background: transparent;
    }
    
    .btn-outline-danger:hover {
        background: #e74a3b;
        color: white;
    }
    
    .action-buttons .btn {
        margin: 0 0.15rem;
    }
    
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid #e3e6f0;
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0 !important;
    }
    
    .card-body {
        padding: 0;
    }
    
    .table {
        width: 100%;
        margin-bottom: 0;
        color: #5a5c69;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        color: #6c757d;
        padding: 1rem 1.5rem;
        background-color: #f8f9fc;
        border: none;
        white-space: nowrap;
    }
    
    .table td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-top: 1px solid #e3e6f0;
        transition: background-color 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    .table tbody tr:last-child td {
        border-bottom: 1px solid #e3e6f0;
    }
    
    .badge {
        padding: 0.4em 0.8em;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-success { background-color: rgba(28, 200, 138, 0.15); color: #1cc88a; }
    .badge-warning { background-color: rgba(246, 194, 62, 0.15); color: #f6c23e; }
    .badge-danger { background-color: rgba(231, 74, 59, 0.15); color: #e74a3b; }
    
    .search-box {
        position: relative;
        width: 300px;
    }
    
    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #b7b9cc;
    }
    
    .search-input {
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        outline: none;
    }
    
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    .status-available { background-color: #1cc88a; }
    .status-low { background-color: #f6c23e; }
    .status-out { background-color: #e74a3b; }
    
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d3e2;
        margin-bottom: 1rem;
        display: block;
    }
    
    .empty-state h4 {
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-capsules"></i> Pharmacy Management</h1>
        <p>Manage medications and pharmaceutical inventory with ease</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.pharmacy.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Medication
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Medication Inventory</h5>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search medications..." onkeyup="filterTable('medicationsTable', this.value)">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="medicationsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Medication Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-pills"></i>
                                <h4>No medications found</h4>
                                <p>Get started by adding a new medication to your inventory</p>
                                <a href="{{ route('admin.pharmacy.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Medication
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Low Stock Alerts</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <h4>No low stock items</h4>
                    <p>All your medications are well-stocked</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-exchange-alt text-info"></i>
                    <h4>No recent transactions</h4>
                    <p>Your transaction history will appear here</p>
                </div>
            </div>
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
            const cells = row.getElementsByTagName('td');
            let shouldShow = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell.textContent.toLowerCase().indexOf(query.toLowerCase()) > -1) {
                    shouldShow = true;
                    break;
                }
            }
            
            row.style.display = shouldShow ? '' : 'none';
        }
    }
    
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this medication? This action cannot be undone.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection
