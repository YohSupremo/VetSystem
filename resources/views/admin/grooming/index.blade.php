@extends('admin.dashboard')

@section('page-title', 'Grooming Services')
@section('page-description', 'Manage grooming appointments and services')

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

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .dashboard-card {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: #fff;
        font-size: 1.25rem;
    }

    .card-info h3 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 700;
    }

    .card-info p {
        margin: 0.25rem 0 0;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .content-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .section-header h2 {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }

    .section-header h2 i {
        margin-right: 0.5rem;
        color: #9c27b0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #9c27b0 0%, #6a1b9a 100%);
        border: none;
        padding: 0.6rem 1.4rem;
        border-radius: 8px;
        font-weight: 500;
        color: #fff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary:hover {
        color: #fff;
    }

    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
    }

    .data-table thead th {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6c757d;
        border-bottom: 1px solid #e9ecef;
        text-align: left;
        white-space: nowrap;
    }

    .data-table tbody tr {
        background: #f9fafb;
    }

    .data-table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .pet-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pet-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-status-scheduled { background: #e3f2fd; color: #1565c0; }
    .badge-status-in_progress { background: #fff3cd; color: #856404; }
    .badge-status-completed { background: #d4edda; color: #155724; }
    .badge-status-cancelled { background: #f8d7da; color: #721c24; }
    .badge-payment-paid { background: #d4edda; color: #155724; }
    .badge-payment-partial { background: #fff3cd; color: #856404; }
    .badge-payment-unpaid { background: #f8d7da; color: #721c24; }
    .badge-payment-unbilled { background: #e2e3e5; color: #495057; }

    .actions {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        color: var(--light-text);
    }

    .btn-icon:hover {
        background: var(--paw-medium);
        color: var(--primary-orange);
    }

    .btn-icon.text-danger:hover {
        color: #c82333;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-cut"></i> Grooming Appointments</h1>
        <p>Manage all grooming visits in one place</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('admin.grooming-services.index') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);">
            <i class="fas fa-spa"></i> Manage Services
        </a>
        <a href="{{ route('admin.grooming.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Grooming
        </a>
    </div>
</div>



@if($errors->has('error'))
    <div class="alert alert-danger" style="margin-bottom: 1rem;">
        {{ $errors->first('error') }}
    </div>
@endif

<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-icon" style="background: linear-gradient(135deg,#9c27b0,#6a1b9a);">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="card-info">
            <h3>{{ $todayAppointments ?? 0 }}</h3>
            <p>Today&apos;s Groomings</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: linear-gradient(135deg,#4caf50,#388e3c);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-info">
            <h3>{{ $completedAppointments ?? 0 }}</h3>
            <p>Completed Overall</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: linear-gradient(135deg,#ff9800,#f57c00);">
            <i class="fas fa-spa"></i>
        </div>
        <div class="card-info">
            <h3>{{ isset($servicesCount) ? $servicesCount : 0 }}</h3>
            <p>Available Services</p>
        </div>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-list"></i> All Grooming Appointments</h2>
        <a href="{{ route('admin.grooming-services.index') }}" style="color: #9c27b0; text-decoration: none; font-weight: 500;">
            <i class="fas fa-spa"></i> Manage Services
        </a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Service</th>
                    <th>Groomer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Tax</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groomingAppointments as $groomingAppointment)
                    @php $appointment = $groomingAppointment->appointment; @endphp
                    @php $isVirtual = (bool) $groomingAppointment->getAttribute('is_virtual'); @endphp
                    <tr>
                        <td>
                            {{ $appointment && $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : 'N/A' }}
                        </td>
                        <td>
                            <div class="pet-info">
                                @php $pet = $appointment->pet ?? null; @endphp
                                <img src="{{ $pet ? $pet->photo_url : 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\" viewBox=\"0 0 200 200\"><rect fill=\"#f0f0f0\" width=\"200\" height=\"200\"/><text x=\"50%\" y=\"50%\" font-size=\"80\" text-anchor=\"middle\" dominant-baseline=\"middle\" fill=\"#ccc\">🐾</text></svg>') }}"
                                     alt="{{ $pet->name ?? 'Pet' }}" class="pet-avatar" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+PHJlY3QgZmlsbD0iI2YwZjBmMCIgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1zaXplPSI4MCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iI2NjYyI+8J+QrjwvdGV4dD48L3N2Zz4='">
                                <span>{{ $pet->name ?? 'Unknown Pet' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $ownerUser = optional(optional($pet)->owner)->user;
                            @endphp
                            @if($ownerUser)
                                {{ $ownerUser->first_name }} {{ $ownerUser->last_name }}
                            @else
                                Unknown Owner
                            @endif
                        </td>
                        <td>
                            {{ $groomingAppointment->service->service_name ?? 'N/A' }}
                        </td>
                        <td>
                            @if($groomingAppointment->groomer)
                                {{ $groomingAppointment->groomer->first_name }} {{ $groomingAppointment->groomer->last_name }}
                            @elseif($isVirtual && $appointment && $appointment->veterinarian && $appointment->veterinarian->role === 'groomer')
                                {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}
                            @else
                                Not assigned
                            @endif
                        </td>
                        <td>
                            @php
                                $status = $groomingAppointment->status ?? 'scheduled';
                            @endphp
                            <span class="badge badge-status-{{ $status }}">
                                {{ ucfirst(str_replace('_',' ', $status)) }}
                            </span>
                        </td>
                        <td>
                            @if(!is_null($groomingAppointment->getAttribute('invoice_total')))
                                ₱{{ number_format((float) $groomingAppointment->getAttribute('invoice_total'), 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php $invoiceTax = $groomingAppointment->getAttribute('invoice_tax'); @endphp
                            @if(!is_null($invoiceTax) && (float) $invoiceTax > 0)
                                ₱{{ number_format((float) $invoiceTax, 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $paymentStatus = $groomingAppointment->getAttribute('payment_status') ?? 'unbilled';
                            @endphp
                            <span class="badge badge-payment-{{ $paymentStatus }}">
                                {{ ucfirst($paymentStatus) }}
                            </span>
                        </td>
                        <td class="actions">
                            @if(!$isVirtual)
                                <a href="{{ route('admin.grooming.show', $groomingAppointment->id) }}" class="btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.grooming.edit', $groomingAppointment->id) }}" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(($groomingAppointment->getAttribute('payment_status') ?? 'unbilled') !== 'paid')
                                    <form method="POST" action="{{ route('admin.grooming.mark-paid', $groomingAppointment->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-icon" title="Mark Paid">
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.grooming.destroy', $groomingAppointment->id) }}" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @elseif($appointment)
                                <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn-icon" title="View Appointment">
                                    <i class="fas fa-calendar-check"></i>
                                </a>
                                <a href="{{ route('admin.grooming.complete', $appointment->id) }}" class="btn-icon" title="Complete Grooming Details">
                                    <i class="fas fa-clipboard-list"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No grooming appointments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('.delete-form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var ok = confirm('Are you sure you want to delete this grooming appointment?');
            if (!ok) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection
