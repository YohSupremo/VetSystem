@extends('admin.dashboard')

@section('page-title', 'Dispensing History')
@section('page-description', 'View history of all medication dispensing')

@push('styles')
<style>
    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .table-responsive {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .dispensing-row {
        transition: background-color 0.2s;
    }

    .dispensing-row:hover {
        background-color: #f8f9fa !important;
    }

    .patient-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .medication-badge {
        background: linear-gradient(135deg, #FF8C42 0%, #FF6B9D 100%);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .amount-badge {
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .btn-mark-paid {
        padding: 0.22rem 0.45rem !important;
        line-height: 1;
        border-radius: 0.35rem !important;
    }

    .btn-mark-paid i {
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-history"></i> Dispensing History</h1>
        <p>View all medication dispensing records</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Pharmacy
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <label for="medication_id" class="form-label">Medication</label>
            <select name="medication_id" id="medication_id" class="form-select">
                <option value="">All Medications</option>
                @foreach($medications as $medication)
                    <option value="{{ $medication->id }}" {{ request('medication_id') == $medication->id ? 'selected' : '' }}>
                        {{ $medication->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="date_from" class="form-label">From Date</label>
            <input type="date" class="form-control" id="date_from" name="date_from"
                   value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
            <label for="date_to" class="form-label">To Date</label>
            <input type="date" class="form-control" id="date_to" name="date_to"
                   value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Dispensing Records Table -->
<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Date & Time</th>
                <th>Patient</th>
                <th>Medication</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Dispensed By</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dispensingRecords as $record)
                <tr class="dispensing-row">
                    @php
                        $isDispensingTable = ($historySource ?? null) === 'medication_dispensing';
                        $item = $isDispensingTable ? $record->inventoryItem : $record->stock?->inventoryItem;
                        $quantity = $isDispensingTable ? $record->quantity_dispensed : $record->quantity;
                        $unitPrice = (float) ($isDispensingTable ? ($record->unit_price ?? 0) : ($item->unit_price ?? 0));
                        $pet = $isDispensingTable ? $record->prescription?->medicalRecord?->pet : null;
                        $ownerUser = $pet?->owner?->user;
                        $displayDate = $record->dispensed_at ?? $record->transaction_date ?? $record->created_at;
                        $invoice = $isDispensingTable ? $record->invoice : null;
                        $paymentStatus = 'unpaid';
                        if ($invoice) {
                            $paymentStatus = $invoice->is_paid
                                ? 'paid'
                                : ((float) $invoice->paid_amount > 0 ? 'partial' : 'unpaid');
                        }
                    @endphp
                    <td>
                        @if($displayDate)
                            <strong>{{ $displayDate->format('M j, Y') }}</strong><br>
                            <small class="text-muted">{{ $displayDate->format('g:i A') }}</small>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        <div class="patient-info">
                            <i class="fas fa-paw text-primary"></i>
                            <div>
                                <strong>{{ $pet?->name ?? 'N/A' }}</strong><br>
                                @if($ownerUser)
                                    <small class="text-muted">{{ trim(($ownerUser->first_name ?? '') . ' ' . ($ownerUser->last_name ?? '')) }}</small>
                                @else
                                    <small class="text-muted">No linked patient in transaction history</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="medication-badge">{{ $item->name ?? 'N/A' }}</span>
                        @if($item?->sku)
                            <br><small class="text-muted">SKU: {{ $item->sku }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $quantity }}</span>
                    </td>
                    <td>
                        @if($unitPrice > 0)
                            <span class="amount-badge">₱{{ number_format($quantity * $unitPrice, 2) }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($isDispensingTable)
                            {{ $record->dispensedBy ? $record->dispensedBy->first_name . ' ' . $record->dispensedBy->last_name : 'N/A' }}
                        @else
                            {{ $record->performedBy ? $record->performedBy->first_name . ' ' . $record->performedBy->last_name : 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($paymentStatus === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($paymentStatus === 'partial')
                            <span class="badge bg-warning text-dark">Partial</span>
                        @elseif($paymentStatus === 'unpaid' && $isDispensingTable)
                            <span class="badge bg-danger">Dispensed / Unpaid</span>
                        @elseif($paymentStatus === 'unpaid')
                            <span class="badge bg-danger">Unpaid</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group" style="gap:.35rem;">
                            @if($isDispensingTable && $paymentStatus !== 'paid')
                                <form method="POST" action="{{ route('admin.pharmacy.dispensing.mark-paid', $record->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success btn-mark-paid" title="Mark Paid">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </button>
                                </form>
                            @endif
                            @if($item?->id)
                                <a href="{{ route('admin.pharmacy.show', $item->id) }}"
                                   class="btn btn-sm btn-outline-primary" title="View Medication">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="No medication record">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No dispensing records found</p>
                        <p class="text-muted">Dispensing records will appear here once medications are dispensed.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($dispensingRecords->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $dispensingRecords->appends(request()->query())->links() }}
    </div>
@endif

<!-- Summary Statistics -->
@if($dispensingRecords->count() > 0)
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">{{ $dispensingRecords->total() }}</h5>
                    <p class="card-text">Total Dispensing Records</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">{{ $dispensingRecords->sum(($historySource ?? null) === 'medication_dispensing' ? 'quantity_dispensed' : 'quantity') }}</h5>
                    <p class="card-text">Total Units Dispensed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        {{ $dispensingRecords->map(fn($r) => $r->medicalRecord?->pet?->owner_id)->unique()->filter()->count() }}
                    </h5>
                    <p class="card-text">Unique Patients (this page)</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
function printReceipt(dispensingId) {
    // Open a new window for printing
    const printWindow = window.open('', '_blank', 'width=600,height=800');

    // You would typically make an AJAX call here to get the receipt data
    // For now, we'll show a simple message
    printWindow.document.write(`
        <html>
        <head>
            <title>Receipt #${dispensingId}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .receipt-details { margin: 20px 0; }
                .total { font-weight: bold; font-size: 1.2em; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>VetSystem Pharmacy</h2>
                <p>Receipt #${dispensingId}</p>
            </div>
            <div class="receipt-details">
                <p>Receipt printing functionality would be implemented here.</p>
                <p>This would include medication details, quantity, price, and patient information.</p>
            </div>
            <div class="total">
                <p>Thank you for your business!</p>
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush