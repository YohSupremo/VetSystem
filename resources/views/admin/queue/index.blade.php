@extends('admin.dashboard')

@section('page-title', 'Queue Management')
@section('page-description', 'Manage appointment queue and waiting list')

@push('styles')
<style>
    .queue-toolbar {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.97), rgba(252, 244, 255, 0.95));
        border: 1px solid rgba(251, 146, 60, 0.24);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        box-shadow: 0 10px 24px rgba(251, 146, 60, 0.1);
        margin-bottom: 1rem;
    }

    .queue-title {
        font-weight: 800;
        margin-bottom: 0;
        background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .queue-filter-form {
        display: flex;
        align-items: end;
        gap: 0.5rem;
        margin-right: 0.5rem;
    }

    .queue-filter-form .form-group {
        margin-bottom: 0;
    }

    .queue-filter-form label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #6B7280;
        margin-bottom: 0.25rem;
    }

    .queue-filter-form .form-control {
        border-radius: 10px;
        border: 1px solid #FED7AA;
        min-width: 170px;
        box-shadow: inset 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    .queue-filter-form .form-control:focus {
        border-color: #FB923C;
        box-shadow: 0 0 0 0.2rem rgba(251, 146, 60, 0.16);
    }

    .queue-filter-form .btn-outline-secondary {
        border-radius: 10px;
        border-color: #FED7AA;
        color: #EA580C;
        font-weight: 600;
    }

    .queue-filter-form .btn-outline-secondary:hover {
        background: #FFF7ED;
        color: #C2410C;
    }

    .queue-call-btn {
        border-radius: 12px;
        font-weight: 700;
        border: none;
        padding: 0.45rem 0.95rem;
        background: linear-gradient(135deg, #FB7185 0%, #EC4899 100%);
        box-shadow: 0 10px 18px rgba(236, 72, 153, 0.22);
    }

    .queue-call-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 22px rgba(236, 72, 153, 0.28);
    }

    .stats-card {
        border-radius: 14px;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(251, 146, 60, 0.18);
        box-shadow: 0 8px 20px rgba(251, 146, 60, 0.08);
        background: linear-gradient(140deg, rgba(255, 255, 255, 0.97), rgba(255, 247, 237, 0.94));
    }

    .stats-card.waiting-stat {
        border-color: rgba(251, 146, 60, 0.25);
        background: linear-gradient(140deg, rgba(255,255,255,0.98), rgba(255, 247, 237, 0.95));
    }

    .stats-card.progress-stat {
        border-color: rgba(236, 72, 153, 0.23);
        background: linear-gradient(140deg, rgba(255,255,255,0.98), rgba(253, 242, 248, 0.95));
    }

    .stats-card.completed-stat {
        border-color: rgba(34, 197, 94, 0.22);
        background: linear-gradient(140deg, rgba(255,255,255,0.98), rgba(240, 253, 244, 0.95));
    }

    .stat-value {
        font-size: 1.9rem;
        line-height: 1;
        font-weight: 800;
        color: #EA580C;
    }

    .waiting-stat .stat-value {
        color: #EA580C;
    }

    .progress-stat .stat-value {
        color: #DB2777;
    }

    .completed-stat .stat-value {
        color: #059669;
    }

    .stat-label {
        margin-top: 0.35rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6B7280;
        letter-spacing: 0.7px;
        font-weight: 700;
    }

    .queue-container {
        display: flex;
        gap: 18px;
        margin-top: 10px;
    }

    .queue-column {
        flex: 1;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 10px 22px rgba(251, 146, 60, 0.08);
        border: 1px solid rgba(251, 146, 60, 0.2);
        background: linear-gradient(180deg, #FFFFFF 0%, #FFF7ED 100%);
    }

    .queue-column.waiting-col {
        border-color: rgba(251, 146, 60, 0.24);
        background: linear-gradient(180deg, #FFFFFF 0%, #FFF7ED 100%);
    }

    .queue-column.progress-col {
        border-color: rgba(236, 72, 153, 0.22);
        background: linear-gradient(180deg, #FFFFFF 0%, #FDF2F8 100%);
    }

    .queue-column.completed-col {
        border-color: rgba(34, 197, 94, 0.22);
        background: linear-gradient(180deg, #FFFFFF 0%, #F0FDF4 100%);
    }

    .queue-column h3 {
        margin-top: 0;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #E5E7EB;
        color: #1F2937;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .queue-column .badge {
        border-radius: 999px;
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
        font-weight: 700;
    }

    .waiting-col .badge {
        background: rgba(251, 146, 60, 0.16);
        color: #C2410C;
    }

    .progress-col .badge {
        background: rgba(236, 72, 153, 0.16);
        color: #BE185D;
    }

    .completed-col .badge {
        background: rgba(34, 197, 94, 0.15);
        color: #047857;
    }

    .queue-item {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 10px;
        transition: all 0.25s ease;
    }

    .queue-item:hover {
        box-shadow: 0 10px 18px rgba(251, 146, 60, 0.12);
        transform: translateY(-2px);
    }

    .queue-item.in-progress {
        border-left: 4px solid #EC4899;
        background: linear-gradient(180deg, #FFFFFF 0%, #FDF2F8 100%);
    }

    .queue-item.completed {
        border-left: 4px solid #34D399;
        background: linear-gradient(180deg, #FFFFFF 0%, #F0FDF4 100%);
        opacity: 0.9;
    }

    .queue-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .queue-number {
        background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
        color: white;
        border-radius: 999px;
        padding: 4px 9px;
        font-size: 12px;
        font-weight: 700;
        min-width: 40px;
        text-align: center;
    }

    .pet-name {
        font-weight: 700;
        color: #1F2937;
        font-size: 1.05rem;
    }

    .pet-type {
        font-size: 13px;
        color: #6B7280;
    }

    .wait-time {
        font-size: 12px;
        color: #EA580C;
        margin-top: 6px;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
        margin-top: 10px;
    }

    .action-buttons .btn {
        border-radius: 9px;
        font-weight: 600;
        border: none;
    }

    .action-buttons .btn-primary {
        background: linear-gradient(135deg, #FB923C, #EC4899);
    }

    .action-buttons .btn-success {
        background: linear-gradient(135deg, #34D399, #10B981);
    }

    .action-buttons .btn-secondary {
        background: #FFF7ED;
        color: #C2410C;
    }

    .empty-queue-message {
        color: #9CA3AF;
        font-size: 0.92rem;
        margin-bottom: 0;
    }

    @media (max-width: 991.98px) {
        .queue-toolbar {
            display: block !important;
        }

        .queue-toolbar > .d-flex {
            margin-top: 0.8rem;
            flex-wrap: wrap;
        }

        .queue-filter-form {
            width: 100%;
            margin-right: 0;
            margin-bottom: 0.5rem;
        }

        .queue-filter-form .form-control {
            min-width: 0;
        }
    }

    @media (max-width: 1199.98px) {
        .queue-container {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between queue-toolbar">
        <h1 class="h3 queue-title">Queue Management</h1>
        <div class="d-flex">
            <form method="GET" action="{{ route('admin.queue.index') }}" class="form-inline queue-filter-form">
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
                <button type="submit" class="btn btn-sm btn-primary queue-call-btn">
                    <i class="fas fa-bell"></i> Call Next
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card waiting-stat">
                <div class="stat-value">{{ $stats['waiting'] ?? 0 }}</div>
                <div class="stat-label">Waiting</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card progress-stat">
                <div class="stat-value">{{ $stats['in_progress'] ?? 0 }}</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card completed-stat">
                <div class="stat-value">{{ $stats['completed'] ?? 0 }}</div>
                <div class="stat-label">Completed Today</div>
            </div>
        </div>
    </div>

    <div class="queue-container">
        <!-- Waiting Column -->
        <div class="queue-column waiting-col">
            <h3>Waiting <span class="badge badge-primary">{{ $appointments['waiting']->count() }}</span></h3>
            <div>
                @forelse($appointments['waiting'] as $appointment)
                    <div class="queue-item">
                        <div class="queue-item-header">
                            <span class="pet-name">{{ $appointment->pet->name ?? 'Unknown' }}</span>
                            <span class="queue-number">#{{ $appointment->queue_priority ?? 0 }}</span>
                        </div>
                        <div class="pet-type">{{ ucfirst($appointment->pet->species ?? 'N/A') }}</div>
                        <div class="pet-type">Type: {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}</div>
                        @if($appointment->arrival_time)
                            <div class="wait-time">
                                Arrived: {{ $appointment->arrival_time->format('h:i A') }}
                                ({{ now()->diffInMinutes($appointment->arrival_time) }} min ago)
                            </div>
                        @endif
                        <div class="action-buttons">
                            <form method="POST" action="{{ route('admin.queue.status.update', $appointment->id) }}" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" class="btn btn-sm btn-primary">Start</button>
                            </form>
                            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-sm btn-secondary">View</a>
                        </div>
                    </div>
                @empty
                    <p class="empty-queue-message">No waiting appointments.</p>
                @endforelse
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="queue-column progress-col">
            <h3>In Progress <span class="badge badge-info">{{ $appointments['in_progress']->count() }}</span></h3>
            <div>
                @forelse($appointments['in_progress'] as $appointment)
                    <div class="queue-item in-progress">
                        <div class="queue-item-header">
                            <span class="pet-name">{{ $appointment->pet->name ?? 'Unknown' }}</span>
                            <span class="queue-number">#{{ $appointment->queue_priority ?? 0 }}</span>
                        </div>
                        <div class="pet-type">{{ ucfirst($appointment->pet->species ?? 'N/A') }}</div>
                        <div class="pet-type">Type: {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}</div>
                        @if($appointment->arrival_time)
                            <div class="wait-time">
                                Started: {{ $appointment->arrival_time->format('h:i A') }}
                            </div>
                        @endif
                        <div class="action-buttons">
                            <form method="POST" action="{{ route('admin.queue.status.update', $appointment->id) }}" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-sm btn-success">Complete</button>
                            </form>
                            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-sm btn-secondary">View</a>
                        </div>
                    </div>
                @empty
                    <p class="empty-queue-message">No appointments in progress.</p>
                @endforelse
            </div>
        </div>

        <!-- Completed Column -->
        <div class="queue-column completed-col">
            <h3>Completed <span class="badge badge-secondary">{{ $appointments['completed']->count() }}</span></h3>
            <div>
                @forelse($appointments['completed'] as $appointment)
                    <div class="queue-item completed">
                        <div class="queue-item-header">
                            <span class="pet-name">{{ $appointment->pet->name ?? 'Unknown' }}</span>
                            <span class="queue-number">#{{ $appointment->queue_priority ?? 0 }}</span>
                        </div>
                        <div class="pet-type">{{ ucfirst($appointment->pet->species ?? 'N/A') }}</div>
                        <div class="pet-type">Type: {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}</div>
                        <div class="action-buttons">
                            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-sm btn-secondary">View</a>
                        </div>
                    </div>
                @empty
                    <p class="empty-queue-message">No completed appointments yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
