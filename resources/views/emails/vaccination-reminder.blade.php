<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Reminder</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; }
        .email-wrapper { max-width: 560px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .email-header { background: linear-gradient(135deg, #ff8c42, #ff6b6b); padding: 28px 32px; text-align: center; }
        .email-header h1 { margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; }
        .email-header p { margin: 6px 0 0; color: rgba(255,255,255,0.9); font-size: 13px; }
        .email-body { padding: 32px; }
        .reminder-card { background: #fff8f0; border: 1px solid #ffe0c2; border-left: 4px solid #ff8c42; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .reminder-card .label { font-size: 12px; color: #92400e; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 6px; }
        .reminder-card .value { font-size: 18px; color: #1e293b; font-weight: 600; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-size: 13px; }
        .detail-value { color: #1e293b; font-size: 13px; font-weight: 600; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #ff8c42, #ff6b6b); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 700; font-size: 14px; margin-top: 20px; }
        .email-footer { padding: 20px 32px; background: #f8fafc; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>🐾 Vaccination Reminder</h1>
            <p>PawCare Veterinary Clinic</p>
        </div>
        <div class="email-body">
            <p style="color: #374151; font-size: 15px; line-height: 1.6;">
                Hello! This is a friendly reminder that a vaccination for your pet is due soon.
            </p>

            <div class="reminder-card">
                <div class="label">Upcoming Vaccination</div>
                <div class="value">{{ $vaccineName }}</div>
            </div>

            <div style="margin: 20px 0;">
                <div class="detail-row">
                    <span class="detail-label">Pet</span>
                    <span class="detail-value">{{ $petName }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Due Date</span>
                    <span class="detail-value">{{ $dueDate }}</span>
                </div>
            </div>

            <p style="color: #374151; font-size: 14px; line-height: 1.6;">
                Please schedule an appointment at your earliest convenience to keep your pet's vaccinations up to date.
            </p>

            <div style="text-align: center;">
                <a href="{{ url('/customer/appointments/create') }}" class="cta-button">
                    Book Appointment
                </a>
            </div>
        </div>
        <div class="email-footer">
            <p>This is an automated reminder from PawCare Veterinary Clinic.</p>
            <p>If you have questions, please contact our clinic directly.</p>
        </div>
    </div>
</body>
</html>
