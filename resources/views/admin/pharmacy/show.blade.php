@extends('admin.dashboard')

@push('styles')
<style>
    .show-container {
        max-width: 900px;
        margin: 2rem auto;
    }
    .detail-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
    }
    .page-header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
    }
    .page-header p {
        color: #6c757d;
        margin: 0;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .detail-item {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #4e73df;
    }
    .detail-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.15rem;
    }
    .detail-value {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .stock-info {
        background: #e7f3ff;
        border-left: 4px solid #0056b3;
    }
    .low-stock {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
    }
    .expired {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
    }
    .actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }
    .btn {
        border-radius: 8px;
        padding: 0.6rem 1.4rem;
        border: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-primary { background: linear-gradient(135deg,#4e73df,#224abe); color:#fff; }
    .btn-secondary { background:#6c757d; color:#fff; }
    .btn-danger { background:#dc3545; color:#fff; }
    .transactions-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    .transactions-table th {
        background: #4e73df;
        color: white;
        padding: 0.75rem;
        text-align: left;
    }
    .transactions-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #dee2e6;
    }
    .transactions-table tr:hover {
        background: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="show-container">
    <div class="page-header">
        <h1><i class="fas fa-capsules"></i> Medication Details</h1>
        <p>View information about this medicine.</p>
    </div>

    <div class="detail-card">
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Name</div>
                <div class="detail-value">{{ $medication->name }}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">SKU</div>
                <div class="detail-value">{{ $medication->sku ?? 'Not set' }}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Unit Price</div>
                <div class="detail-value">${{ number_format($medication->unit_price, 2) }}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Category</div>
                <div class="detail-value">{{ ucfirst($medication->category) }}</div>
            </div>
        </div>
        
        @if($medication->description)
        <div style="margin-bottom: 1.5rem;">
            <div class="detail-label">Description</div>
            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                {{ $medication->description }}
            </div>
        </div>
        @endif
    </div>

    <!-- Stock Information -->
    <div class="detail-card">
        <h3 style="margin-bottom: 1rem;"><i class="fas fa-boxes"></i> Stock Information</h3>
        <div class="detail-grid">
            @foreach($medication->inventoryStocks as $stock)
            <div class="detail-item {{ $stock->quantity <= $stock->min_stock ? 'low-stock' : '' }} {{ $stock->expiry_date && $stock->expiry_date->isPast() ? 'expired' : '' }}">
                <div class="detail-label">Stock #{{ $loop->iteration }}</div>
                <div class="detail-value">
                    <div>Quantity: {{ $stock->quantity }}</div>
                    <div>Min Stock: {{ $stock->min_stock }}</div>
                    @if($stock->expiry_date)
                    <div>Expires: {{ $stock->expiry_date->format('M d, Y') }}</div>
                    @endif
                    @if($stock->location)
                    <div>Location: {{ $stock->location }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Transactions -->
    @if($recentTransactions->count() > 0)
    <div class="detail-card">
        <h3 style="margin-bottom: 1rem;"><i class="fas fa-history"></i> Recent Transactions</h3>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Reference</th>
                    <th>Performed By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('M d, Y h:i A') }}</td>
                    <td>
                        <span class="badge" style="
                            background: {{ $transaction->type == 'in' ? '#28a745' : ($transaction->type == 'out' ? '#dc3545' : '#ffc107') }};
                            color: white;
                            padding: 0.25rem 0.5rem;
                            border-radius: 0.25rem;
                            font-size: 0.75rem;
                        ">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td>{{ $transaction->quantity }}</td>
                    <td>{{ $transaction->reference ?? 'N/A' }}</td>
                    <td>{{ $transaction->performedBy ? $transaction->performedBy->name : 'System' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="actions">
        <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <a href="{{ route('admin.pharmacy.edit', $medication->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form method="POST" action="{{ route('admin.pharmacy.destroy', $medication->id) }}" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this medication?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.delete-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var ok = confirm('Are you sure you want to delete this medication?');
            if (!ok) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush
@endsection

