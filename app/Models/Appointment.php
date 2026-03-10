<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'pet_id',
        'veterinarian_id',
        'appointment_date',
        'type',
        'status',
        'notes',
        'arrival_time',
        'queue_status',
        'queue_priority',
        'estimated_wait_time',
        'reminder_sent',
        'reminder_sent_at',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'arrival_time' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'reminder_sent' => 'boolean',
        'queue_priority' => 'integer',
        'estimated_wait_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get formatted created_at timestamp with proper timezone
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->timezone(config('app.timezone'))->format('M d, Y g:i A');
    }

    /**
     * Get formatted updated_at timestamp with proper timezone
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at->timezone(config('app.timezone'))->format('M d, Y g:i A');
    }

    /**
     * Get the pet that owns the appointment.
     */
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the veterinarian assigned to the appointment.
     */
    public function veterinarian()
    {
        return $this->belongsTo(User::class, 'veterinarian_id');
    }
}
