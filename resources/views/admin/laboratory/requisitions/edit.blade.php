@extends('admin.dashboard')

@push('styles')
<style>
    .form-container {
        max-width: 980px;
        margin: 2rem auto;
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
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 920px) { .grid { grid-template-columns: 1fr; } }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.35rem;
    }
    .form-control {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d1d3e2;
        padding: 0.6rem 0.8rem;
    }
    .help { color:#6c757d; font-size:.85rem; margin-top:.35rem; }
    .form-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.25rem;
        flex-wrap: wrap;
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
        color: #fff;
    }
    .btn-primary { background: linear-gradient(135deg,#1565c0,#0d47a1); }
    .btn-secondary { background: #6c757d; }
    .btn-danger { background:#dc3545; }
    .delete-form { display:inline; }
    .note-card {
        background: #f9fafb;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .note-title {
        font-size: .85rem;
        text-transform: uppercase;
        color:#6c757d;
        margin-bottom: .35rem;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-file-medical"></i> Edit Requisition #{{ $labRequisition->id }}</h1>
            <p>Update status, results, and other details.</p>
        </div>
        <div class="form-actions" style="margin-top:0;">
            <a href="{{ route('admin.laboratory.requisitions.show', $labRequisition->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="note-card">
        <div class="note-title">Linked Medical Record</div>
        <div>
            #{{ $labRequisition->medical_record_id }}
            -
            {{ optional(optional($labRequisition->medicalRecord)->pet)->name ?? 'N/A' }}
            ({{ optional(optional(optional(optional($labRequisition->medicalRecord)->pet)->owner)->user)->first_name ?? 'Owner' }}
            {{ optional(optional(optional(optional($labRequisition->medicalRecord)->pet)->owner)->user)->last_name ?? '' }})
        </div>
    </div>

    <form method="POST" action="{{ route('admin.laboratory.requisitions.update', $labRequisition->id) }}" style="margin-top:1rem;">
        @csrf
        @method('PUT')

        <div class="grid">
            <div>
                <div class="form-group">
                    <label for="test_id">Lab Test</label>
                    <select id="test_id" name="test_id" class="form-control" required>
                        @foreach($labTests as $t)
                            <option value="{{ $t->id }}" @selected(old('test_id', $labRequisition->test_id) == $t->id)>
                                {{ $t->test_name }}
                                ({{ ucfirst($t->category) }})
                                @if($t->standard_price !== null) - ₱{{ number_format($t->standard_price, 2) }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="requested_by">Requested By</label>
                    <select id="requested_by" name="requested_by" class="form-control" required>
                        @foreach($requesters as $u)
                            <option value="{{ $u->id }}" @selected(old('requested_by', $labRequisition->requested_by) == $u->id)>
                                {{ $u->first_name }} {{ $u->last_name }} ({{ $u->role ?? 'user' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="requested_date">Requested Date</label>
                    <input type="datetime-local" id="requested_date" name="requested_date" class="form-control" required
                        value="{{ old('requested_date', optional($labRequisition->requested_date)->format('Y-m-d\TH:i')) }}">
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label for="status">Status</label>
                    @php($statusOld = old('status', $labRequisition->status))
                    <select id="status" name="status" class="form-control" required>
                        <option value="pending" @selected($statusOld === 'pending')>Pending</option>
                        <option value="collected" @selected($statusOld === 'collected')>Collected</option>
                        <option value="sent_to_lab" @selected($statusOld === 'sent_to_lab')>Sent to Lab</option>
                        <option value="completed" @selected($statusOld === 'completed')>Completed</option>
                        <option value="cancelled" @selected($statusOld === 'cancelled')>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sample_collected">Sample Collected</label>
                    <select id="sample_collected" name="sample_collected" class="form-control">
                        <option value="0" @selected((int)old('sample_collected', (int)$labRequisition->sample_collected) === 0)>No</option>
                        <option value="1" @selected((int)old('sample_collected', (int)$labRequisition->sample_collected) === 1)>Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sample_collection_date">Sample Collection Date (optional)</label>
                    <input type="datetime-local" id="sample_collection_date" name="sample_collection_date" class="form-control"
                        value="{{ old('sample_collection_date', optional($labRequisition->sample_collection_date)->format('Y-m-d\TH:i')) }}">
                    <div class="help">If sample is marked Yes and this is empty, it auto-sets to now.</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes', $labRequisition->notes) }}</textarea>
        </div>

        <div class="form-group">
            <label for="results">Result Summary (Report) <span class="text-danger">*</span></label>
            <textarea id="results" name="results" rows="4" class="form-control @error('results') is-invalid @enderror"
                      placeholder="Enter a brief summary of the report or initial findings here...">{{ old('results', $labRequisition->results) }}</textarea>
            @error('results')
                <div class="text-danger" style="margin-top: 0.25rem; font-size: 0.875em;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions d-flex justify-content-between align-items-center">
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('admin.laboratory.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Back to Dashboard
                </a>
            </div>
            
            <button type="button" class="btn btn-danger" onclick="if(confirm('Delete this lab requisition?')) document.getElementById('delete-form').submit();">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </form>

    <form id="delete-form" method="POST" action="{{ route('admin.laboratory.requisitions.destroy', $labRequisition->id) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

