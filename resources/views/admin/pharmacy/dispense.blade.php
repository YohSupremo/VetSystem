@extends('admin.dashboard')

@section('page-title', 'Medication Dispensing')
@section('page-description', 'Dispense medications to patients')

@push('styles')
<style>
    .dispense-container {
        max-width: 900px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.75rem;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .page-header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    .page-header p {
        color: #6c757d;
        margin: 0;
    }
    .prescription-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .prescription-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
        border-left: 4px solid #FF8C42;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    .prescription-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 140, 66, 0.15);
    }
    .prescription-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f3f5;
    }
    .prescription-title h3 {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0 0 0.75rem 0;
        color: #FF8C42;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .prescription-title h3::before {
        content: "💊";
        font-size: 1.2rem;
    }
    .prescription-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
    .prescription-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
    }
    .prescription-meta i {
        color: #FF8C42;
    }
    .prescription-actions {
        display: flex;
        gap: 0.5rem;
    }
    .btn {
        border: none;
        border-radius: 8px;
        padding: 0.7rem 1.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-primary {
        background: linear-gradient(135deg, #FF8C42 0%, #FF6B1B 100%);
        color: #fff;
        border: none;
        box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 140, 66, 0.4);
    }
    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(90, 98, 123, 0.3);
    }
    .status-badge {
        background: #dc3545;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .alert {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        border: 1px solid transparent;
    }
    .alert-info {
        background: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    .alert-warning {
        background: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }
    .medication-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .medication-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
        border-left: 4px solid #4e73df;
        transition: all 0.3s ease;
    }
    .medication-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(78, 115, 223, 0.15);
    }
    .medication-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f3f5;
    }
    .medication-title h4 {
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .medication-title h4::before {
        content: "💊";
        font-size: 1.1rem;
    }
    .medication-info {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
        font-size: 0.9rem;
        color: #495057;
    }
    .medication-info > div {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
        transition: background 0.2s ease;
    }
    .medication-info > div:hover {
        background: #e9ecef;
    }
    .medication-info strong {
        color: #495057;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .prescription-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }
    .prescription-details > div {
        padding: 0.65rem 0.85rem;
        background: #f8f9fa;
        border-radius: 6px;
        border-left: 3px solid #FF8C42;
    }
    .prescription-details strong {
        display: block;
        color: #6c757d;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .prescription-details span {
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .medication-stock {
        background: #d4edda;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-top: 0;
        border-left: 4px solid #28a745;
        display: inline-block;
    }
    .stock-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .stock-quantity {
        font-size: 1.75rem;
        font-weight: 700;
        color: #28a745;
        line-height: 1;
    }
    .stock-status {
        font-size: 0.8rem;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-available {
        background: #28a745;
        color: #fff;
    }
    .status-low {
        background: #fff3cd;
        color: #856404;
    }
    .status-expired {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

@section('content')
<div class="dispense-container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-pills"></i> Medication Dispensing</h1>
            <p>Select a prescription to dispense medication</p>
        </div>
        <div>
            <a href="{{ route('admin.pharmacy.dispensing.history') }}" class="btn btn-primary" style="margin-right: .5rem;">
                <i class="fas fa-history"></i> View History
            </a>
            <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Pharmacy
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="prescription-grid">
        @if($prescriptions->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>No prescriptions found</strong>
                <p>There are no incomplete prescriptions that need to be dispensed.</p>
            </div>
        @elseif($prescriptions->count() === 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No prescriptions available</strong>
                <p>All prescriptions have been completed or no incomplete prescriptions found.</p>
            </div>
        @else
            @foreach($prescriptions as $prescription)
                <div class="prescription-card">
                    <div class="prescription-header">
                        <div class="prescription-title">
                            <h3>{{ $prescription->medication_name }}</h3>
                            <div class="prescription-meta">
                                <span><i class="fas fa-user-md"></i> {{ $prescription->getPrescribedByAttribute()?->first_name ?? 'N/A' }} {{ $prescription->getPrescribedByAttribute()?->last_name ?? '' }}</span>
                                <span><i class="fas fa-paw"></i> {{ $prescription->getPetAttribute()?->name ?? 'Unknown Pet' }}</span>
                                <span><i class="fas fa-calendar"></i> {{ $prescription->created_at->format('M j, Y') }}</span>
                            </div>
                        </div>
                        <div class="prescription-actions">
                            <form method="POST" action="{{ route('admin.pharmacy.dispense.store') }}">
                                @csrf
                                <input type="hidden" name="prescription_id" value="{{ $prescription->id }}">
                                <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap; margin-bottom:0.5rem;">
                                    <select name="inventory_item_id" class="form-select" style="min-width:240px;" required>
                                        <option value="">Select medication</option>
                                        @foreach($medications as $medicationOption)
                                            <option value="{{ $medicationOption->id }}" {{ (string) old('prescription_id') === (string) $prescription->id && (string) old('inventory_item_id') === (string) $medicationOption->id ? 'selected' : '' }}>
                                                {{ $medicationOption->name }} (Stock: {{ $medicationOption->inventoryStocks->sum('quantity') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input
                                        type="number"
                                        name="quantity_dispensed"
                                        class="form-control"
                                        min="1"
                                        style="max-width:120px;"
                                        value="{{ (string) old('prescription_id') === (string) $prescription->id ? old('quantity_dispensed', $prescription->quantity) : $prescription->quantity }}"
                                        required
                                    >
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-hand-holding-medical"></i> Dispense
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="prescription-details">
                        <div>
                            <strong>Dosage</strong>
                            <span>{{ $prescription->dosage }}</span>
                        </div>
                        <div>
                            <strong>Frequency</strong>
                            <span>{{ $prescription->frequency }}</span>
                        </div>
                        <div>
                            <strong>Duration</strong>
                            <span>{{ $prescription->duration_days }} days</span>
                        </div>
                        <div>
                            <strong>Quantity</strong>
                            <span>{{ $prescription->quantity }}</span>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <strong>Instructions</strong>
                            <span>{{ $prescription->instructions }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="medication-grid">
        <div style="grid-column: 1 / -1; margin-bottom: 1rem;">
            <h3 style="font-size: 1.5rem; font-weight: 700; color: #2c3e50; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="fas fa-capsules" style="color: #4e73df;"></i> Available Medications
            </h3>
            <p style="color: #6c757d; margin: 0.5rem 0 0 0; font-size: 0.9rem;">Medications currently in stock and ready to dispense</p>
        </div>
        @if($medications->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No medications available</strong>
                <p>There are no medications in stock to dispense.</p>
            </div>
        @elseif($medications->count() === 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No medications available</strong>
                <p>All medications are out of stock.</p>
            </div>
        @else
            @foreach($medications as $medication)
                <div class="medication-card">
                    <div class="medication-header">
                        <div class="medication-title">
                            <h4>{{ $medication->name }}</h4>
                        </div>
                        <div class="medication-stock">
                            <div class="stock-info">
                                <div class="stock-quantity">{{ $medication->inventoryStocks->sum('quantity') }}</div>
                                <div class="stock-status status-available">
                                    In Stock
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="medication-info">
                        <div><strong>SKU</strong> <span>{{ $medication->sku ?? 'N/A' }}</span></div>
                        <div><strong>Price</strong> <span>${{ number_format($medication->unit_price, 2) }}</span></div>
                        <div><strong>Location</strong> <span>{{ $medication->inventoryStocks->first()->location ?? 'N/A' }}</span></div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
