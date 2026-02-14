@extends('admin.dashboard')

@push('styles')
<style>
    .show-container { max-width: 980px; margin: 2rem auto; }
    .detail-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.75rem;
    }
    .page-header {
        display:flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .page-header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin: 0 0 0.3rem;
        color: #2c3e50;
    }
    .page-header p { color:#6c757d; margin:0; }
    .grid {
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem 1.5rem;
        margin-top: 1rem;
    }
    @media (max-width: 920px) { .grid { grid-template-columns: 1fr; } }
    .detail-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.15rem;
    }
    .detail-value { font-size: 1rem; font-weight: 600; color: #2c3e50; }
    .badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-block;
    }
    .b-pending { background:#e3f2fd; color:#1565c0; }
    .b-collected { background:#fff3cd; color:#856404; }
    .b-sent_to_lab { background:#ede7f6; color:#5e35b1; }
    .b-completed { background:#e8f5e9; color:#2e7d32; }
    .b-cancelled { background:#f8d7da; color:#842029; }
    .actions { display:flex; gap:.75rem; flex-wrap: wrap; }
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
        color: #fff;
    }
    .btn-primary { background: linear-gradient(135deg,#1565c0,#0d47a1); }
    .btn-secondary { background:#6c757d; }
    .btn-danger { background:#dc3545; }
    .text-block {
        margin-top: 1.25rem;
        background: #f9fafb;
        border-radius: 10px;
        padding: 1rem;
    }
    .text-block pre {
        white-space: pre-wrap;
        margin: 0;
        font-family: inherit;
        color: #2c3e50;
    }
</style>
@endpush

@section('content')
<div class="show-container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-file-medical"></i> Lab Requisition #{{ $labRequisition->id }}</h1>
            <p>Medical record-based lab request (schema: `lab_requisitions`).</p>
        </div>
        <div class="actions">
            <a href="{{ route('admin.laboratory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.laboratory.requisitions.edit', $labRequisition->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.laboratory.requisitions.destroy', $labRequisition->id) }}" onsubmit="return confirm('Delete this lab requisition?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="detail-card">
        @php($status = $labRequisition->status ?? 'pending')
        @php($statusClass = 'b-' . $status)

        <div class="grid">
            <div>
                <div class="detail-label">Pet</div>
                <div class="detail-value">{{ optional(optional($labRequisition->medicalRecord)->pet)->name ?? 'N/A' }}</div>

                <div class="detail-label">Owner</div>
                <div class="detail-value">
                    {{ optional(optional(optional(optional($labRequisition->medicalRecord)->pet)->owner)->user)->first_name ?? 'N/A' }}
                    {{ optional(optional(optional(optional($labRequisition->medicalRecord)->pet)->owner)->user)->last_name ?? '' }}
                </div>

                <div class="detail-label">Medical Record</div>
                <div class="detail-value">
                    #{{ $labRequisition->medical_record_id }}
                    @if($labRequisition->medicalRecord && $labRequisition->medicalRecord->visit_date)
                        (Visit: {{ $labRequisition->medicalRecord->visit_date->format('M d, Y') }})
                    @endif
                </div>

                <div class="detail-label">Requested By</div>
                <div class="detail-value">
                    {{ $labRequisition->requestedBy->first_name ?? 'N/A' }} {{ $labRequisition->requestedBy->last_name ?? '' }}
                    @if($labRequisition->requestedBy && $labRequisition->requestedBy->role)
                        ({{ $labRequisition->requestedBy->role }})
                    @endif
                </div>
            </div>

            <div>
                <div class="detail-label">Lab Test</div>
                <div class="detail-value">{{ $labRequisition->test->test_name ?? 'N/A' }}</div>

                <div class="detail-label">Category</div>
                <div class="detail-value">{{ ucfirst($labRequisition->test->category ?? 'N/A') }}</div>

                <div class="detail-label">Standard Price</div>
                <div class="detail-value">
                    @php($price = optional($labRequisition->test)->standard_price)
                    {{ $price !== null ? '₱' . number_format($price, 2) : 'Not set' }}
                </div>

                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="badge {{ $statusClass }}">{{ str_replace('_', ' ', $status) }}</span>
                </div>

                <div class="detail-label">Requested Date</div>
                <div class="detail-value">{{ $labRequisition->requested_date ? $labRequisition->requested_date->format('M d, Y h:i A') : 'N/A' }}</div>

                <div class="detail-label">Sample Collected</div>
                <div class="detail-value">{{ $labRequisition->sample_collected ? 'Yes' : 'No' }}</div>

                <div class="detail-label">Sample Collection Date</div>
                <div class="detail-value">{{ $labRequisition->sample_collection_date ? $labRequisition->sample_collection_date->format('M d, Y h:i A') : '—' }}</div>
            </div>
        </div>

        <div class="text-block">
            <div class="detail-label">Notes</div>
            <pre>{{ $labRequisition->notes ?: '—' }}</pre>
        </div>

        <div class="text-block">
            <div class="detail-label">Results</div>
            <pre>{{ $labRequisition->results ?: '—' }}</pre>
        </div>
    </div>
</div>

@endsection

