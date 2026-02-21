@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Notification Settings</h2>

            <form action="{{ route('admin.notifications.settings-update') }}" method="POST">
                @csrf

                <!-- General Notifications -->
                <div class="form-section mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">General Settings</h3>
                    
                    <div class="space-y-4">
                        <div class="form-check">
                            <input type="checkbox" id="notifications_enabled" name="notifications_enabled" 
                                   class="form-checkbox" {{ $settings->notifications_enabled ? 'checked' : '' }}>
                            <label for="notifications_enabled" class="ml-3">
                                <span class="font-medium">Enable Notifications</span>
                                <p class="text-gray-600 text-sm">Turn on/off all notifications</p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notification Delivery Methods -->
                <div class="form-section mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Delivery Methods</h3>
                    
                    <div class="space-y-4">
                        <div class="form-check">
                            <input type="checkbox" id="in_app_enabled" name="in_app_enabled" 
                                   class="form-checkbox" {{ $settings->in_app_enabled ? 'checked' : '' }}>
                            <label for="in_app_enabled" class="ml-3">
                                <span class="font-medium">In-App Notifications</span>
                                <p class="text-gray-600 text-sm">Show notifications within the application</p>
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="email_enabled" name="email_enabled" 
                                   class="form-checkbox" {{ $settings->email_enabled ? 'checked' : '' }}>
                            <label for="email_enabled" class="ml-3">
                                <span class="font-medium">Email Notifications</span>
                                <p class="text-gray-600 text-sm">Receive email notifications</p>
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="sms_enabled" name="sms_enabled" 
                                   class="form-checkbox" {{ $settings->sms_enabled ? 'checked' : '' }}>
                            <label for="sms_enabled" class="ml-3">
                                <span class="font-medium">SMS Notifications</span>
                                <p class="text-gray-600 text-sm">Receive SMS notifications</p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notification Types (Admin Specific) -->
                <div class="form-section mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Notification Types</h3>
                    
                    <div class="space-y-4">
                        <div class="form-check">
                            <input type="checkbox" id="appointment_reminder_enabled" name="appointment_reminder_enabled" 
                                   class="form-checkbox" {{ $settings->appointment_reminder_enabled ? 'checked' : '' }}>
                            <label for="appointment_reminder_enabled" class="ml-3">
                                <span class="font-medium">Appointment Reminders</span>
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="payment_due_enabled" name="payment_due_enabled" 
                                   class="form-checkbox" {{ $settings->payment_due_enabled ? 'checked' : '' }}>
                            <label for="payment_due_enabled" class="ml-3">
                                <span class="font-medium">Payment Due Notifications</span>
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="low_stock_enabled" name="low_stock_enabled" 
                                   class="form-checkbox" {{ $settings->low_stock_enabled ? 'checked' : '' }}>
                            <label for="low_stock_enabled" class="ml-3">
                                <span class="font-medium">Low Stock Alerts</span>
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="incident_report_enabled" name="incident_report_enabled" 
                                   class="form-checkbox" {{ $settings->incident_report_enabled ? 'checked' : '' }}>
                            <label for="incident_report_enabled" class="ml-3">
                                <span class="font-medium">Incident Reports</span>
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="system_alert_enabled" name="system_alert_enabled" 
                                   class="form-checkbox" {{ $settings->system_alert_enabled ? 'checked' : '' }}>
                            <label for="system_alert_enabled" class="ml-3">
                                <span class="font-medium">System Alerts</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Quiet Hours -->
                <div class="form-section mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Quiet Hours</h3>
                    
                    <div class="space-y-4">
                        <div class="form-check">
                            <input type="checkbox" id="quiet_hours_enabled" name="quiet_hours_enabled" 
                                   class="form-checkbox" {{ $settings->quiet_hours_enabled ? 'checked' : '' }}>
                            <label for="quiet_hours_enabled" class="ml-3">
                                <span class="font-medium">Enable Quiet Hours</span>
                                <p class="text-gray-600 text-sm">Don't show notifications during specified hours</p>
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-4 ml-6" id="quiet-hours-section">
                            <div class="form-group">
                                <label class="form-label">Quiet Hours Start</label>
                                <input type="time" name="quiet_hours_start" class="form-control" 
                                       value="{{ $settings->quiet_hours_start ?? '22:00' }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Quiet Hours End</label>
                                <input type="time" name="quiet_hours_end" class="form-control" 
                                       value="{{ $settings->quiet_hours_end ?? '08:00' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center mt-8 border-t pt-6">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle quiet hours section visibility
const quietHoursCheckbox = document.getElementById('quiet_hours_enabled');
const quietHoursSection = document.getElementById('quiet-hours-section');

function toggleQuietHours() {
    quietHoursSection.style.display = quietHoursCheckbox.checked ? 'grid' : 'none';
}

quietHoursCheckbox?.addEventListener('change', toggleQuietHours);
toggleQuietHours(); // Initialize on page load
</script>
@endsection
