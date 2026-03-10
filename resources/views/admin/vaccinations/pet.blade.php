@extends('admin.dashboard')

@section('page-title', 'Vaccination Records for ' . ($pet->name ?? 'Pet'))
@section('page-description', 'All vaccinations for ' . ($pet->name ?? 'pet'))

@section('content')
<div class="vacc-wrapper">
    {{-- Page Header --}}
    <div class="vacc-header">
        <div class="vacc-header-left">
            <a href="{{ route('admin.vaccinations.index') }}" class="back-link">
                <span class="back-arrow"><i class="fas fa-arrow-left"></i></span>
                <span>All Vaccinations</span>
            </a>
            <div class="vacc-title-block">
                <h2 class="vacc-title">{{ $pet->name ?? 'N/A' }}</h2>
                <div class="vacc-subtitle">
                    <i class="fas fa-user-circle" style="font-size:13px; margin-right:5px; opacity:0.5;"></i>
                    {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}
                </div>
            </div>
        </div>
        <a href="{{ route('admin.vaccinations.create', ['pet_id' => $pet->id]) }}" class="btn-new">
            <i class="fas fa-plus"></i>
            <span>Record New Vaccination</span>
        </a>
    </div>

    {{-- Table Card --}}
    <div class="vacc-card">
        @if($vaccinations->count() > 0)
            <div class="table-scroll">
                <table class="vacc-table">
                    <thead>
                        <tr>
                            <th>Vaccine</th>
                            <th>Vaccinated</th>
                            <th>Next Due</th>
                            <th>Veterinarian</th>
                            <th>Payment Status</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vaccinations as $vaccination)
                            @php $isVirtual = (bool) $vaccination->getAttribute('is_virtual'); @endphp
                            <tr class="vacc-row{{ $isVirtual ? ' vacc-row--virtual' : '' }}">
                                <td>
                                    <div class="vaccine-name">
                                        @if($isVirtual)
                                            <span class="tag-virtual"><i class="fas fa-calendar-alt"></i> Appointment</span>
                                        @else
                                            {{ $vaccination->inventoryItem->name ?? 'N/A' }}
                                        @endif
                                    </div>
                                    @if(!$isVirtual && $vaccination->batch_number)
                                        <div class="batch-label">Batch: {{ $vaccination->batch_number }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="date-chip">
                                        {{ $vaccination->administered_date ? \Carbon\Carbon::parse($vaccination->administered_date)->format('M d, Y') : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="date-chip date-chip--due">
                                        {{ $vaccination->next_due_date ? \Carbon\Carbon::parse($vaccination->next_due_date)->format('M d, Y') : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="vet-name">
                                        @if($isVirtual && $vaccination->appointment && $vaccination->appointment->veterinarian)
                                            Dr. {{ $vaccination->appointment->veterinarian->first_name }} {{ $vaccination->appointment->veterinarian->last_name }}
                                        @else
                                            {{ $vaccination->administeredBy ? 'Dr. ' . $vaccination->administeredBy->first_name . ' ' . $vaccination->administeredBy->last_name : '—' }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if(!$isVirtual)
                                        @php $invoice = $vaccination->getAttribute('billing_invoice'); @endphp
                                        @if($invoice)
                                            <span class="status-badge status-{{ strtolower($invoice->status) }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        @else
                                            <span class="null-val">—</span>
                                        @endif
                                    @else
                                        <span class="null-val">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$isVirtual)
                                        @php $invoice = $vaccination->getAttribute('billing_invoice'); @endphp
                                        @if($invoice)
                                            PHP {{ number_format((float) ($invoice->tax_amount ?? 0), 2) }}
                                        @else
                                            <span class="null-val">—</span>
                                        @endif
                                    @else
                                        <span class="null-val">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$isVirtual)
                                        @php $invoice = $vaccination->getAttribute('billing_invoice'); @endphp
                                        @if($invoice)
                                            PHP {{ number_format((float) ($invoice->total_amount ?? 0), 2) }}
                                        @else
                                            <span class="null-val">—</span>
                                        @endif
                                    @else
                                        <span class="null-val">—</span>
                                    @endif
                                </td>
                                <td class="col-actions">
                                    @if(($showTrash ?? false) && !$isVirtual)
                                        <div class="action-group">
                                            <form action="{{ route('admin.vaccinations.restore', $vaccination->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Restore this vaccination record?');">
                                                @csrf
                                                <button type="submit" class="action-btn action-btn--accept" title="Restore">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif(!$isVirtual)
                                        <div class="action-group">
                                            @php $invoice = $vaccination->getAttribute('billing_invoice'); @endphp
                                            @if($invoice && !$invoice->is_paid && $invoice->status !== 'cancelled')
                                                <form action="{{ route('admin.vaccinations.payment.process', $vaccination->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark this invoice as paid?');">
                                                    @csrf
                                                    <button type="submit" class="action-btn action-btn--pay" title="Mark as Paid">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.vaccinations.show', $vaccination->id) }}" class="action-btn" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.vaccinations.edit', $vaccination->id) }}" class="action-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.vaccinations.destroy', $vaccination->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this vaccination record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn action-btn--danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($vaccination->appointment)
                                        <div class="action-group">
                                            <form action="{{ route('admin.vaccinations.appointments.accept', $vaccination->appointment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Accept this vaccination appointment and continue to create record?');">
                                                @csrf
                                                <button type="submit" class="action-btn action-btn--accept" title="Accept Appointment">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.appointments.show', $vaccination->appointment->id) }}" class="action-btn" title="View Appointment">
                                                <i class="fas fa-calendar-check"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($vaccinations->hasPages())
                <div class="pagination-wrap">
                    {{ $vaccinations->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-syringe"></i></div>
                <p class="empty-msg">No vaccination records found for this pet.</p>
                <a href="{{ route('admin.vaccinations.create', ['pet_id' => $pet->id]) }}" class="btn-new" style="margin-top:4px;">
                    <i class="fas fa-plus"></i>
                    <span>Record one now</span>
                </a>
            </div>
        @endif
    </div>
</div>

<style>
/* ── Layout ──────────────────────────────────────────────────── */
.vacc-wrapper {
    display: flex;
    flex-direction: column;
    gap: 24px;
    padding: 4px 0 32px;
}

/* ── Header ──────────────────────────────────────────────────── */
.vacc-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    flex-wrap: wrap;
}

.vacc-header-left {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: var(--light-text);
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: color 0.18s;
}
.back-link:hover { color: var(--dark-text); }

.back-arrow {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: var(--soft-gray, #f3f4f6);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: background 0.18s;
}
.back-link:hover .back-arrow { background: #e5e7eb; }

.vacc-title-block { display: flex; flex-direction: column; gap: 4px; }

.vacc-title {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: var(--dark-text);
    line-height: 1.2;
    letter-spacing: -0.4px;
}

.vacc-subtitle {
    font-size: 13px;
    color: var(--light-text);
    display: flex;
    align-items: center;
}

.btn-new {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--primary, #3b82f6);
    color: #fff;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: opacity 0.18s, transform 0.12s;
    white-space: nowrap;
}
.btn-new:hover { opacity: 0.88; transform: translateY(-1px); color: #fff; }
.btn-new:active { transform: translateY(0); }

/* ── Card ────────────────────────────────────────────────────── */
.vacc-card {
    background: var(--white, #fff);
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07), 0 0 0 1px rgba(0,0,0,0.04);
    overflow: hidden;
}

.table-scroll { overflow-x: auto; width: 100%; }

/* ── Table ───────────────────────────────────────────────────── */
.vacc-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.vacc-table th:nth-child(1), .vacc-table td:nth-child(1) { width: 18%; }
.vacc-table th:nth-child(2), .vacc-table td:nth-child(2) { width: 12%; text-align: center; }
.vacc-table th:nth-child(3), .vacc-table td:nth-child(3) { width: 12%; text-align: center; }
.vacc-table th:nth-child(4), .vacc-table td:nth-child(4) { width: 18%; text-align: center; }
.vacc-table th:nth-child(5), .vacc-table td:nth-child(5) { width: 10%; text-align: center; }
.vacc-table th:nth-child(6), .vacc-table td:nth-child(6) { width: 10%; text-align: center; }
.vacc-table th:nth-child(7), .vacc-table td:nth-child(7) { width: 10%; text-align: center; }
.vacc-table th:nth-child(8), .vacc-table td:nth-child(8) { width: 10%; text-align: center; }

.vacc-table thead tr {
    border-bottom: 1px solid var(--soft-gray, #f0f0f0);
}

.vacc-table thead th {
    padding: 13px 14px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.9px;
    text-transform: uppercase;
    color: var(--light-text);
    white-space: nowrap;
    background: transparent;
}

.vacc-table tbody tr {
    border-bottom: 1px solid var(--soft-gray, #f3f4f6);
    transition: background 0.14s;
}
.vacc-table tbody tr:last-child { border-bottom: none; }
.vacc-table tbody tr:hover { background: rgba(0,0,0,0.018); }

.vacc-table tbody td {
    padding: 11px 14px;
    vertical-align: middle;
    color: var(--dark-text);
    font-size: 15px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ── Vaccine cell ────────────────────────────────────────────── */
.vaccine-name {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 15px;
    line-height: 1.2;
}

.tag-virtual {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: rgba(99,102,241,0.08);
    color: #6366f1;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.batch-label {
    font-size: 12px;
    color: var(--light-text);
    margin-top: 1px;
}

/* ── Date chips ──────────────────────────────────────────────── */
.date-chip {
    display: inline-block;
    font-size: 15px;
    font-weight: 500;
    color: var(--dark-text);
    white-space: nowrap;
}
.date-chip--due {
    color: var(--light-text);
}

/* ── Vet name ────────────────────────────────────────────────── */
.vet-name {
    font-size: 15px;
    color: var(--dark-text);
}

/* ── Payment ─────────────────────────────────────────────────── */
.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.status-paid    { background: #d1fae5; color: #065f46; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-unpaid  { background: #fee2e2; color: #991b1b; }
.status-cancelled { background: #f3f4f6; color: #6b7280; }

/* default for unknown statuses */
.status-badge:not([class*="status-paid"]):not([class*="status-pending"]):not([class*="status-unpaid"]):not([class*="status-cancelled"]) {
    background: var(--soft-gray, #f3f4f6);
    color: var(--dark-text);
}

.null-val { color: var(--light-text); font-size: 18px; line-height: 1; }

/* ── Actions ─────────────────────────────────────────────────── */
.col-actions { text-align: center; white-space: nowrap; }

.action-group {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.action-group form { margin: 0; }

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 9px;
    border: 1px solid rgba(0,0,0,0.08);
    background: var(--white, #fff);
    color: var(--dark-text);
    font-size: 13px;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, transform 0.12s;
}
.action-btn:hover {
    background: var(--soft-gray, #f3f4f6);
    border-color: rgba(0,0,0,0.13);
    transform: translateY(-1px);
    color: var(--dark-text);
}
.action-btn:active { transform: translateY(0); }

.action-btn--danger { color: #ef4444; }
.action-btn--danger:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

.action-btn--pay { color: #d97706; }
.action-btn--pay:hover { background: #fef3c7; border-color: #fde68a; color: #b45309; }

.action-btn--accept { color: #16a34a; }
.action-btn--accept:hover { background: #dcfce7; border-color: #86efac; color: #15803d; }

/* ── Pagination ──────────────────────────────────────────────── */
.pagination-wrap {
    padding: 16px 24px;
    border-top: 1px solid var(--soft-gray, #f0f0f0);
}

/* ── Empty state ─────────────────────────────────────────────── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 72px 24px;
    text-align: center;
}

.empty-icon {
    width: 72px;
    height: 72px;
    border-radius: 20px;
    background: var(--soft-gray, #f3f4f6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #d1d5db;
}

.empty-msg {
    margin: 0;
    font-size: 15px;
    color: var(--light-text);
}
</style>
@endsection