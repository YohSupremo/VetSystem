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
    }
    .prescription-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 140, 66, 0.1);
    }
    .prescription-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .prescription-title h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    .prescription-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .prescription-actions {
        display: flex;
        gap: 0.5rem;
    }
    .btn {
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.4rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-primary {
        background: linear-gradient(135deg, #FF8C42 0%, #FF6B1B 100%);
        color: #fff;
        border: none;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
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
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
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
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.1);
    }
    .medication-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .medication-title h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    .medication-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #6c757d;
    }
    .medication-stock {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        border-left: 4px solid #28a745;
    }
    .stock-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stock-quantity {
        font-size: 1.5rem;
        font-weight: 700;
        color: #28a745;
    }
    .stock-status {
        font-size: 0.9rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }
    .status-available {
        background: #d4edda;
        color: #155724;
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
                        <div>
                            <h3>{{ $prescription->medication_name }}</h3>
                            <div class="prescription-meta">
                                <span><i class="fas fa-user"></i> {{ $prescription->getPrescribedByAttribute()?->first_name ?? 'N/A' }} {{ $prescription->getPrescribedByAttribute()?->last_name ?? '' }}</span>
                                <span><i class="fas fa-paw"></i> {{ $prescription->getPetAttribute()?->name ?? 'Unknown Pet' }}</span>
                                <span><i class="fas fa-calendar"></i> {{ $prescription->created_at->format('M j, Y') }}</span>
                            </div>
                        </div>
                        <div class="prescription-actions">
                            <form method="POST" action="{{ route('admin.pharmacy.dispense.store') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-hand-holding-medical"></i> Dispense
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="medication-info">
                        <div><strong>Dosage:</strong> {{ $prescription->dosage }}</div>
                        <div><strong>Frequency:</strong> {{ $prescription->frequency }}</div>
                        <div><strong>Duration:</strong> {{ $prescription->duration_days }} days</div>
                        <div><strong>Quantity:</strong> {{ $prescription->quantity }}</div>
                        <div><strong>Instructions:</strong> {{ $prescription->instructions }}</div>
                    </div>
                </div>
            @endforeach
        @endforelse
    </div>

    <div class="medication-grid">
        <h3 class="mb-3"><i class="fas fa-capsules"></i> Available Medications</h3>
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
                        <h4>{{ $medication->name }}</h4>
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
                        <div><strong>SKU:</strong> {{ $medication->sku ?? 'N/A' }}</div>
                        <div><strong>Price:</strong> ${{ number_format($medication->unit_price, 2) }}</div>
                        <div><strong>Location:</strong> {{ $medication->inventoryStocks->first()->location ?? 'N/A' }}</div>
                    </div>
                </div>
            @endforeach
        @endforelse
    </div>
</div>
@endsection
