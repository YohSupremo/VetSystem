@extends('admin.dashboard')

@push('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 2rem;
    }
    .page-header h1 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }
    .page-header p { color:#6c757d; margin:0 0 1.5rem; }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #344767;
    }
    .form-control {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d1d3e2;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }
    .form-control:focus {
        border-color: #4e73df;
        outline: none;
        box-shadow: 0 0 0 3px rgba(78,115,223,0.1);
    }
    .help-text {
        font-size: 0.85rem;
        color: #858796;
        margin-top: 0.25rem;
    }
    .row {
        display: flex;
        gap: 1.5rem;
    }
    .col { flex: 1; }
    .btn {
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-primary { background: #4e73df; color: white; }
    .btn-primary:hover { background: #2e59d9; }
    .btn-secondary { background: #858796; color: white; }
    .btn-secondary:hover { background: #60616f; }
    .actions {
        margin-top: 2rem;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }
    
    /* Checkbox styling */
    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        background: #f8f9fc;
    }
    .checkbox-wrapper input[type="checkbox"] {
        width: 1.2em;
        height: 1.2em;
    }
    .checkbox-wrapper label { margin: 0; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1><i class="fas fa-microscope"></i> New Laboratory Requisition</h1>
        <p>Create a new lab test request linked to a medical record.</p>
    </div>

    <form method="POST" action="{{ route('admin.laboratory.requisitions.store') }}">
        @csrf

        <!-- Medical Record & Test Selection -->
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="medical_record_id">Medical Record <span class="text-danger">*</span></label>
                    <select id="medical_record_id" name="medical_record_id" class="form-control @error('medical_record_id') is-invalid @enderror">
                        <option value="">-- Select Patient Visit --</option>
                        @foreach($medicalRecords as $mr)
                            <option value="{{ $mr->id }}" @selected(old('medical_record_id') == $mr->id)>
                                #{{ $mr->id }} - {{ $mr->pet->name ?? 'Unknown Pet' }} 
                                ({{ $mr->visit_date ? \Carbon\Carbon::parse($mr->visit_date)->format('M d, Y') : 'No Date' }})
                            </option>
                        @endforeach
                    </select>
                    @error('medical_record_id')
                        <span class="text-danger help-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="test_id">Lab Test <span class="text-danger">*</span></label>
                    <select id="test_id" name="test_id" class="form-control @error('test_id') is-invalid @enderror">
                        <option value="">-- Select Test --</option>
                        @foreach($labTests as $test)
                            <option value="{{ $test->id }}" @selected(old('test_id') == $test->id)>
                                {{ $test->test_name }} ({{ ucfirst($test->category) }})
                            </option>
                        @endforeach
                    </select>
                    @error('test_id')
                        <span class="text-danger help-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Requester & Date -->
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="requested_by">Requested By <span class="text-danger">*</span></label>
                    <select id="requested_by" name="requested_by" class="form-control @error('requested_by') is-invalid @enderror">
                        <option value="">-- Select Staff --</option>
                        @foreach($requesters as $staff)
                            <option value="{{ $staff->id }}" @selected(old('requested_by', auth()->id()) == $staff->id)>
                                {{ $staff->first_name }} {{ $staff->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('requested_by')
                        <span class="text-danger help-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="requested_date">Requested Date</label>
                    <input type="datetime-local" id="requested_date" name="requested_date" 
                           class="form-control @error('requested_date') is-invalid @enderror"
                           value="{{ old('requested_date', now()->format('Y-m-d\TH:i')) }}">
                    @error('requested_date')
                        <span class="text-danger help-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sample Collection -->
        <div class="form-group">
            <label>Sample Status</label>
            <div class="row" style="align-items: flex-start;">
                <div class="col">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="sample_collected" name="sample_collected" value="1" @checked(old('sample_collected'))>
                        <label for="sample_collected">Sample Collected?</label>
                    </div>
                </div>
                <div class="col">
                    <input type="datetime-local" id="sample_collection_date" name="sample_collection_date" 
                           class="form-control @error('sample_collection_date') is-invalid @enderror"
                           placeholder="Collection Date"
                           value="{{ old('sample_collection_date') }}">
                    <div class="help-text">Leave specific time empty if same as requested date</div>
                </div>
            </div>
        </div>

        <!-- Status & Notes -->
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="status">Initial Status</label>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="pending" @selected(old('status') == 'pending')>Pending</option>
                        <option value="collected" @selected(old('status') == 'collected')>Sample Collected</option>
                        <option value="sent_to_lab" @selected(old('status') == 'sent_to_lab')>Sent to Lab</option>
                        <option value="completed" @selected(old('status') == 'completed')>Completed</option>
                    </select>
                    @error('status')
                        <span class="text-danger help-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Clinical Notes</label>
            <textarea id="notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                      placeholder="Any relevant clinical information...">{{ old('notes') }}</textarea>
            @error('notes')
                <span class="text-danger help-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group" id="results-group" style="display: {{ old('status') === 'completed' ? 'block' : 'none' }};">
            <label for="results">Result Summary (Report)</label>
            <textarea id="results" name="results" rows="4" class="form-control @error('results') is-invalid @enderror"
                      placeholder="Enter a brief summary of the report or initial findings here...">{{ old('results') }}</textarea>
            @error('results')
                <span class="text-danger help-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="actions">
            <a href="{{ route('admin.laboratory.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Requisition
            </button>
        </div>
    </form>
</div>

<script>
    // Simple script to toggle collection date visibility/requirement
    document.getElementById('sample_collected').addEventListener('change', function() {
        const dateInput = document.getElementById('sample_collection_date');
        if (this.checked && !dateInput.value) {
            // Optional: auto-fill with current time if checked
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            dateInput.value = now.toISOString().slice(0,16);
        }
    });

    // Show report summary only when status is completed
    const statusSelect = document.getElementById('status');
    const resultsGroup = document.getElementById('results-group');
    statusSelect.addEventListener('change', function() {
        resultsGroup.style.display = this.value === 'completed' ? 'block' : 'none';
    });
</script>
@endsection
