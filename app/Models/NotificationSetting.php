<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'notifications_enabled',
        'email_enabled',
        'sms_enabled',
        'in_app_enabled',
        'appointment_reminder_enabled',
        'appointment_reminder_hours',
        'vaccination_due_enabled',
        'vaccination_due_days',
        'payment_due_enabled',
        'payment_overdue_enabled',
        'lab_result_enabled',
        'prescription_refill_enabled',
        'boarding_checkout_enabled',
        'low_stock_enabled',
        'item_expiry_enabled',
        'incident_report_enabled',
        'surgery_status_enabled',
        'grooming_status_enabled',
        'medication_dispensing_enabled',
        'inventory_alert_enabled',
        'default_advance_hours',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'notification_frequency',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'appointment_reminder_enabled' => 'boolean',
        'vaccination_due_enabled' => 'boolean',
        'payment_due_enabled' => 'boolean',
        'payment_overdue_enabled' => 'boolean',
        'lab_result_enabled' => 'boolean',
        'prescription_refill_enabled' => 'boolean',
        'boarding_checkout_enabled' => 'boolean',
        'low_stock_enabled' => 'boolean',
        'item_expiry_enabled' => 'boolean',
        'incident_report_enabled' => 'boolean',
        'surgery_status_enabled' => 'boolean',
        'grooming_status_enabled' => 'boolean',
        'medication_dispensing_enabled' => 'boolean',
        'inventory_alert_enabled' => 'boolean',
        'quiet_hours_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultSettings($userId)
    {
        return [
            'user_id' => $userId,
            'notifications_enabled' => true,
            'email_enabled' => false,
            'sms_enabled' => false,
            'in_app_enabled' => true,
            'appointment_reminder_enabled' => true,
            'appointment_reminder_hours' => 24,
            'vaccination_due_enabled' => true,
            'vaccination_due_days' => 7,
            'payment_due_enabled' => true,
            'payment_overdue_enabled' => true,
            'lab_result_enabled' => true,
            'prescription_refill_enabled' => true,
            'boarding_checkout_enabled' => true,
            'low_stock_enabled' => true,
            'item_expiry_enabled' => true,
            'incident_report_enabled' => true,
            'surgery_status_enabled' => true,
            'grooming_status_enabled' => true,
            'medication_dispensing_enabled' => true,
            'inventory_alert_enabled' => true,
            'default_advance_hours' => 24,
            'quiet_hours_enabled' => false,
            'quiet_hours_start' => '22:00',
            'quiet_hours_end' => '08:00',
            'notification_frequency' => 'immediate',
        ];
    }

    public function isNotificationTypeEnabled($type)
    {
        $settingKey = $type . '_enabled';
        return isset($this->{$settingKey}) ? $this->{$settingKey} : true;
    }

    public function shouldNotify($method = 'in_app')
    {
        if (!$this->notifications_enabled) {
            return false;
        }

        $methodKey = $method . '_enabled';
        return $this->{$methodKey} ?? false;
    }

    public function isInQuietHours()
    {
        if (!$this->quiet_hours_enabled) {
            return false;
        }

        $now = now();
        $start = $now->setTimeFromTimeString($this->quiet_hours_start);
        $end = $now->setTimeFromTimeString($this->quiet_hours_end);

        if ($start > $end) {
            // Quiet hours span midnight
            return $now->isAfter($start) || $now->isBefore($end);
        }

        return $now->isBetween($start, $end);
    }
}
