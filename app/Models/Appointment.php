<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    /**
     * The attributes that are mass assignable. Trial
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pet_id',
        'veterinarian_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'type',
        'reason',
        'notes',
        'queue_number',
        'check_in_time',
        'start_service_time',
        'end_service_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'check_in_time' => 'datetime',
        'start_service_time' => 'datetime',
        'end_service_time' => 'datetime',
    ];

    /**
     * Get the pet that owns the appointment.
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the veterinarian that owns the appointment.
     */
    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'veterinarian_id');
    }

    /**
     * Scope a query to only include scheduled appointments.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include in-progress appointments.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include appointments for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('appointment_date', $date);
    }

    /**
     * Scope a query to only include appointments for a specific veterinarian.
     */
    public function scopeForVeterinarian($query, $veterinarianId)
    {
        return $query->where('veterinarian_id', $veterinarianId);
    }

    /**
     * Check if the appointment is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'scheduled' && 
               Carbon::parse($this->appointment_date)->isFuture() &&
               Carbon::parse($this->start_time)->isFuture();
    }

    /**
     * Check if the appointment is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the appointment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get the duration of the appointment in minutes.
     */
    public function getDurationInMinutes(): int
    {
        return Carbon::parse($this->start_time)->diffInMinutes($this->end_time);
    }
}
