@extends('admin.dashboard')

@section('page-title', 'Appointments Overview')
@section('page-description', 'Monitor and manage scheduled visits')

@section('content')
    <style>
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .toolbar .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 10px 14px;
            border-radius: 14px;
            border: 2px solid var(--soft-gray);
            background: var(--white);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            min-width: 160px;
        }

        .appointments-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .appointments-table thead th {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12px;
            color: var(--light-text);
            padding: 0 18px 8px 18px;
            text-align: left;
        }

        .appointments-table tbody tr {
            background: var(--white);
            box-shadow: var(--shadow-soft);
            border-radius: 18px;
        }

        .appointments-table tbody td {
            padding: 18px;
            font-size: 14px;
            color: var(--dark-text);
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 12px;
            font-weight: 600;
            background: rgba(74, 144, 226, 0.15);
            color: var(--primary-blue);
        }

        .badge-status.warning {
            background: rgba(255, 140, 66, 0.18);
            color: var(--primary-orange);
        }

        .badge-status.success {
            background: rgba(95, 208, 104, 0.18);
            color: var(--accent-green);
        }

        .badge-status.danger {
            background: rgba(255, 107, 157, 0.18);
            color: var(--accent-pink);
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-outline {
            padding: 8px 14px;
            border-radius: 12px;
            border: 2px solid rgba(74, 144, 226, 0.25);
            background: transparent;
            color: var(--primary-blue);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-outline:hover {
            background: var(--primary-blue);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
        }

        .empty-state i {
            font-size: 64px;
            color: var(--soft-gray);
            margin-bottom: 20px;
            display: block;
        }

        .table-wrapper {
            overflow-x: auto;
        }
    </style>

    <div class="toolbar">
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Schedule Appointment
        </a>

        <div class="filters">
            <form method="GET" action="{{ route('admin.appointments.index') }}" class="filters">
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ ($filters['status'] ?? null) === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>

                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ ($filters['type'] ?? null) === $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if(!$hasAppointments)
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Appointments table missing</h3>
            <p>Please run migrations to enable appointment management.</p>
        </div>
    @elseif($appointments->isEmpty())
        <div class="empty-state">
            <i class="fas fa-calendar-check"></i>
            <h3>No appointments yet</h3>
            <p>Schedule your first appointment to see it listed here.</p>
        </div>
    @else
        <div class="table-wrapper">
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Pet</th>
                        <th>Owner</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Veterinarian</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                        <tr>
                            <td>
                                <strong>{{ $appointment->formatted_date }}</strong>
                                <div style="font-size: 12px; color: var(--light-text); margin-top: 4px;">
                                    #{{ $appointment->id }}
                                </div>
                            </td>
                            <td>
                                <strong>{{ $appointment->pet_name ?? 'Unnamed Pet' }}</strong>
                                <div style="font-size: 12px; color: var(--light-text);">
                                    {{ ucfirst($appointment->pet_species ?? 'N/A') }}
                                </div>
                            </td>
                            <td>{{ $appointment->owner_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-status warning">
                                    <i class="fas fa-tag"></i>
                                    {{ $appointment->type_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-status {{ $appointment->status_badge }}">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $appointment->status_label }}
                                </span>
                            </td>
                            <td>{{ $appointment->veterinarian_name ?? 'Unassigned' }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn-outline">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="btn-outline">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
