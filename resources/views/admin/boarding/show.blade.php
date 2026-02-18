@extends('admin.dashboard')

@section('page-title', 'Boarding Details')
@section('page-description', 'View boarding reservation details')

@push('styles')
<style>
    .show-container {
        max-width: 900px;
        margin: 2rem auto;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header p {
        color: #6c757d;
        margin: 0;
    }

    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .detail-section {
        margin-bottom: 2rem;
    }

    .detail-section:last-child {
        margin-bottom: 0;
    }

    .detail-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #4e73df;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-section-title i {
        color: #4e73df;
    }

    .detail-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 1.5rem;
    }

    .detail-group {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #4e73df;
    }

    .detail-label {
        display: block;
        font-weight: 500;
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }

    .detail-value {
        display: block;
        font-size: 1.1rem;
        color: #2c3e50;
        font-weight: 600;
        word-break: break-word;
    }

    .detail-value.text {
        font-weight: 400;
        font-size: 1rem;
        line-height: 1.6;
        color: #495057;
    }

    .pet-preview {
        background: #f8f9fa;
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .pet-preview-item {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .pet-preview-image {
        width: 100px;
        height: 100px;
        border-radius: 8px;
        object-fit: cover;
        background: white;
        border: 2px solid #d1d3e2;
    }

    .pet-preview-info {
        flex: 1;
    }

    .pet-preview-info h3 {
        margin: 0 0 0.75rem;
        color: #2c3e50;
        font-size: 1.3rem;
    }

    .pet-preview-info p {
        margin: 0.5rem 0;
        color: #6c757d;
        font-size: 0.95rem;
    }

    .breadcrumb {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .breadcrumb a {
        color: #4e73df;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb a:hover {
        color: #224abe;
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: #6c757d;
    }

    .breadcrumb-current {
        color: #6c757d;
    }

    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-upcoming {
        background: #fff3cd;
        color: #856404;
    }

    .status-completed {
        background: #e2e3e5;
        color: #383d41;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.75rem;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        color: white;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        color: white;
    }

    .delete-form {
        display: inline;
    }

    .empty-state {
        padding: 1.5rem;
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        color: #856404;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="show-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.boarding.index') }}"><i class="fas fa-home"></i> Boardings</a>
        <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
        <span class="breadcrumb-current">Boarding #{{ $boarding->id ?? 'N/A' }}</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-eye"></i> Boarding Details
        </h1>
        <p>View complete boarding information</p>
    </div>

    <!-- Pet Information Card -->
    <div class="detail-card">
        <div class="detail-section-title">
            <i class="fas fa-paw"></i> Pet Information
        </div>

        <div class="pet-preview">
            <div class="pet-preview-item">
                @php $pet = $boarding->pet; @endphp
                <img src="{{ $pet && $pet->photo_path ? $pet->photo_url : asset('images/default-pet.svg') }}" alt="Pet" class="pet-preview-image">
                <div class="pet-preview-info">
                    <h3>{{ $pet?->name ?? 'N/A' }}</h3>
                    <p><strong>Breed:</strong> {{ $pet?->breed ?? 'N/A' }}</p>
                    <p><strong>Species:</strong> {{ $pet?->species ? ucfirst($pet->species) : 'N/A' }}</p>
                    @php
                        $ownerUser = optional(optional($pet)->owner)->user;
                    @endphp
                    <p><strong>Owner:</strong>
                        @if($ownerUser)
                            {{ $ownerUser->first_name }} {{ $ownerUser->last_name }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Boarding Information Card -->
    <div class="detail-card">
        <div class="detail-section-title">
            <i class="fas fa-calendar-alt"></i> Boarding Information
        </div>

        <div class="detail-row">
            <div class="detail-group">
                <span class="detail-label">Check-in Date</span>
                <span class="detail-value">
                    @if($boarding->start_date)
                        {{ \Carbon\Carbon::parse($boarding->start_date)->format('M d, Y') }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Check-out Date</span>
                <span class="detail-value">
                    @if($boarding->end_date)
                        {{ \Carbon\Carbon::parse($boarding->end_date)->format('M d, Y') }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Check-in Time</span>
                <span class="detail-value">
                    {{ $boarding->check_in_time ? \Carbon\Carbon::parse($boarding->check_in_time)->format('g:i A') : 'Not set' }}
                </span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Check-out Time</span>
                <span class="detail-value">
                    {{ $boarding->check_out_time ? \Carbon\Carbon::parse($boarding->check_out_time)->format('g:i A') : 'Not set' }}
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-group">
                <span class="detail-label">Days Boarded</span>
                <span class="detail-value">
                    @php
                        $days = null;
                        if ($boarding->start_date && $boarding->end_date) {
                            $days = \Carbon\Carbon::parse($boarding->end_date)
                                ->diffInDays(\Carbon\Carbon::parse($boarding->start_date)) + 1;
                        }
                    @endphp
                    {{ $days !== null ? $days . ' day' . ($days === 1 ? '' : 's') : 'N/A' }}
                </span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Status</span>
                <div>
                    @php
                        if ($boarding->isUpcoming()) {
                            $statusText = 'Upcoming';
                            $statusClass = 'upcoming';
                        } elseif ($boarding->isCompleted()) {
                            $statusText = 'Completed';
                            $statusClass = 'completed';
                        } else {
                            $statusText = 'Active';
                            $statusClass = 'active';
                        }
                    @endphp
                    <span class="status-badge status-{{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-group">
                <span class="detail-label">Daily Rate</span>
                <span class="detail-value">{{ $boarding->daily_rate !== null ? number_format($boarding->daily_rate, 2) : 'N/A' }}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Notes</span>
                <span class="detail-value text">{{ $boarding->notes ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Cage Assignment Card -->
    <div class="detail-card">
        <div class="detail-section-title">
            <i class="fas fa-home"></i> Cage Assignment
        </div>

        <div class="detail-row">
            <div class="detail-group">
                <span class="detail-label">Cage Code</span>
                <span class="detail-value">{{ $boarding->cage?->cage_code ?? 'N/A' }}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Location</span>
                <span class="detail-value">{{ $boarding->cage?->location ?? 'N/A' }}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Status</span>
                <span class="detail-value">{{ $boarding->cage?->status ? ucfirst($boarding->cage->status) : 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Medication Notes Card -->
    <div class="detail-card">
        <div class="detail-section-title">
            <i class="fas fa-pills"></i> Medication Notes
        </div>

        <div class="detail-group">
            <span class="detail-label">Medication Instructions</span>
            @if($boarding->medication_instructions)
                <span class="detail-value text">{{ $boarding->medication_instructions }}</span>
            @else
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i> No medication instructions provided
                </div>
            @endif
        </div>

        <div class="detail-group" style="margin-top: 1rem;">
            <span class="detail-label">Medication Times</span>
            <span class="detail-value text">{{ $boarding->medication_times ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Feeding Schedule Card -->
    <div class="detail-card">
        <div class="detail-section-title">
            <i class="fas fa-utensils"></i> Feeding Schedule
        </div>

        @php
            $times = $boarding->feeding_times ? explode(',', $boarding->feeding_times) : [];
            $morningTime = $times[0] ?? null;
            $afternoonTime = $times[1] ?? null;
            $eveningTime = $times[2] ?? null;
        @endphp
        <div class="detail-row">
            <div class="detail-group">
                <span class="detail-label">Morning Feed Time</span>
                <span class="detail-value">{{ $morningTime ?? 'Not scheduled' }}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Afternoon Feed Time</span>
                <span class="detail-value">{{ $afternoonTime ?? 'Not scheduled' }}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Evening Feed Time</span>
                <span class="detail-value">{{ $eveningTime ?? 'Not scheduled' }}</span>
            </div>
        </div>

        <div class="detail-group">
            <span class="detail-label">Feeding Notes</span>
            <span class="detail-value text">{{ $boarding->special_diet_notes ?? 'No notes provided.' }}</span>
        </div>
    </div>

    <!-- Billing Card -->
    <div class="detail-card">
        <div class="detail-section-title">
            <i class="fas fa-file-invoice-dollar"></i> Billing & Payment
        </div>

        @if($invoice)
            <div class="detail-row">
                <div class="detail-group">
                    <span class="detail-label">Invoice Number</span>
                    <span class="detail-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Invoice Status</span>
                    <span class="detail-value">{{ ucfirst($invoice->status) }}</span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value">₱{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-group">
                    <span class="detail-label">Paid Amount</span>
                    <span class="detail-value">₱{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Balance</span>
                    <span class="detail-value">₱{{ number_format($invoice->balance, 2) }}</span>
                </div>
                <div class="detail-group">
                    <span class="detail-label">Due Date</span>
                    <span class="detail-value">{{ optional($invoice->due_date)->format('M d, Y') }}</span>
                </div>
            </div>

            @if(!$invoice->is_paid)
                <form method="POST" action="{{ route('admin.boarding.payment.process', $boarding->id) }}">
                    @csrf
                    <div class="detail-row">
                        <div class="detail-group">
                            <label class="detail-label" for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                                <option value="mobile_payment">Mobile Payment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="detail-group">
                            <label class="detail-label" for="amount">Amount (leave blank to pay full balance)</label>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" max="{{ $invoice->balance }}" class="form-control" value="{{ old('amount') }}">
                        </div>
                        <div class="detail-group">
                            <label class="detail-label" for="reference_number">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{ old('reference_number') }}">
                        </div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label" for="notes">Payment Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    <div class="actions" style="margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-money-check-alt"></i> Record Payment
                        </button>
                    </div>
                </form>
            @else
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i> This boarding invoice is fully paid.
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-info-circle"></i> No invoice has been generated yet for this boarding.
            </div>
            <div class="actions" style="margin-top: 1rem;">
                <form method="POST" action="{{ route('admin.boarding.invoice.generate', $boarding->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-invoice"></i> Generate Invoice
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="actions">
        <a href="{{ route('admin.boarding.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Boardings
        </a>
        <a href="{{ route('admin.boarding.edit', $boarding->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Boarding
        </a>
        <form method="POST" action="{{ route('admin.boarding.destroy', $boarding->id) }}" class="delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete Boarding
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
            var ok = confirm('Are you sure you want to delete this boarding record? This action cannot be undone.');
            if (!ok) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush
@endsection
