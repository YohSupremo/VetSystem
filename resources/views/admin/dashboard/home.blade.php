@extends('admin.dashboard')

@section('page-title', 'Clinic Overview')
@section('page-description', "A snapshot of today's operations and key metrics")

@section('content')
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1/dist/apexcharts.css">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1"></script>
    @endpush

    <style>
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px;
            margin-bottom: 30px;
        }

        .metric-card {
            border-radius: 18px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            color: var(--dark-text);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.2s ease;
            background: var(--white);
            box-shadow: var(--shadow-soft);
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .metric-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 140, 66, 0.18), transparent 55%);
            opacity: 0.6;
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: var(--shadow-soft);
            color: var(--primary-orange);
            z-index: 1;
        }

        .metric-label {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--light-text);
            font-weight: 600;
            z-index: 1;
        }

        .metric-value {
            font-size: 34px;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            color: var(--dark-text);
            z-index: 1;
        }

        .metric-subtext {
            font-size: 13px;
            color: var(--light-text);
            z-index: 1;
        }

        .dashboard-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .dashboard-section .card {
            height: 100%;
        }

        .chart-container {
            min-height: 320px;
        }

        .chart-section {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(220px, 320px);
            gap: 18px;
            align-items: stretch;
        }

        .species-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            max-height: 320px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .species-card {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 16px;
            border: 1px solid rgba(255, 140, 66, 0.12);
            padding: 14px 16px;
            box-shadow: var(--shadow-soft);
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 180px;
        }

        .species-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .species-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Fredoka', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--dark-text);
        }

        .species-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .species-count {
            font-size: 12px;
            font-weight: 700;
            color: var(--light-text);
            background: rgba(255, 140, 66, 0.12);
            padding: 4px 10px;
            border-radius: 999px;
        }

        .species-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 160px;
            overflow-y: auto;
        }

        .species-list li {
            font-size: 13px;
            color: var(--light-text);
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 6px 8px;
            border: 1px solid rgba(255, 140, 66, 0.08);
        }

        .species-empty {
            font-size: 13px;
            color: var(--light-text);
            padding: 8px 0;
        }

        .chart-panel {
            min-height: 320px;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .chart-legend.scrollable {
            max-height: 320px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--light-text);
        }

        .legend-item.large {
            align-items: flex-start;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid rgba(255, 140, 66, 0.12);
            width: 100%;
        }

        .legend-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            line-height: 1.2;
        }

        .legend-label {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark-text);
        }

        .legend-count {
            font-size: 12px;
            font-weight: 600;
            color: var(--light-text);
        }

        .chart-legend.scrollable.grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .chart-legend.grid {
            display: grid;
            gap: 12px;
        }

        .chart-legend.grid.two-col {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pipeline-legend .legend-item.large {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .pipeline-legend .legend-pill {
            max-width: 100%;
            white-space: normal;
            line-height: 1.1;
        }

        .dashboard-section.compact {
            align-items: start;
        }

        .appointment-pipeline .chart-legend {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }

        .appointment-pipeline .chart-section {
            grid-template-columns: 1fr;
            justify-items: center;
        }

        .appointment-pipeline .chart-panel,
        .appointment-pipeline .chart-legend {
            width: 100%;
            max-width: 420px;
        }

        .legend-pill {
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            color: var(--white);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @media (max-width: 1200px) {
            .chart-section {
                grid-template-columns: 1fr;
            }

            .chart-legend.scrollable {
                max-height: 220px;
            }

            .species-grid {
                grid-template-columns: 1fr;
            }
        }

        .legend-color {
            width: 14px;
            height: 14px;
            border-radius: 4px;
        }

        .list-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .list-item {
            background: rgba(255, 255, 255, 0.75);
            border-radius: 16px;
            padding: 16px;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 14px;
            align-items: center;
            transition: transform 0.2s ease;
            border: 1px solid rgba(255, 140, 66, 0.08);
        }

        .list-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        .list-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 140, 66, 0.12);
            color: var(--primary-orange);
            font-size: 18px;
        }

        .list-content h4 {
            margin: 0 0 6px 0;
            font-size: 15px;
            font-family: 'Fredoka', sans-serif;
            color: var(--dark-text);
        }

        .list-content p {
            margin: 0;
            font-size: 13px;
            color: var(--light-text);
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            background: rgba(74, 144, 226, 0.15);
            color: var(--primary-blue);
        }

        .badge-warning {
            background: rgba(255, 140, 66, 0.15);
            color: var(--primary-orange);
        }

        .badge-success {
            background: rgba(95, 208, 104, 0.15);
            color: var(--accent-green);
        }

        .card-columns {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-radius: 12px;
            background: rgba(255, 140, 66, 0.08);
            margin-bottom: 10px;
        }

        .stat-row span {
            font-size: 13px;
            font-weight: 600;
            color: var(--light-text);
        }

        .stat-row strong {
            font-size: 15px;
            color: var(--dark-text);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--light-text);
        }

        .empty-state i {
            font-size: 42px;
            color: var(--soft-gray);
            margin-bottom: 12px;
            display: block;
        }

        @media (max-width: 992px) {
            .dashboard-section {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-paw"></i>
            </div>
            <div class="metric-label">Total Pets</div>
            <div class="metric-value">{{ number_format($petCount) }}</div>
            <div class="metric-subtext">Active patient profiles</div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, rgba(74, 144, 226, 0.15), rgba(155, 126, 222, 0.15));">
            <div class="metric-icon" style="color: var(--primary-blue);">
                <i class="fas fa-users"></i>
            </div>
            <div class="metric-label">Pet Owners</div>
            <div class="metric-value">{{ number_format($petOwnerCount) }}</div>
            <div class="metric-subtext">Registered guardians & families</div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, rgba(95, 208, 104, 0.18), rgba(255, 255, 255, 0.95));">
            <div class="metric-icon" style="color: var(--accent-green);">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="metric-label">Active Appointments</div>
            <div class="metric-value">{{ number_format($activeAppointmentsCount) }}</div>
            <div class="metric-subtext">Pending + confirmed + in-progress</div>
        </div>

        <div class="metric-card" style="background: linear-gradient(135deg, rgba(255, 108, 96, 0.18), rgba(255, 255, 255, 0.95));">
            <div class="metric-icon" style="color: var(--accent-pink);">
                <i class="fas fa-sun"></i>
            </div>
            <div class="metric-label">Today</div>
            <div class="metric-value">{{ number_format($appointmentsToday) }}</div>
            <div class="metric-subtext">Appointments scheduled for {{ now()->format('M d, Y') }}</div>
        </div>
    </div>

    <div class="dashboard-section compact">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-paw"></i> Species Mix</h3>
            </div>
            <div class="species-grid">
                @foreach($speciesChart['labels'] as $index => $label)
                    <div class="species-card">
                        <div class="species-card-header">
                            <div class="species-title">
                                <span class="species-dot" style="background: {{ $speciesChart['colors'][$index] }}"></span>
                                {{ $label }}
                            </div>
                            <span class="species-count">{{ $speciesChart['counts'][$index] }} pets</span>
                        </div>
                        @php
                            $speciesKey = strtolower(str_replace(' ', '_', $label));
                            $petNames = $speciesPets[$speciesKey] ?? [];
                        @endphp
                        @if(count($petNames) > 0)
                            <ul class="species-list">
                                @foreach($petNames as $petName)
                                    <li>{{ $petName }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="species-empty">No pets registered yet.</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card appointment-pipeline">
            <div class="card-header">
                <h3><i class="fas fa-notes-medical"></i> Appointment Pipeline</h3>
            </div>
            @if($appointmentStatusChart['hasData'])
                <div class="chart-legend grid two-col pipeline-legend">
                    @foreach($appointmentStatusChart['labels'] as $index => $label)
                        <div class="legend-item large">
                            <span class="legend-pill" style="background: {{ $appointmentStatusChart['colors'][$index] }}">
                                {{ $label }}
                            </span>
                            <div class="legend-text">
                                <span class="legend-label">{{ $appointmentStatusChart['counts'][$index] }}</span>
                                <span class="legend-count">appointments</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>Schedule appointments to visualize pipeline status.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card-columns">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-stethoscope"></i> Next Up (5)</h3>
            </div>

            @if($upcomingAppointments->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-calendar"></i>
                    <p>No upcoming appointments scheduled.</p>
                </div>
            @else
                <div class="list-group">
                    @foreach($upcomingAppointments as $appt)
                        <div class="list-item">
                            <div class="list-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="list-content">
                                <h4>{{ $appt->formatted_date }}</h4>
                                <p>
                                    <strong>{{ $appt->pet_name ?? 'Unnamed Pet' }}</strong>
                                    @if($appt->owner_name)
                                        • Owner: {{ $appt->owner_name }}
                                    @endif
                                </p>
                                <p>
                                    <span class="badge-status badge-warning">
                                        <i class="fas fa-tag"></i> {{ $appt->type_label }}
                                    </span>
                                    <span class="badge-status" style="margin-left: 8px;">
                                        <i class="fas fa-info-circle"></i> {{ $appt->status_label }}
                                    </span>
                                    @if($appt->veterinarian_name)
                                        <span class="badge-status badge-success" style="margin-left: 8px;">
                                            <i class="fas fa-user-md"></i> {{ $appt->veterinarian_name }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-syringe"></i> Vaccinations Due Soon</h3>
                <span class="badge-status {{ $vaccinationsDueSoonCount ? 'badge-warning' : '' }}">
                    <i class="fas fa-hourglass-half"></i> {{ $vaccinationsDueSoonCount }} due</span>
            </div>

            @if($vaccinationsDueSoon->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-shield-dog"></i>
                    <p>No vaccinations due within the next 30 days.</p>
                </div>
            @else
                <div class="list-group">
                    @foreach($vaccinationsDueSoon as $vaccination)
                        <div class="list-item">
                            <div class="list-icon" style="background: rgba(95, 208, 104, 0.18); color: var(--accent-green);">
                                <i class="fas fa-syringe"></i>
                            </div>
                            <div class="list-content">
                                <h4>{{ $vaccination->pet_name }} • {{ $vaccination->vaccine_name ?? 'Vaccine TBD' }}</h4>
                                <p>Due on <strong>{{ $vaccination->formatted_due_date }}</strong> • Dose #{{ $vaccination->dose_number ?? 'N/A' }}</p>
                                @if($vaccination->administered_by)
                                    <p>Last administered by {{ $vaccination->administered_by }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="dashboard-section" style="margin-top: 24px;">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-dog"></i> Recent Pet Registrations</h3>
            </div>
            @if($recentPets->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-paw"></i>
                    <p>No new pets registered yet.</p>
                </div>
            @else
                <div class="list-group">
                    @foreach($recentPets as $pet)
                        <div class="list-item">
                            <div class="list-icon" style="background: rgba(155, 126, 222, 0.18); color: var(--accent-purple);">
                                <i class="fas fa-dog"></i>
                            </div>
                            <div class="list-content">
                                <h4>{{ $pet['name'] }}</h4>
                                <p>{{ $pet['species'] }} • {{ $pet['breed'] ?? 'Breed N/A' }}</p>
                                <p>Owner: {{ $pet['owner'] }} • Gender: {{ $pet['gender'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-warehouse"></i> Boarding & Inventory Alerts</h3>
            </div>
            <div class="stat-row">
                <span>Boarding Occupancy</span>
                <strong>{{ $occupiedCages }} / {{ $boardingCapacity }} cages ({{ $boardingOccupancy }}%)</strong>
            </div>
            <div class="stat-row">
                <span>Critical Inventory Alerts</span>
                <strong>{{ $lowStockCount }} items</strong>
            </div>

            @if($lowStockItems->isNotEmpty())
                <div class="list-group" style="margin-top: 14px;">
                    @foreach($lowStockItems as $item)
                        <div class="list-item">
                            <div class="list-icon" style="background: rgba(255, 140, 66, 0.18); color: var(--primary-orange);">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="list-content">
                                <h4>{{ $item->item_name }}</h4>
                                <p>Category: {{ ucfirst($item->category) }} • Supplier: {{ $item->supplier_name ?? 'N/A' }}</p>
                                <p class="badge-status badge-warning">Qty {{ $item->quantity }} / Min {{ $item->min_stock }} • Exp: {{ $item->expiry_label }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-boxes"></i>
                    <p>All inventory items are adequately stocked.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3><i class="fas fa-birthday-cake"></i> Pet Age Insights</h3>
        </div>
        @if($petAgeStats['average'] === null)
            <div class="empty-state">
                <i class="fas fa-dog"></i>
                <p>Add birth dates to pet profiles to unlock age analytics.</p>
            </div>
        @else
            <div class="stat-row">
                <span>Average Age</span>
                <strong>{{ $petAgeStats['average'] }} yrs</strong>
            </div>
            <div class="stat-row">
                <span>Youngest Patient</span>
                <strong>{{ $petAgeStats['youngest'] }} yrs</strong>
            </div>
            <div class="stat-row">
                <span>Oldest Patient</span>
                <strong>{{ $petAgeStats['oldest'] }} yrs</strong>
            </div>
            <p class="metric-subtext" style="margin: 10px 0 0 0;">Based on {{ $petAgeStats['count'] }} pets with birth dates.</p>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const speciesChartEl = document.querySelector('#speciesChart');
            if (speciesChartEl && @json($speciesChart['hasData'])) {
                const speciesChart = new ApexCharts(speciesChartEl, {
                    chart: {
                        type: 'donut',
                        height: 320,
                        toolbar: { show: false }
                    },
                    series: @json($speciesChart['counts']),
                    labels: @json($speciesChart['labels']),
                    colors: @json($speciesChart['colors']),
                    legend: { show: false },
                    stroke: { colors: ['#ffffff'], width: 3 },
                    dataLabels: {
                        style: {
                            fontFamily: 'DM Sans, sans-serif',
                            fontWeight: 600
                        },
                        formatter: (val) => `${val.toFixed(1)}%`
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '68%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontFamily: 'Fredoka, sans-serif',
                                        color: 'var(--dark-text)'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '24px',
                                        fontFamily: 'Fredoka, sans-serif',
                                        formatter: (val) => val
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: () => @json(array_sum($speciesChart['counts']))
                                    }
                                }
                            }
                        }
                    }
                });
                speciesChart.render();
            }

            const appointmentChartEl = document.querySelector('#appointmentStatusChart');
            if (appointmentChartEl && @json($appointmentStatusChart['hasData'])) {
                const appointmentChart = new ApexCharts(appointmentChartEl, {
                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: { show: false }
                    },
                    series: [{
                        data: @json($appointmentStatusChart['counts'])
                    }],
                    xaxis: {
                        categories: @json($appointmentStatusChart['labels']),
                        labels: {
                            style: {
                                fontFamily: 'DM Sans, sans-serif',
                                colors: 'var(--light-text)'
                            }
                        }
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '50%',
                            borderRadius: 8
                        }
                    },
                    colors: @json($appointmentStatusChart['colors']),
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '13px',
                            fontFamily: 'DM Sans, sans-serif'
                        }
                    },
                    grid: {
                        borderColor: 'rgba(0, 0, 0, 0.05)'
                    }
                });
                appointmentChart.render();
            }
        });
    </script>
@endsection
