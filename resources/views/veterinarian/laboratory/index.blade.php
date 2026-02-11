@extends('veterinarian.layout')

@section('title', 'Laboratory - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Laboratory</h2>
        @if($petId)
            <a href="{{ route('veterinarian.patients.show', $petId) }}" class="btn-action">
                <i class="fas fa-arrow-left me-2"></i>Back to Patient
            </a>
        @else
            <a href="{{ route('veterinarian.dashboard') }}" class="btn-action">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        @endif
    </div>

    <div class="empty-state">
        <i class="fas fa-microscope fa-3x mb-3"></i>
        <h4>Laboratory Management</h4>
        <p class="text-muted">
            Laboratory test management system is coming soon. This will allow you to:
        </p>
        <ul class="text-start" style="max-width: 400px; margin: 0 auto;">
            <li>Order laboratory tests</li>
            <li>View and manage test results</li>
            <li>Track specimen collection</li>
            <li>Generate lab reports</li>
            <li>Manage test workflows</li>
        </ul>
    </div>
</div>
@endsection

@push('styles')
<style>
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6b7280;
}

.empty-state i {
    color: var(--light-purple);
    margin-bottom: 1rem;
}

.empty-state h4 {
    margin-bottom: 1rem;
    color: #374151;
}

.text-start {
    text-align: left;
}
</style>
@endpush
