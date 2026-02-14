@extends('admin.dashboard')

@section('page-title', 'Surgery Details')
@section('page-description', $surgery->pet->name ?? 'Pet Surgery')

@section('content')
<div class="container-fluid">
    <div class="record-container">
        <div class="record-header">
            <h2><i class="fas fa-procedures"></i> Surgery Record</h2>
            <div class="header-actions">
                <a href="{{ route('admin.surgeries.edit', $surgery->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.surgeries.destroy', $surgery->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this surgery record? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" style="background:#ff6b6b; color:white;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                <a href="{{ route('admin.surgeries.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="record-content">
            <div class="record-section">
                <h3>Pet Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Pet Name</label>
                        <p>{{ $surgery->pet->name ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Species</label>
                        <p>{{ $surgery->pet->species ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Breed</label>
                        <p>{{ $surgery->pet->breed ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Owner</label>
                        <p>{{ ($surgery->pet && $surgery->pet->owner && $surgery->pet->owner->user) ? $surgery->pet->owner->user->first_name . ' ' . $surgery->pet->owner->user->last_name : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="record-section">
                <h3>Surgery Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Procedure Name</label>
                        <p>{{ $surgery->procedure_name ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Surgeon</label>
                        <p>{{ ($surgery->surgeon) ? 'Dr. ' . $surgery->surgeon->first_name . ' ' . $surgery->surgeon->last_name : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Scheduled Date</label>
                        <p>{{ $surgery->scheduled_date ? \Carbon\Carbon::parse($surgery->scheduled_date)->format('M d, Y H:i A') : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Status</label>
                        <p>
                            <span class="badge" style="background: {{ $surgery->status === 'completed' ? '#5FD068' : ($surgery->status === 'in_progress' ? '#FFC107' : '#4A90E2') }}; color: {{ $surgery->status === 'completed' || $surgery->status === 'cancelled' ? 'white' : 'black' }};">
                                {{ ucfirst($surgery->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="record-section">
                <h3>Anesthesia & Medical Info</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Anesthesia Type</label>
                        <p>{{ $surgery->anesthesia_type ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="record-section">
                <h3>Pre-operative Notes</h3>
                <div class="text-content">
                    {{ $surgery->pre_op_notes ?? 'N/A' }}
                </div>
            </div>

            <div class="record-section">
                <h3>Surgery Notes</h3>
                <div class="text-content">
                    {{ $surgery->surgery_notes ?? 'Not yet recorded' }}
                </div>
            </div>

            <div class="record-section">
                <h3>Post-operative Instructions</h3>
                <div class="text-content">
                    {{ $surgery->post_op_instructions ?? 'N/A' }}
                </div>
            </div>

            <div class="record-section">
                <h3>Outcome</h3>
                <div class="text-content">
                    {{ $surgery->outcome ?? 'Not yet recorded' }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.record-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.record-header {
    background: linear-gradient(135deg, #9B7EDE 0%, #B399E8 100%);
    color: white;
    padding: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.record-header h2 {
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.record-content {
    padding: 30px;
}

.record-section {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid #f0f0f0;
}

.record-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.record-section h3 {
    color: var(--dark-text);
    margin-bottom: 15px;
    font-size: 18px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-item {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 6px;
}

.info-item label {
    display: block;
    font-weight: 600;
    color: #9B7EDE;
    margin-bottom: 5px;
    font-size: 12px;
    text-transform: uppercase;
}

.info-item p {
    margin: 0;
    color: var(--dark-text);
}

.text-content {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 6px;
    line-height: 1.6;
    color: var(--dark-text);
    white-space: pre-wrap;
    word-wrap: break-word;
}

.badge {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
    font-size: 14px;
}

.btn-primary {
    background: white;
    color: #9B7EDE;
}

.btn-primary:hover {
    background: #f0f0f0;
}

.btn-secondary {
    background: #6C757D;
    color: white;
}

.btn-secondary:hover {
    background: #5A6268;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .record-header {
        flex-direction: column;
        gap: 15px;
    }
}
</style>
@endsection
