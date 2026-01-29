<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroomingAppointment extends Model
{
    protected $fillable = [
        'appointment_id',
        'service_id',
        'groomer_id',
        'special_instructions',
        'status',
    ];

    /**
     * Get the appointment that owns this grooming appointment.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the grooming service for this appointment.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(GroomingService::class, 'service_id');
    }

    /**
     * Get the groomer assigned to this appointment.
     */
    public function groomer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'groomer_id');
    }
}
