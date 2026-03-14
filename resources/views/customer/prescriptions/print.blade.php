<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription #{{ $prescription->id }}</title>
    <style>
        :root {
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #d1d5db;
            --accent: #0f172a;
            --paper: #ffffff;
            --bg: #f3f4f6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: Georgia, "Times New Roman", serif;
            line-height: 1.4;
        }

        .page-wrap {
            max-width: 920px;
            margin: 24px auto;
            padding: 0 16px 32px;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-bottom: 12px;
        }

        .toolbar .btn {
            border: 1px solid #111827;
            background: #111827;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-family: Arial, sans-serif;
            font-size: 13px;
            cursor: pointer;
        }

        .toolbar .btn.secondary {
            background: #fff;
            color: #111827;
        }

        .paper {
            background: var(--paper);
            border: 1px solid var(--line);
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            padding: 28px;
        }

        .head {
            border-bottom: 2px solid var(--accent);
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .clinic-name {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.4px;
        }

        .clinic-meta {
            color: var(--muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .rx-row {
            display: grid;
            grid-template-columns: 90px 1fr;
            gap: 10px;
            align-items: center;
            margin: 14px 0 18px;
        }

        .rx-mark {
            font-size: 58px;
            font-weight: 700;
            line-height: 1;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 16px;
            margin-bottom: 14px;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .meta-grid .field {
            border-bottom: 1px dashed var(--line);
            padding-bottom: 4px;
        }

        .meta-grid .label {
            color: var(--muted);
            display: block;
            font-size: 11px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .section {
            margin-top: 12px;
        }

        .section-title {
            font-family: Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-size: 12px;
            color: #374151;
            margin-bottom: 6px;
        }

        .line-box {
            border: 1px solid var(--line);
            padding: 10px 12px;
            min-height: 42px;
            background: #fcfcfc;
            font-size: 16px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .details-grid .item {
            border: 1px solid var(--line);
            padding: 8px 10px;
            background: #fcfcfc;
        }

        .details-grid .k {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .two-col {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .foot {
            margin-top: 24px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .signature {
            border-top: 1px solid var(--ink);
            padding-top: 6px;
            font-size: 13px;
            font-family: Arial, sans-serif;
        }

        .small {
            color: var(--muted);
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        @media (max-width: 800px) {
            .meta-grid,
            .details-grid,
            .two-col,
            .foot {
                grid-template-columns: 1fr;
            }

            .rx-row {
                grid-template-columns: 1fr;
            }

            .rx-mark {
                font-size: 46px;
            }
        }

        @media print {
            @page {
                margin: 12mm;
                size: A4;
            }

            body {
                background: #fff;
            }

            .page-wrap {
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .toolbar {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                border: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <div class="toolbar">
            <button class="btn" onclick="window.print()">Print</button>
            <a class="btn secondary" href="{{ route('customer.pets.show', $pet->id) }}">Back to Pet</a>
        </div>

        <div class="paper">
            <header class="head">
                <h1 class="clinic-name">{{ $clinicSetting->clinic_name ?? 'Veterinary Clinic' }}</h1>
                <div class="clinic-meta">
                    {{ $clinicSetting->clinic_address ?: 'Clinic address not set' }}
                    @if($clinicSetting->clinic_phone)
                        | {{ $clinicSetting->clinic_phone }}
                    @endif
                    @if($clinicSetting->clinic_email)
                        | {{ $clinicSetting->clinic_email }}
                    @endif
                </div>
            </header>

            <div class="meta-grid">
                <div class="field">
                    <span class="label">Prescription No.</span>
                    <strong>#{{ $prescription->id }}</strong>
                </div>
                <div class="field">
                    <span class="label">Issued Date</span>
                    <strong>{{ $prescription->created_at ? $prescription->created_at->format('M d, Y h:i A') : 'N/A' }}</strong>
                </div>
                <div class="field">
                    <span class="label">Pet Name</span>
                    <strong>{{ $pet->name }}</strong>
                </div>
                <div class="field">
                    <span class="label">Species / Breed</span>
                    <strong>{{ $pet->species }}{{ $pet->breed ? ' / ' . $pet->breed : '' }}</strong>
                </div>
                <div class="field">
                    <span class="label">Owner</span>
                    <strong>{{ $pet->owner->user->first_name ?? '' }} {{ $pet->owner->user->last_name ?? '' }}</strong>
                </div>
                <div class="field">
                    <span class="label">Owner Contact</span>
                    <strong>{{ $pet->owner->user->contact_number ?? 'N/A' }}</strong>
                </div>
            </div>

            <div class="rx-row">
                <div class="rx-mark">Rx</div>
                <div>
                    <div class="section-title">Medication</div>
                    <div class="line-box"><strong>{{ $prescription->medication_name }}</strong></div>
                </div>
            </div>

            <div class="details-grid">
                <div class="item">
                    <div class="k">Dosage</div>
                    <div>{{ $prescription->dosage }}</div>
                </div>
                <div class="item">
                    <div class="k">Frequency</div>
                    <div>{{ $prescription->frequency }}</div>
                </div>
                <div class="item">
                    <div class="k">Duration</div>
                    <div>{{ $prescription->duration_days }} day(s)</div>
                </div>
                <div class="item">
                    <div class="k">Quantity</div>
                    <div>{{ $prescription->quantity }}</div>
                </div>
                <div class="item">
                    <div class="k">Dispense Status</div>
                    <div>{{ $prescription->dispensed ? 'Dispensed' : 'Pending' }}</div>
                </div>
                <div class="item">
                    <div class="k">Dispensed At</div>
                    <div>{{ $prescription->dispensed_at ? $prescription->dispensed_at->format('M d, Y h:i A') : 'N/A' }}</div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Administration Instructions</div>
                <div class="line-box">{{ $prescription->instructions ?: 'No special instructions provided.' }}</div>
            </div>

            <div class="two-col">
                <div class="section">
                    <div class="section-title">Medical Record Reference</div>
                    <div class="line-box">
                        Visit Date: {{ optional($prescription->medicalRecord?->visit_date)->format('M d, Y') ?? 'N/A' }}
                        <br>
                        Complaint: {{ $prescription->medicalRecord?->complaint ?? 'N/A' }}
                        <br>
                        Diagnosis: {{ $prescription->medicalRecord?->diagnosis ?? 'N/A' }}
                    </div>
                </div>
                <div class="section">
                    <div class="section-title">Prescription Source & Assignment</div>
                    <div class="line-box">
                        Prescribed By: 
                        @if($prescription->medicalRecord?->veterinarian)
                            Dr. {{ $prescription->medicalRecord->veterinarian->first_name }} {{ $prescription->medicalRecord->veterinarian->last_name }}
                        @else
                            N/A
                        @endif
                        <br>
                        Assigned Staff: {{ $prescription->assignedStaff ? ($prescription->assignedStaff->first_name . ' ' . $prescription->assignedStaff->last_name) : 'N/A' }}
                        <br>
                        Dispensed By: {{ $prescription->dispensedBy ? ($prescription->dispensedBy->first_name . ' ' . $prescription->dispensedBy->last_name) : 'N/A' }}
                        <br>
                        External Clinic: {{ $prescription->external_clinic_name ?: 'N/A' }}
                        <br>
                        External Veterinarian: {{ $prescription->external_veterinarian_name ?: 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="foot">
                <div>
                    <div class="signature">
                        @if($prescription->medicalRecord?->veterinarian)
                            Dr. {{ $prescription->medicalRecord->veterinarian->first_name }} {{ $prescription->medicalRecord->veterinarian->last_name }}
                        @else
                            Prescribing Veterinarian
                        @endif
                    </div>
                    <div class="small">Prescribing Veterinarian</div>
                </div>
                <div>
                    <div class="signature">
                        {{ $clinicSetting->clinic_name ?? 'Veterinary Clinic' }}
                    </div>
                    <div class="small">Clinic Stamp / Authorized Signature</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
