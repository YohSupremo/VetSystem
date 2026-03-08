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
            -webkit-overflow-scrolling: touch;
        }

        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .toolbar .filters {
                flex-direction: column;
                gap: 8px;
            }

            .filter-select {
                width: 100%;
                min-width: auto;
            }

            .appointments-table {
                font-size: 12px;
            }

            .appointments-table thead th {
                padding: 8px 10px;
                font-size: 10px;
            }

            .appointments-table tbody td {
                padding: 12px 8px;
                font-size: 11px;
            }

            .actions {
                flex-direction: column;
                gap: 6px;
            }

            .btn-outline {
                padding: 6px 10px;
                font-size: 11px;
                width: 100%;
                justify-content: center;
            }

            .badge-status {
                font-size: 10px;
                padding: 4px 8px;
            }
        }

        @media (max-width: 576px) {
            .toolbar {
                margin-bottom: 16px;
            }

            .appointments-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .appointments-table thead {
                display: none;
            }

            .appointments-table tbody,
            .appointments-table tr,
            .appointments-table td {
                display: block;
                width: 100% !important;
            }

            .appointments-table tr {
                margin-bottom: 16px;
                border: 1px solid var(--soft-gray);
                border-radius: 12px;
                padding: 12px;
                background: var(--white);
                box-shadow: var(--shadow-soft);
            }

            .appointments-table td {
                text-align: left !important;
                padding: 8px 0 !important;
                border: none !important;
                position: relative;
                padding-left: 35% !important;
            }

            .appointments-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 30%;
                font-weight: 600;
                color: var(--light-text);
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .actions {
                flex-direction: row;
                justify-content: space-between;
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid var(--soft-gray);
            }

            .btn-outline {
                flex: 1;
                margin: 0 2px;
            }
        }
    </style>

    <div class="toolbar">
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Schedule Appointment
        </a>

        <div class="filters">
            <form method="GET" action="{{ route('admin.appointments.index') }}" class="filters">
                <input
                    type="text"
                    name="filter[search]"
                    class="filter-select"
                    value="{{ request('filter.search') }}"
                    placeholder="Search pet, owner, staff"
                >
                <select name="pet_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Pets</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ ($filters['pet_id'] ?? null) == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }} - {{ ucfirst($pet->species ?? 'N/A') }}
                        </option>
                    @endforeach
                </select>

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
                <button type="submit" class="btn-outline">Search</button>
                @if(request('filter.search') || ($filters['pet_id'] ?? null) || ($filters['status'] ?? null) || ($filters['type'] ?? null))
                    <a href="{{ route('admin.appointments.index') }}" class="btn-outline">Clear</a>
                @endif
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
                            <td data-label="When">
                                <strong>{{ $appointment->formatted_date }}</strong>
                                <div style="font-size: 12px; color: var(--light-text); margin-top: 4px;">
                                    #{{ $appointment->id }}
                                </div>
                            </td>
                            <td data-label="Pet">
                                <strong>{{ $appointment->pet_name ?? 'Unnamed Pet' }}</strong>
                                <div style="font-size: 12px; color: var(--light-text);">
                                    {{ ucfirst($appointment->pet_species ?? 'N/A') }}
                                </div>
                            </td>
                            <td data-label="Owner">{{ $appointment->owner_name ?? 'N/A' }}</td>
                            <td data-label="Type">
                                <span class="badge-status warning">
                                    <i class="fas fa-tag"></i>
                                    {{ $appointment->type_label }}
                                </span>
                            </td>
                            <td data-label="Status">
                                <span class="badge-status {{ $appointment->status_badge }}">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $appointment->status_label }}
                                </span>
                            </td>
                            <td data-label="Veterinarian">{{ $appointment->veterinarian_name ?? 'Unassigned' }}</td>
                            <td data-label="Actions">
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
