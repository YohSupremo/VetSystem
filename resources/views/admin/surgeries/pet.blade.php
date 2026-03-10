@extends('admin.dashboard')

@section('page-title', 'Surgery Records for ' . ($pet->name ?? 'Pet'))
@section('page-description', 'All surgeries for ' . ($pet->name ?? 'pet'))

@section('content')
<div class="surg-wrapper">
    @php $showTrash = request()->boolean('trash'); @endphp
    {{-- Page Header --}}
    <div class="surg-header">
        <div class="surg-header-left">
            <a href="{{ route('admin.surgeries.index', ['trash' => $showTrash ? 1 : null]) }}" class="back-link">
                <span class="back-arrow"><i class="fas fa-arrow-left"></i></span>
                <span>All Surgeries</span>
            </a>
            <div class="surg-title-block">
                <h2 class="surg-title">{{ $pet->name ?? 'N/A' }}</h2>
                <div class="surg-subtitle">
                    <i class="fas fa-user-circle" style="font-size:13px; margin-right:5px; opacity:0.5;"></i>
                    {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}
                </div>
            </div>
        </div>
        <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
            @if($showTrash)
                <a href="{{ route('admin.surgeries.pet', ['pet' => $pet->id]) }}" class="btn-new" style="background:#6b7280;">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back To Active</span>
                </a>
            @else
                <a href="{{ route('admin.surgeries.pet', ['pet' => $pet->id, 'trash' => 1]) }}" class="btn-new" style="background:#64748b;">
                    <i class="fas fa-trash-restore"></i>
                    <span>View Trash</span>
                </a>
                <a href="{{ route('admin.surgeries.create', ['pet_id' => $pet->id]) }}" class="btn-new">
                    <i class="fas fa-plus"></i>
                    <span>Schedule New Surgery</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Table Card --}}
    <div class="surg-card">
        @if($surgeries->count() > 0)
            <div class="table-scroll">
                <table class="surg-table">
                    <thead>
                        <tr>
                            <th>Procedure</th>
                            <th>Scheduled</th>
                            <th>Surgeon</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Tax</th>
                            <th>Payment Status</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($surgeries as $surgery)
                            @php $isVirtual = (bool) $surgery->getAttribute('is_virtual'); @endphp
                            <tr class="surg-row{{ $isVirtual ? ' surg-row--virtual' : '' }}">
                                <td>
                                    <div class="surgery-name">
                                        @if($isVirtual)
                                            <span class="tag-virtual"><i class="fas fa-calendar-alt"></i> Appointment</span>
                                        @else
                                            {{ $surgery->surgeryType->name ?? 'N/A' }}
                                        @endif
                                    </div>
                                    @if(!$isVirtual && $surgery->anesthesia_type)
                                        <div class="anesthesia-label">{{ $surgery->anesthesia_type }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="date-chip">
                                        {{ $surgery->scheduled_date ? \Carbon\Carbon::parse($surgery->scheduled_date)->format('M d, Y H:i A') : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="vet-name">
                                        @if($isVirtual && $surgery->appointment && $surgery->appointment->veterinarian)
                                            Dr. {{ $surgery->appointment->veterinarian->first_name }} {{ $surgery->appointment->veterinarian->last_name }}
                                        @else
                                            {{ $surgery->surgeon ? 'Dr. ' . $surgery->surgeon->first_name . ' ' . $surgery->surgeon->last_name : '—' }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'scheduled' => ['bg' => '#dbeafe', 'color' => '#1e40af'],
                                            'in_progress' => ['bg' => '#fef3c7', 'color' => '#92400e'],
                                            'completed' => ['bg' => '#d1fae5', 'color' => '#065f46'],
                                            'cancelled' => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
                                        ];
                                        $statusStyle = $statusMap[$surgery->status] ?? ['bg' => '#f3f4f6', 'color' => '#374151'];
                                    @endphp
                                    <span class="status-badge" style="background: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['color'] }};">
                                        {{ ucfirst(str_replace('_', ' ', $surgery->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if(!$isVirtual)
                                        @php $invoice = $surgery->getAttribute('billing_invoice'); @endphp
                                        @if($invoice)
                                            <span class="null-val">₱{{ number_format($invoice->total_amount, 2) }}</span>
                                        @else
                                            <span class="null-val">—</span>
                                        @endif
                                    @else
                                        <span class="null-val">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$isVirtual)
                                        @php $invoice = $surgery->getAttribute('billing_invoice'); @endphp
                                        @if($invoice && (float) $invoice->tax_amount > 0)
                                            <span class="null-val">₱{{ number_format($invoice->tax_amount, 2) }}</span>
                                        @else
                                            <span class="null-val">—</span>
                                        @endif
                                    @else
                                        <span class="null-val">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$isVirtual)
                                        @php $invoice = $surgery->getAttribute('billing_invoice'); @endphp
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
                                <td class="col-actions">
                                    @if(!$isVirtual)
                                        <div class="action-group">
                                            @php $invoice = $surgery->getAttribute('billing_invoice'); @endphp
                                            @if(!$showTrash && $invoice && !$invoice->is_paid && $invoice->status !== 'cancelled')
                                                <form action="{{ route('admin.surgeries.payment.process', $surgery->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark this invoice as paid?');">
                                                    @csrf
                                                    <button type="submit" class="action-btn action-btn--pay" title="Mark as Paid">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.surgeries.show', $surgery->id) }}" class="action-btn" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($showTrash)
                                                <form action="{{ route('admin.surgeries.restore', $surgery->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Restore this surgery record?');">
                                                    @csrf
                                                    <button type="submit" class="action-btn" title="Restore" style="color:#0f766e;">
                                                        <i class="fas fa-trash-restore"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.surgeries.edit', $surgery->id) }}" class="action-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.surgeries.destroy', $surgery->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this surgery record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn action-btn--danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @elseif($surgery->appointment)
                                        <div class="action-group">
                                            <a href="{{ route('admin.appointments.show', $surgery->appointment->id) }}" class="action-btn" title="View Appointment">
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

            @if($surgeries->hasPages())
                <div class="pagination-wrap">
                    {{ $surgeries->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-procedures"></i></div>
                <p class="empty-msg">No surgery records found for this pet.</p>
                <a href="{{ route('admin.surgeries.create', ['pet_id' => $pet->id]) }}" class="btn-new" style="margin-top:4px;">
                    <i class="fas fa-plus"></i>
                    <span>Schedule one now</span>
                </a>
            </div>
        @endif
    </div>
</div>

<style>
/* ── Layout ──────────────────────────────────────────────────── */
.surg-wrapper {
    display: flex;
    flex-direction: column;
    gap: 24px;
    padding: 4px 0 32px;
}

/* ── Header ──────────────────────────────────────────────────── */
.surg-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    flex-wrap: wrap;
}

.surg-header-left {
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

.surg-title-block { display: flex; flex-direction: column; gap: 4px; }

.surg-title {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: var(--dark-text);
    line-height: 1.2;
    letter-spacing: -0.4px;
}

.surg-subtitle {
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
.surg-card {
    background: var(--white, #fff);
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07), 0 0 0 1px rgba(0,0,0,0.04);
    overflow: hidden;
}

.table-scroll { overflow-x: auto; width: 100%; }

/* ── Table ───────────────────────────────────────────────────── */
.surg-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.surg-table th:nth-child(1), .surg-table td:nth-child(1) { width: 18%; }
.surg-table th:nth-child(2), .surg-table td:nth-child(2) { width: 15%; text-align: center; }
.surg-table th:nth-child(3), .surg-table td:nth-child(3) { width: 14%; text-align: center; }
.surg-table th:nth-child(4), .surg-table td:nth-child(4) { width: 10%; text-align: center; }
.surg-table th:nth-child(5), .surg-table td:nth-child(5) { width: 12%; text-align: center; }
.surg-table th:nth-child(6), .surg-table td:nth-child(6) { width: 8%; text-align: center; }
.surg-table th:nth-child(7), .surg-table td:nth-child(7) { width: 11%; text-align: center; }
.surg-table th:nth-child(8), .surg-table td:nth-child(8) { width: 12%; text-align: center; }

.surg-table thead tr {
    border-bottom: 1px solid var(--soft-gray, #f0f0f0);
}

.surg-table thead th {
    padding: 10px 10px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.9px;
    text-transform: uppercase;
    color: var(--light-text);
    white-space: nowrap;
    background: transparent;
}

.surg-table tbody tr {
    border-bottom: 1px solid var(--soft-gray, #f3f4f6);
    transition: background 0.14s;
}
.surg-table tbody tr:last-child { border-bottom: none; }
.surg-table tbody tr:hover { background: rgba(0,0,0,0.018); }

.surg-table tbody td {
    padding: 9px 10px;
    vertical-align: middle;
    color: var(--dark-text);
    font-size: 13px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
    word-break: break-word;
}

/* ── Surgery cell ────────────────────────────────────────────── */
.surgery-name {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 13px;
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

.anesthesia-label {
    font-size: 12px;
    color: var(--light-text);
    margin-top: 1px;
}

/* ── Date chips ──────────────────────────────────────────────── */
.date-chip {
    display: inline-block;
    font-size: 12px;
    font-weight: 500;
    color: var(--dark-text);
    white-space: nowrap;
}

/* ── Vet name ────────────────────────────────────────────────── */
.vet-name {
    font-size: 13px;
    color: var(--dark-text);
}

/* ── Payment ─────────────────────────────────────────────────── */
.status-badge {
    display: inline-block;
    padding: 4px 9px;
    border-radius: 20px;
    font-size: 11px;
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
    gap: 4px;
}
.action-group form { margin: 0; }

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 9px;
    border: 1px solid rgba(0,0,0,0.08);
    background: var(--white, #fff);
    color: var(--dark-text);
    font-size: 12px;
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
