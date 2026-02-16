<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CageAssignment extends Model
{
    protected $table = 'cage_assignments';

    protected $fillable = [
        'cage_id',
        'pet_id',
        'start_date',
        'end_date',
        'check_in_time',
        'check_out_time',
        'feeding_schedule',
        'feeding_times',
        'special_diet_notes',
        'medication_instructions',
        'medication_times',
        'notes',
        'daily_rate',
        'checkout_reminder_sent',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function cage()
    {
        return $this->belongsTo(Cage::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function isActive()
    {
        $today = now()->toDateString();
        return $today >= $this->start_date && $today <= $this->end_date;
    }

    /**
     * Scope: Get only active assignments (current or future)
     */
    public function scopeActive($query)
    {
        return $query->whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now());
    }

    /**
     * Scope: Get only expired assignments (past end_date)
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('end_date', '<', now());
    }
}
