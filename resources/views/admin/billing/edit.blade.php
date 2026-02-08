@extends('admin.dashboard')

@section('page-title', 'Edit Invoice')
@section('page-description', 'Update invoice details')

@section('content')
<style>
    .billing-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .billing-form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }

    .billing-section-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
    }

    .items-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
    }

    .items-table td {
        vertical-align: middle;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.65rem 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15);
    }
</style>

<div class="content-header billing-hero">
    <div class="header-title">
        <h1><i class="fas fa-file-invoice"></i> Edit Invoice</h1>
        <p>Update invoice {{ $invoice->invoice_number }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.billing.show', $invoice->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-eye"></i> View Invoice
        </a>
        <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary ms-2">
            <i class="fas fa-arrow-left"></i> Back to Billing
        </a>
    </div>
</div>

<form action="{{ route('admin.billing.update', $invoice->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="billing-form-card">
        <h5 class="billing-section-title">Invoice Details</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="pet_owner_id" class="form-label">Pet Owner</label>
                <select name="pet_owner_id" id="pet_owner_id" class="form-select" required>
                    <option value="">Select owner</option>
                    @foreach($petOwners as $owner)
                        <option value="{{ $owner->id }}" {{ old('pet_owner_id', $invoice->pet_owner_id) == $owner->id ? 'selected' : '' }}>
                            {{ $owner->full_name ?? ('Owner #' . $owner->id) }}
                        </option>
                    @endforeach
                </select>
                @error('pet_owner_id')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="pet_id" class="form-label">Pet</label>
                <select name="pet_id" id="pet_id" class="form-select" required>
                    <option value="">Select pet</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ old('pet_id', $invoice->pet_id) == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }}
                        </option>
                    @endforeach
                </select>
                @error('pet_id')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="invoice_date" class="form-label">Invoice Date</label>
                <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date?->toDateString()) }}" required>
                @error('invoice_date')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $invoice->due_date?->toDateString()) }}" required>
                @error('due_date')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2 mb-3">
                <label for="tax_amount" class="form-label">Tax</label>
                <input type="number" class="form-control" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', $invoice->tax_amount) }}" min="0" step="0.01">
            </div>
            <div class="col-md-2 mb-3">
                <label for="discount_amount" class="form-label">Discount</label>
                <input type="number" class="form-control" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount) }}" min="0" step="0.01">
            </div>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
        </div>
    </div>

    <div class="billing-form-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="billing-section-title mb-0">Line Items</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-item">
                <i class="fas fa-plus"></i> Add Item
            </button>
        </div>
        <div class="table-responsive">
            <table class="table items-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    @foreach($invoice->invoiceItems as $index => $item)
                        <tr>
                            <td>
                                <select name="items[{{ $index }}][item_type]" class="form-select" required>
                                    @foreach(['consultation','vaccination','surgery','grooming','laboratory','medication','other'] as $type)
                                        <option value="{{ $type }}" {{ $item->item_type === $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item->description }}" required></td>
                            <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control" min="1" value="{{ $item->quantity }}" required></td>
                            <td><input type="number" name="items[{{ $index }}][unit_price]" class="form-control" min="0" step="0.01" value="{{ $item->unit_price }}" required></td>
                            <td class="text-muted">{{ number_format($item->total_price, 2) }}</td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Invoice</button>
    </div>
</form>

<script>
    const itemsBody = document.getElementById('items-body');
    const addItemButton = document.getElementById('add-item');

    function buildRow(index) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${index}][item_type]" class="form-select" required>
                    <option value="consultation">Consultation</option>
                    <option value="vaccination">Vaccination</option>
                    <option value="surgery">Surgery</option>
                    <option value="grooming">Grooming</option>
                    <option value="laboratory">Laboratory</option>
                    <option value="medication">Medication</option>
                    <option value="other">Other</option>
                </select>
            </td>
            <td><input type="text" name="items[${index}][description]" class="form-control" required></td>
            <td><input type="number" name="items[${index}][quantity]" class="form-control" min="1" value="1" required></td>
            <td><input type="number" name="items[${index}][unit_price]" class="form-control" min="0" step="0.01" value="0" required></td>
            <td class="text-muted">Auto</td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-trash"></i></button></td>
        `;
        return row;
    }

    addItemButton.addEventListener('click', () => {
        const index = itemsBody.querySelectorAll('tr').length;
        itemsBody.appendChild(buildRow(index));
    });

    itemsBody.addEventListener('click', (event) => {
        if (event.target.closest('.remove-item')) {
            const row = event.target.closest('tr');
            if (itemsBody.querySelectorAll('tr').length > 1) {
                row.remove();
            }
        }
    });
</script>
@endsection
