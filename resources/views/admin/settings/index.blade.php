@extends('admin.dashboard')

@section('page-title', 'Settings')
@section('page-description', 'Manage clinic-wide system settings')

@section('content')
<div class="content-header mb-4">
    <div class="header-title">
        <h1><i class="fas fa-cog"></i> Clinic Settings</h1>
        <p>Configure global clinic information and operational defaults</p>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.settings.store') }}" method="POST">
            @csrf

            <h5 class="mb-3">Clinic Profile</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Clinic Name</label>
                    <input
                        type="text"
                        name="clinic_name"
                        class="form-control"
                        value="{{ old('clinic_name', $clinicSettings->clinic_name) }}"
                        maxlength="150"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Clinic Email</label>
                    <input
                        type="email"
                        name="clinic_email"
                        class="form-control"
                        value="{{ old('clinic_email', $clinicSettings->clinic_email) }}"
                        maxlength="150"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Clinic Phone</label>
                    <input
                        type="text"
                        name="clinic_phone"
                        class="form-control"
                        value="{{ old('clinic_phone', $clinicSettings->clinic_phone) }}"
                        maxlength="30"
                    >
                </div>

                <div class="col-md-12">
                    <label class="form-label">Clinic Address</label>
                    <textarea
                        name="clinic_address"
                        class="form-control"
                        rows="3"
                    >{{ old('clinic_address', $clinicSettings->clinic_address) }}</textarea>
                </div>
            </div>

            <h5 class="mb-3">Regional & Billing Defaults</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Timezone</label>
                    <input
                        type="text"
                        name="timezone"
                        class="form-control"
                        value="{{ old('timezone', $clinicSettings->timezone) }}"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Currency Code</label>
                    <input
                        type="text"
                        name="currency_code"
                        class="form-control"
                        value="{{ old('currency_code', $clinicSettings->currency_code) }}"
                        maxlength="3"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Invoice Prefix</label>
                    <input
                        type="text"
                        name="invoice_prefix"
                        class="form-control"
                        value="{{ old('invoice_prefix', $clinicSettings->invoice_prefix) }}"
                        maxlength="10"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Default Tax Rate (%)</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        name="default_tax_rate"
                        class="form-control"
                        value="{{ old('default_tax_rate', $clinicSettings->default_tax_rate) }}"
                        required
                    >
                </div>
            </div>

            <h5 class="mb-3">Operational Defaults</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Appointment Slot (minutes)</label>
                    <input
                        type="number"
                        min="1"
                        name="appointment_slot_minutes"
                        class="form-control"
                        value="{{ old('appointment_slot_minutes', $clinicSettings->appointment_slot_minutes) }}"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Appointment Buffer (minutes)</label>
                    <input
                        type="number"
                        min="0"
                        name="appointment_buffer_minutes"
                        class="form-control"
                        value="{{ old('appointment_buffer_minutes', $clinicSettings->appointment_buffer_minutes) }}"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Low Stock Threshold</label>
                    <input
                        type="number"
                        min="0"
                        name="low_stock_threshold"
                        class="form-control"
                        value="{{ old('low_stock_threshold', $clinicSettings->low_stock_threshold) }}"
                        required
                    >
                </div>
            </div>

            <div class="d-flex justify-content-between border-top pt-3 mt-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Clinic Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
