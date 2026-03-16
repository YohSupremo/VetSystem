<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pet Report - {{ $pet->name }}</title>
    <style>
        @page {
            margin: 20px 20px 20px 20px;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4A90E2;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #4A90E2;
            margin: 0;
            font-size: 28px;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        
        .pet-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4A90E2;
        }
        
        .pet-info h3 {
            margin: 0 0 10px 0;
            color: #4A90E2;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-box {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-box h4 {
            margin: 0 0 5px 0;
            color: #4A90E2;
            font-size: 14px;
        }
        
        .stat-box .number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section h2 {
            color: #4A90E2;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th {
            background: #4A90E2;
            color: white;
            font-weight: bold;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .data-table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .data-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-active {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐾 Pet Health Report</h1>
        <p>Generated on {{ now()->format('F j, Y') }} at {{ now()->format('g:i A') }}</p>
    </div>

    <div class="pet-info">
        <h3>Pet Information</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 20%; font-weight: bold;">Name:</td>
                <td>{{ $pet->name }}</td>
                <td style="width: 20%; font-weight: bold;">Species:</td>
                <td>{{ $pet->species }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Breed:</td>
                <td>{{ $pet->breed ?? 'N/A' }}</td>
                <td style="font-weight: bold;">Age:</td>
                <td>{{ $pet->age ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Owner:</td>
                <td>{{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}</td>
                <td style="font-weight: bold;">Contact:</td>
                <td>{{ $pet->owner->user->email }}</td>
            </tr>
        </table>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <h4>Total Visits</h4>
            <div class="number">{{ $stats['total_visits'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Prescriptions</h4>
            <div class="number">{{ $stats['total_prescriptions'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Vaccinations</h4>
            <div class="number">{{ $stats['total_vaccinations'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Medical Records</h4>
            <div class="number">{{ $stats['total_medical_records'] }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Medical Records</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                    <th>Veterinarian</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['medical_records']) && count($tables['medical_records']) > 0)
                    @foreach($tables['medical_records'] as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record['visit_date'])->format('M j, Y') }}</td>
                            <td>{{ Str::limit($record['diagnosis'], 50) }}</td>
                            <td>{{ Str::limit($record['treatment_plan'] ?? 'N/A', 60) }}</td>
                            <td>{{ $record['vet_name'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center; font-style: italic;">No medical records found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Prescription History</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['prescriptions']) && count($tables['prescriptions']) > 0)
                    @foreach($tables['prescriptions'] as $prescription)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($prescription['created_at'])->format('M j, Y') }}</td>
                            <td>{{ $prescription['medication_name'] ?? 'N/A' }}</td>
                            <td>{{ $prescription['dosage'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $prescription['status'] ?? 'active' }}">
                                    {{ ucfirst($prescription['status'] ?? 'Active') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center; font-style: italic;">No prescriptions found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Appointment History</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Veterinarian</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['appointments']) && count($tables['appointments']) > 0)
                    @foreach($tables['appointments'] as $appointment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($appointment['appointment_date'])->format('M j, Y') }}</td>
                            <td>{{ $appointment['type'] ?? 'General' }}</td>
                            <td>
                                <span class="status-badge status-{{ $appointment['status'] ?? 'scheduled' }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment['status'] ?? 'Scheduled')) }}
                                </span>
                            </td>
                            <td>{{ $appointment['vet_name'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center; font-style: italic;">No appointments found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Vaccination Records</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date Administered</th>
                    <th>Vaccine</th>
                    <th>Next Due Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['vaccinations']) && count($tables['vaccinations']) > 0)
                    @foreach($tables['vaccinations'] as $vaccination)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($vaccination['administered_date'])->format('M j, Y') }}</td>
                            <td>{{ $vaccination['vaccine_name'] ?? 'N/A' }}</td>
                            <td>{{ $vaccination['next_due_date'] ? \Carbon\Carbon::parse($vaccination['next_due_date'])->format('M j, Y') : 'N/A' }}</td>
                            <td>{{ $vaccination['notes'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center; font-style: italic;">No vaccination records found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated from the PawCare Veterinary Management System</p>
        <p>For any questions, please contact the clinic directly.</p>
        <p>Page <span class="pagenum"></span> of <span class="pagecount"></span></p>
    </div>
</body>
</html>
