<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CageAssignment extends Model
{
    use SoftDeletes;

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

    public function startBoundary(): Carbon
    {
        if ($this->check_in_time) {
            return Carbon::parse($this->check_in_time);
        }

        return Carbon::parse($this->start_date)->startOfDay();
    }

    public function endBoundary(): Carbon
    {
        if ($this->check_out_time) {
            return Carbon::parse($this->check_out_time);
        }

        return Carbon::parse($this->end_date)->endOfDay();
    }

    public function isActive()
    {
        return !$this->isUpcoming() && !$this->isCompleted();
    }

    public function isUpcoming(): bool
    {
        $now = now();

        if ($this->check_in_time) {
            return $now->lt(Carbon::parse($this->check_in_time));
        }

        return $now->lt(Carbon::parse($this->start_date)->startOfDay());
    }

    public function isCompleted(): bool
    {
        $now = now();

        if ($this->check_out_time) {
            return $now->gte(Carbon::parse($this->check_out_time));
        }

        return $now->gt(Carbon::parse($this->end_date)->endOfDay());
    }

    /**
     * Scope: Get only active assignments (current or future)
     */
    public function scopeActive($query)
    {
        $now = now();

        return $query->where(function ($q) use ($now) {
                $q->where(function ($sub) use ($now) {
                    $sub->whereNull('check_in_time')
                        ->whereDate('start_date', '<=', $now->toDateString());
                })->orWhere('check_in_time', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->where(function ($sub) use ($now) {
                    $sub->whereNull('check_out_time')
                        ->whereDate('end_date', '>=', $now->toDateString());
                })->orWhere('check_out_time', '>', $now);
            });
    }

    /**
     * Scope: Get only expired assignments (past end_date)
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('end_date', '<', now());
    }
}
