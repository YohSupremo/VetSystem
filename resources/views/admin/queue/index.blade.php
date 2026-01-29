@extends('admin.dashboard')

@push('styles')
<style>
    .queue-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }
    .queue-column {
        flex: 1;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .queue-column h3 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
        color: #333;
    }
    .queue-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 12px 15px;
        margin-bottom: 10px;
        cursor: move;
        transition: all 0.3s ease;
    }
    .queue-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .queue-item.in-progress {
        border-left: 4px solid #007bff;
    }
    .queue-item.completed {
        opacity: 0.7;
        background-color: #f8f9fa;
    }
    .queue-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .queue-number {
        background: #007bff;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 12px;
        font-weight: bold;
    }
    .pet-name {
        font-weight: 600;
        color: #333;
    }
    .pet-type {
        font-size: 12px;
        color: #6c757d;
    }
    .wait-time {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
        margin-top: 10px;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
    }
    .stat-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Queue Management</h1>
        <div class="d-flex">
            <form method="GET" action="{{ route('admin.queue.index') }}" class="form-inline">
                <div class="form-group mr-2">
                    <label for="veterinarian_id" class="mr-2 small mb-0">Veterinarian</label>
                    <select name="veterinarian_id" id="veterinarian_id" class="form-control form-control-sm">
                        <option value="">All Veterinarians</option>
                        @foreach($veterinarians as $vet)
                            <option value="{{ $vet->id }}" @if(request('veterinarian_id') == $vet->id) selected @endif>
                                {{ $vet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-outline-secondary mr-2">
                    Filter
                </button>
            </form>

            <form method="POST" action="{{ route('admin.queue.call-next', ['veterinarianId' => request('veterinarian_id')]) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-bell"></i> Call Next
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-value">{{ $stats['waiting'] ?? 0 }}</div>
                <div class="stat-label">Waiting</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-value">{{ $stats['in_progress'] ?? 0 }}</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-value">{{ $stats['completed'] ?? 0 }}</div>
                <div class="stat-label">Completed Today</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-value">
                    {{ $stats['average_wait_time'] ?? 'No data' }}
                </div>
                <div class="stat-label">Avg. Wait Time</div>
            </div>
        </div>
    </div>

    <div class="queue-container">
        <!-- Waiting Column -->
        <div class="queue-column">
            <h3>Waiting <span class="badge badge-primary">{{ $appointments['scheduled']->count() }}</span></h3>
            <div>
                @forelse($appointments['scheduled'] as $appointment)
                    @include('admin.queue.partials.appointment-card', ['appointment' => $appointment])
                @empty
                    <p class="text-muted small mb-0">No scheduled appointments.</p>
                @endforelse
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="queue-column">
            <h3>In Progress <span class="badge badge-info">{{ $appointments['in_progress']->count() }}</span></h3>
            <div>
                @forelse($appointments['in_progress'] as $appointment)
                    @include('admin.queue.partials.appointment-card', ['appointment' => $appointment])
                @empty
                    <p class="text-muted small mb-0">No appointments in progress.</p>
                @endforelse
            </div>
        </div>

        <!-- Completed Column -->
        <div class="queue-column">
            <h3>Completed <span class="badge badge-secondary">{{ $appointments['completed']->count() }}</span></h3>
            <div>
                @forelse($appointments['completed'] as $appointment)
                    @include('admin.queue.partials.appointment-card', ['appointment' => $appointment])
                @empty
                    <p class="text-muted small mb-0">No completed appointments yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
