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
        'appointment_reminder_enabled',
        'vaccination_due_enabled',
        'payment_due_enabled',
        'payment_overdue_enabled',
        'lab_result_enabled',
        'prescription_refill_enabled',
        'boarding_checkout_enabled',
        'low_stock_enabled',
        'item_expiry_enabled',
        'default_advance_hours',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
