@extends('admin.dashboard')

@push('styles')
<style>
    .form-container {
        max-width: 900px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.75rem;
    }
    .page-header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
        color: #2c3e50;
    }
    .page-header p { color:#6c757d; margin:0 0 1.25rem; }
    .grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 860px) {
        .grid { grid-template-columns: 1fr; }
    }
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
    .help {
        color: #6c757d;
        font-size: .85rem;
        margin-top: .35rem;
    }
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
    .btn-primary { background: linear-gradient(135deg,#4caf50,#388e3c); }
    .btn-secondary { background: #6c757d; }
    .checkbox-row { display:flex; align-items:center; gap:.5rem; margin-top:.35rem; }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1><i class="fas fa-file-medical"></i> New Lab Requisition</h1>
        <p>Create a lab request based on your database schema (`lab_requisitions`).</p>
    </div>

    <form method="POST" action="{{ route('admin.laboratory.requisitions.store') }}">
        @csrf

        <div class="grid">
            <div>
                <div class="form-group">
                    <label for="medical_record_id">Medical Record</label>
                    <select id="medical_record_id" name="medical_record_id" class="form-control" required>
                        <option value="">-- Select Medical Record --</option>
                        @foreach($medicalRecords as $mr)
                            <option value="{{ $mr->id }}" @selected(old('medical_record_id') == $mr->id)>
                                #{{ $mr->id }}
                                - {{ $mr->pet->name ?? 'Pet' }}
                                ({{ optional(optional(optional($mr->pet)->owner)->user)->first_name ?? 'Owner' }}
                                {{ optional(optional(optional($mr->pet)->owner)->user)->last_name ?? '' }})
                                - {{ $mr->visit_date ? $mr->visit_date->format('M d, Y') : 'No date' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="help">This links the requisition to a patient visit (`medical_records`).</div>
                </div>

                <div class="form-group">
                    <label for="test_id">Lab Test</label>
                    <select id="test_id" name="test_id" class="form-control" required>
                        <option value="">-- Select Lab Test --</option>
                        @foreach($labTests as $t)
                            <option value="{{ $t->id }}" @selected(old('test_id') == $t->id)>
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
                        <option value="">-- Select User --</option>
                        @foreach($requesters as $u)
                            <option value="{{ $u->id }}" @selected(old('requested_by') == $u->id)>
                                {{ $u->first_name }} {{ $u->last_name }} ({{ $u->role ?? 'user' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label for="requested_date">Requested Date</label>
                    <input type="datetime-local" id="requested_date" name="requested_date" class="form-control"
                        value="{{ old('requested_date', now()->format('Y-m-d\TH:i')) }}">
                    <div class="help">If you leave this as-is, it will use the current date/time.</div>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        @php($statusOld = old('status', 'pending'))
                        <option value="pending" @selected($statusOld === 'pending')>Pending</option>
                        <option value="collected" @selected($statusOld === 'collected')>Collected</option>
                        <option value="sent_to_lab" @selected($statusOld === 'sent_to_lab')>Sent to Lab</option>
                        <option value="completed" @selected($statusOld === 'completed')>Completed</option>
                        <option value="cancelled" @selected($statusOld === 'cancelled')>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Sample Collected</label>
                    <div class="checkbox-row">
                        <input type="checkbox" id="sample_collected" name="sample_collected" value="1" @checked(old('sample_collected'))>
                        <label for="sample_collected" style="margin:0;">Yes</label>
                    </div>
                    <div class="help">If checked and no collection date is set, it will auto-set to now.</div>
                </div>

                <div class="form-group">
                    <label for="sample_collection_date">Sample Collection Date (optional)</label>
                    <input type="datetime-local" id="sample_collection_date" name="sample_collection_date" class="form-control"
                        value="{{ old('sample_collection_date') }}">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
        </div>

        <div class="form-group">
            <label for="results">Results (optional)</label>
            <textarea id="results" name="results" rows="4" class="form-control">{{ old('results') }}</textarea>
            <div class="help">Results text is stored in the `results` column (per schema).</div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.laboratory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Requisition
            </button>
        </div>
    </form>
</div>
@endsection

