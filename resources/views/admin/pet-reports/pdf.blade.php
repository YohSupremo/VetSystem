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
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
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
        <div class="stat-box">
            <h4>Surgeries</h4>
            <div class="number">{{ $stats['total_surgeries'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Incidents</h4>
            <div class="number">{{ $stats['total_incidents'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Chronic Conditions</h4>
            <div class="number">{{ $stats['total_chronic_conditions'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Allergies</h4>
            <div class="number">{{ $stats['total_allergies'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Cage Assignments</h4>
            <div class="number">{{ $stats['total_cage_assignments'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Grooming</h4>
            <div class="number">{{ $stats['total_grooming_appointments'] }}</div>
        </div>
        <div class="stat-box">
            <h4>Lab Tests</h4>
            <div class="number">{{ $stats['total_lab_tests'] }}</div>
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

    <div class="page-break"></div>

    <div class="section">
        <h2>Surgical History</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Procedure</th>
                    <th>Surgeon</th>
                    <th>Status</th>
                    <th>Outcome</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['surgeries']) && count($tables['surgeries']) > 0)
                    @foreach($tables['surgeries'] as $surgery)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($surgery['scheduled_date'])->format('M j, Y') }}</td>
                            <td>{{ $surgery['surgery_type'] ?? 'N/A' }}</td>
                            <td>{{ $surgery['surgeon_name'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $surgery['status'] ?? 'scheduled' }}">
                                    {{ ucfirst($surgery['status'] ?? 'Scheduled') }}
                                </span>
                            </td>
                            <td>{{ Str::limit($surgery['outcome'], 50) ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic;">No surgical records found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Incident Reports</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Reported By</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['incidents']) && count($tables['incidents']) > 0)
                    @foreach($tables['incidents'] as $incident)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($incident['incident_date'])->format('M j, Y') }}</td>
                            <td>{{ $incident['incident_type'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($incident['severity'] ?? 'low') }}">
                                    {{ ucfirst($incident['severity'] ?? 'Low') }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $incident['status'] ?? 'open' }}">
                                    {{ ucfirst($incident['status'] ?? 'Open') }}
                                </span>
                            </td>
                            <td>{{ $incident['reported_by'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic;">No incident reports found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Chronic Conditions</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Diagnosed</th>
                    <th>Condition</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Ongoing Treatment</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['chronic_conditions']) && count($tables['chronic_conditions']) > 0)
                    @foreach($tables['chronic_conditions'] as $condition)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($condition['diagnosed_date'])->format('M j, Y') }}</td>
                            <td>{{ $condition['condition_name'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($condition['severity'] ?? 'moderate') }}">
                                    {{ ucfirst($condition['severity'] ?? 'Moderate') }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $condition['status'] ?? 'active' }}">
                                    {{ ucfirst($condition['status'] ?? 'Active') }}
                                </span>
                            </td>
                            <td>{{ $condition['ongoing_treatment'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic;">No chronic conditions found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Allergies & Sensitivities</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Diagnosed</th>
                    <th>Allergen</th>
                    <th>Severity</th>
                    <th>Reaction Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['allergies']) && count($tables['allergies']) > 0)
                    @foreach($tables['allergies'] as $allergy)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($allergy['diagnosed_date'])->format('M j, Y') }}</td>
                            <td>{{ $allergy['allergen'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($allergy['severity'] ?? 'moderate') }}">
                                    {{ ucfirst($allergy['severity'] ?? 'Moderate') }}
                                </span>
                            </td>
                            <td>{{ $allergy['reaction_type'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $allergy['status'] ?? 'active' }}">
                                    {{ ucfirst($allergy['status'] ?? 'Active') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic;">No allergies found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Boarding History</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Cage</th>
                    <th>Days Stayed</th>
                    <th>Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['boarding_history']) && count($tables['boarding_history']) > 0)
                    @foreach($tables['boarding_history'] as $boarding)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($boarding['start_date'])->format('M j, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($boarding['end_date'])->format('M j, Y') }}</td>
                            <td>{{ $boarding['cage_number'] ?? 'N/A' }}</td>
                            <td>{{ $boarding['days_stayed'] ?? 'N/A' }}</td>
                            <td>${{number_format($boarding['total_cost'] ?? 0, 2)}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic;">No boarding history found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Grooming Sessions</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Service</th>
                    <th>Groomer</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['grooming_sessions']) && count($tables['grooming_sessions']) > 0)
                    @foreach($tables['grooming_sessions'] as $grooming)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($grooming['appointment_date'])->format('M j, Y') }}</td>
                            <td>{{ $grooming['service_name'] ?? 'N/A' }}</td>
                            <td>{{ $grooming['groomer_name'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $grooming['status'] ?? 'scheduled' }}">
                                    {{ ucfirst($grooming['status'] ?? 'Scheduled') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center; font-style: italic;">No grooming sessions found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Laboratory Test Results</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Requested</th>
                    <th>Test Name</th>
                    <th>Status</th>
                    <th>Result Date</th>
                    <th>Results</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['lab_tests']) && count($tables['lab_tests']) > 0)
                    @foreach($tables['lab_tests'] as $test)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($test['requested_date'])->format('M j, Y') }}</td>
                            <td>{{ $test['test_name'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $test['status'] ?? 'pending' }}">
                                    {{ ucfirst($test['status'] ?? 'Pending') }}
                                </span>
                            </td>
                            <td>{{ $test['result_date'] ? \Carbon\Carbon::parse($test['result_date'])->format('M j, Y') : 'N/A' }}</td>
                            <td>{{ Str::limit($test['results'], 60) ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic;">No laboratory tests found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Financial Records</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice Date</th>
                    <th>Invoice Number</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tables['invoices']) && count($tables['invoices']) > 0)
                    @foreach($tables['invoices'] as $invoice)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($invoice['issue_date'])->format('M j, Y') }}</td>
                            <td>{{ $invoice['invoice_number'] ?? 'N/A' }}</td>
                            <td>${{number_format($invoice['total_amount'] ?? 0, 2)}}</td>
                            <td>
                                <span class="status-badge status-{{ $invoice['status'] ?? 'pending' }}">
                                    {{ ucfirst($invoice['status'] ?? 'Pending') }}
                                </span>
                            </td>
                            <td>{{ $invoice['due_date'] ? \Carbon\Carbon::parse($invoice['due_date'])->format('M j, Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic;">No invoices found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="footer">
        <p>This report was generated from the PawCare Veterinary Management System</p>
        <p>For any questions, please contact the clinic directly.</p>
        <p>Page <span class="pagenum"></span> of <span class="pagecount"></span></p>
    </div>
</body>
</html>
