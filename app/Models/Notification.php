<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'method',
        'status',
        'scheduled_for',
        'sent_at',
        'read_at',
        'reference_type',
        'reference_id',
        'error_message',
        'retry_count',
        'icon',
        'priority',
        'action_url',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    // Notification types (must match enum in migration)
    const TYPE_APPOINTMENT = 'appointment_reminder';
    const TYPE_VACCINATION = 'vaccination_due';
    const TYPE_PAYMENT = 'payment_due';
    const TYPE_PAYMENT_OVERDUE = 'payment_overdue';
    const TYPE_LAB_TEST = 'lab_result';
    const TYPE_PRESCRIPTION = 'prescription_refill';
    const TYPE_BOARDING = 'boarding_checkout';
    const TYPE_INVENTORY = 'low_stock';
    const TYPE_EXPIRY = 'item_expiry';

    // Notification methods (legacy - kept for compatibility)
    const METHOD_IN_APP = 'in_app';
    const METHOD_EMAIL = 'email';
    const METHOD_SMS = 'sms';

    // Notification status
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_READ = 'read';
    const STATUS_FAILED = 'failed';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('status', '!=', self::STATUS_READ);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByRole($query, $role)
    {
        return $query->whereHas('user', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HIGH)
            ->orWhere('priority', self::PRIORITY_URGENT);
    }

    public function scopeAdminOverview($query)
    {
        $adminVisibleFilter = static function ($builder) {
            $builder->where('type', '!=', self::TYPE_APPOINTMENT)
                ->orWhere(function ($appointmentBuilder) {
                    $appointmentBuilder->where('type', self::TYPE_APPOINTMENT)
                        ->where('title', 'Appointment Requested');
                });
        };

        $subquery = self::query()
            ->selectRaw('MAX(id) as id')
            ->where($adminVisibleFilter)
            ->groupBy('type', 'reference_type', 'reference_id', 'title', 'message');

        return $query->whereIn('id', $subquery)->where($adminVisibleFilter);
    }

    public function scopeVisibleToRole($query, ?string $role)
    {
        if (!$role) {
            return $query;
        }

        if ($role === 'groomer') {
            return $query->where(function ($groomerQuery) {
                $groomerQuery->where('title', 'New Grooming Appointment')
                    ->orWhere(function ($appointmentQuery) {
                        $appointmentQuery->where('type', self::TYPE_APPOINTMENT)
                            ->where('reference_type', 'appointment')
                            ->whereIn('reference_id', function ($appointments) {
                                $appointments->select('id')
                                    ->from('appointments')
                                    ->where('type', 'grooming');
                            });
                    });
            });
        }

        if ($role === 'admin') {
            return $query->where(function ($adminQuery) {
                $adminQuery->where('type', '!=', self::TYPE_APPOINTMENT)
                    ->orWhere(function ($appointmentQuery) {
                        $appointmentQuery->where('type', self::TYPE_APPOINTMENT)
                            ->where('title', 'Appointment Requested');
                    });
            });
        }

        $allowedTypes = self::allowedTypesForRole($role);
        if (empty($allowedTypes)) {
            return $query->whereRaw('1 = 0');
        }

        $appointmentTypes = self::appointmentTypesForRole($role);
        $nonAppointmentTypes = array_values(array_diff($allowedTypes, [self::TYPE_APPOINTMENT]));

        return $query->where(function ($sub) use ($nonAppointmentTypes, $appointmentTypes, $allowedTypes) {
            if (!empty($nonAppointmentTypes)) {
                $sub->whereIn('type', $nonAppointmentTypes);
            }

            if (in_array(self::TYPE_APPOINTMENT, $allowedTypes, true)) {
                $sub->orWhere(function ($apptQuery) use ($appointmentTypes) {
                    $apptQuery->where('type', self::TYPE_APPOINTMENT);

                    if (is_array($appointmentTypes)) {
                        if (count($appointmentTypes) === 0) {
                            $apptQuery->whereRaw('1 = 0');
                            return;
                        }

                        $apptQuery->where('reference_type', 'appointment')
                            ->whereIn('reference_id', function ($appointments) use ($appointmentTypes) {
                                $appointments->select('id')
                                    ->from('appointments')
                                    ->whereIn('type', $appointmentTypes);
                            });
                    }
                });
            }
        });
    }

    public static function allowedTypesForRole(string $role): array
    {
        return match ($role) {
            'veterinarian' => [self::TYPE_APPOINTMENT, self::TYPE_VACCINATION, self::TYPE_PRESCRIPTION, self::TYPE_LAB_TEST],
            'reception' => [self::TYPE_APPOINTMENT, self::TYPE_PAYMENT, self::TYPE_PAYMENT_OVERDUE],
            'boarding' => [self::TYPE_APPOINTMENT, self::TYPE_BOARDING],
            'groomer' => [self::TYPE_APPOINTMENT],
            'staff' => [self::TYPE_APPOINTMENT, self::TYPE_BOARDING],
            'pharmacy' => [self::TYPE_PRESCRIPTION, self::TYPE_INVENTORY, self::TYPE_EXPIRY],
            'pet_owner', 'registered_user' => [
                self::TYPE_APPOINTMENT,
                self::TYPE_VACCINATION,
                self::TYPE_PAYMENT,
                self::TYPE_PAYMENT_OVERDUE,
                self::TYPE_LAB_TEST,
                self::TYPE_PRESCRIPTION,
                self::TYPE_BOARDING,
            ],
            default => [
                self::TYPE_VACCINATION,
                self::TYPE_PAYMENT,
                self::TYPE_PAYMENT_OVERDUE,
                self::TYPE_LAB_TEST,
                self::TYPE_PRESCRIPTION,
                self::TYPE_BOARDING,
                self::TYPE_INVENTORY,
                self::TYPE_EXPIRY,
            ],
        };
    }

    public static function appointmentTypesForRole(string $role): ?array
    {
        return match ($role) {
            'veterinarian' => ['consultation', 'vaccination', 'surgery', 'follow_up', 'emergency', 'other'],
            'reception' => null,
            'boarding' => ['boarding'],
            'groomer' => ['grooming'],
            'staff' => ['boarding', 'grooming'],
            'pharmacy' => [],
            default => null,
        };
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function markAsRead()
    {
        $this->update([
            'read_at' => now(),
            'status' => self::STATUS_READ,
        ]);

        return $this;
    }

    public function markAsUnread()
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'read_at' => null,
        ]);

        return $this;
    }

    public function delete()
    {
        return parent::delete();
    }

    public function getIsReadAttribute(): bool
    {
        return $this->status === self::STATUS_READ;
    }
}
