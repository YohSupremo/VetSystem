@extends('admin.dashboard')

@section('content')
<style>
    .settings-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .settings-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        height: 100%;
    }

    .settings-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .settings-card-title {
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .settings-card-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        margin-top: 0.3rem;
    }

    .icon-pill {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #4338ca;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.65rem 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15);
    }

    .setting-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.75rem;
    }

    .setting-toggle label {
        margin: 0;
        font-weight: 600;
        color: #0f172a;
    }

    .setting-toggle small {
        color: #64748b;
        display: block;
        margin-top: 0.2rem;
    }

    .save-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding: 1rem 1.25rem;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .save-bar p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
    }
</style>

<div class="content-header settings-hero">
    <div class="header-title">
        <h1><i class="fas fa-cog"></i> System Settings</h1>
        <p>Configure clinic settings, preferences, and operational defaults</p>
    </div>
</div>

<form action="{{ route('admin.settings.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="settings-card">
                <div class="settings-card-header">
                    <div>
                        <h5 class="settings-card-title">Clinic Profile</h5>
                        <div class="settings-card-subtitle">Public-facing clinic information.</div>
                    </div>
                    <div class="icon-pill"><i class="fas fa-hospital"></i></div>
                </div>
                <div class="mb-3">
                    <label for="clinic_name" class="form-label">Clinic Name</label>
                    <input type="text" class="form-control" id="clinic_name" name="clinic_name" value="{{ old('clinic_name') }}" placeholder="Paws & Care Veterinary Clinic">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="clinic_email" class="form-label">Clinic Email</label>
                        <input type="email" class="form-control" id="clinic_email" name="clinic_email" value="{{ old('clinic_email') }}" placeholder="contact@clinic.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="clinic_phone" class="form-label">Clinic Phone</label>
                        <input type="text" class="form-control" id="clinic_phone" name="clinic_phone" value="{{ old('clinic_phone') }}" placeholder="+63 900 000 0000">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="clinic_address" class="form-label">Clinic Address</label>
                    <textarea class="form-control" id="clinic_address" name="clinic_address" rows="3" placeholder="Enter clinic address">{{ old('clinic_address') }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="clinic_logo" class="form-label">Logo URL</label>
                        <input type="text" class="form-control" id="clinic_logo" name="clinic_logo" value="{{ old('clinic_logo') }}" placeholder="https://.../logo.png">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="clinic_website" class="form-label">Website</label>
                        <input type="text" class="form-control" id="clinic_website" name="clinic_website" value="{{ old('clinic_website') }}" placeholder="https://clinic.com">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="settings-card">
                <div class="settings-card-header">
                    <div>
                        <h5 class="settings-card-title">Operating Hours</h5>
                        <div class="settings-card-subtitle">Define clinic schedule and availability.</div>
                    </div>
                    <div class="icon-pill"><i class="fas fa-clock"></i></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="open_time" class="form-label">Opening Time</label>
                        <input type="time" class="form-control" id="open_time" name="open_time" value="{{ old('open_time', '08:00') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="close_time" class="form-label">Closing Time</label>
                        <input type="time" class="form-control" id="close_time" name="close_time" value="{{ old('close_time', '18:00') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="timezone" class="form-label">Timezone</label>
                        <select class="form-select" id="timezone" name="timezone">
                            <option value="Asia/Manila" selected>Asia/Manila</option>
                            <option value="UTC">UTC</option>
                            <option value="America/Los_Angeles">America/Los Angeles</option>
                            <option value="Europe/London">Europe/London</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date_format" class="form-label">Date Format</label>
                        <select class="form-select" id="date_format" name="date_format">
                            <option value="M d, Y">Feb 08, 2026</option>
                            <option value="d M Y">08 Feb 2026</option>
                            <option value="Y-m-d">2026-02-08</option>
                        </select>
                    </div>
                </div>
                <div class="setting-toggle">
                    <div>
                        <label for="weekend_enabled">Weekend Appointments</label>
                        <small>Allow appointments on Saturdays and Sundays.</small>
                    </div>
                    <input class="form-check-input" type="checkbox" id="weekend_enabled" name="weekend_enabled" value="1" {{ old('weekend_enabled') ? 'checked' : '' }}>
                </div>
                <div class="setting-toggle">
                    <div>
                        <label for="holiday_block">Block Holidays</label>
                        <small>Disable bookings on registered holidays.</small>
                    </div>
                    <input class="form-check-input" type="checkbox" id="holiday_block" name="holiday_block" value="1" {{ old('holiday_block', true) ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="settings-card">
                <div class="settings-card-header">
                    <div>
                        <h5 class="settings-card-title">Appointments & Queue</h5>
                        <div class="settings-card-subtitle">Defaults for scheduling and queue flow.</div>
                    </div>
                    <div class="icon-pill"><i class="fas fa-calendar-check"></i></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="appointment_duration" class="form-label">Default Duration (min)</label>
                        <input type="number" class="form-control" id="appointment_duration" name="appointment_duration" value="{{ old('appointment_duration', 30) }}" min="10" step="5">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="appointment_buffer" class="form-label">Buffer Time (min)</label>
                        <input type="number" class="form-control" id="appointment_buffer" name="appointment_buffer" value="{{ old('appointment_buffer', 10) }}" min="0" step="5">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="max_daily_appointments" class="form-label">Max Daily Appointments</label>
                        <input type="number" class="form-control" id="max_daily_appointments" name="max_daily_appointments" value="{{ old('max_daily_appointments', 40) }}" min="1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="queue_autocall" class="form-label">Queue Auto-call (min)</label>
                        <input type="number" class="form-control" id="queue_autocall" name="queue_autocall" value="{{ old('queue_autocall', 5) }}" min="1">
                    </div>
                </div>
                <div class="setting-toggle">
                    <div>
                        <label for="allow_walkins">Allow Walk-ins</label>
                        <small>Enable walk-in visits without prior booking.</small>
                    </div>
                    <input class="form-check-input" type="checkbox" id="allow_walkins" name="allow_walkins" value="1" {{ old('allow_walkins', true) ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="settings-card">
                <div class="settings-card-header">
                    <div>
                        <h5 class="settings-card-title">Inventory & Billing</h5>
                        <div class="settings-card-subtitle">Stock warnings and billing defaults.</div>
                    </div>
                    <div class="icon-pill"><i class="fas fa-warehouse"></i></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                        <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="expiry_warning_days" class="form-label">Expiry Warning (days)</label>
                        <input type="number" class="form-control" id="expiry_warning_days" name="expiry_warning_days" value="{{ old('expiry_warning_days', 10) }}" min="1">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-select" id="currency" name="currency">
                            <option value="PHP" selected>PHP</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                        <input type="number" class="form-control" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', 12) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="invoice_prefix" class="form-label">Invoice Prefix</label>
                    <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" value="{{ old('invoice_prefix', 'INV') }}" placeholder="INV">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="settings-card">
                <div class="settings-card-header">
                    <div>
                        <h5 class="settings-card-title">Notifications</h5>
                        <div class="settings-card-subtitle">Alerts and communication channels.</div>
                    </div>
                    <div class="icon-pill"><i class="fas fa-bell"></i></div>
                </div>
                <div class="setting-toggle">
                    <div>
                        <label for="email_notifications">Email Notifications</label>
                        <small>Send appointment and billing alerts via email.</small>
                    </div>
                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" {{ old('email_notifications', true) ? 'checked' : '' }}>
                </div>
                <div class="setting-toggle">
                    <div>
                        <label for="sms_notifications">SMS Notifications</label>
                        <small>Send reminders through SMS.</small>
                    </div>
                    <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" value="1" {{ old('sms_notifications') ? 'checked' : '' }}>
                </div>
                <div class="setting-toggle">
                    <div>
                        <label for="inventory_alerts">Inventory Alerts</label>
                        <small>Notify admins when items are low or expiring.</small>
                    </div>
                    <input class="form-check-input" type="checkbox" id="inventory_alerts" name="inventory_alerts" value="1" {{ old('inventory_alerts', true) ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="settings-card">
                <div class="settings-card-header">
                    <div>
                        <h5 class="settings-card-title">Security & Access</h5>
                        <div class="settings-card-subtitle">Session and access controls.</div>
                    </div>
                    <div class="icon-pill"><i class="fas fa-shield-alt"></i></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="session_timeout" class="form-label">Session Timeout (min)</label>
                        <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="{{ old('session_timeout', 30) }}" min="5">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                        <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" value="{{ old('max_login_attempts', 5) }}" min="1">
                    </div>
                </div>
                <div class="setting-toggle">
                    <div>
                        <label for="two_factor">Require Two-Factor Auth</label>
                        <small>Force admins to enable 2FA on next login.</small>
                    </div>
                    <input class="form-check-input" type="checkbox" id="two_factor" name="two_factor" value="1" {{ old('two_factor') ? 'checked' : '' }}>
                </div>
            </div>
        </div>
    </div>

    <div class="save-bar">
        <p>Review changes before saving. Settings apply immediately after update.</p>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Settings
        </button>
    </div>
</form>
@endsection
