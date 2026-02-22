<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'schedule_date',
        'shift_start',
        'shift_end',
        'break_duration_minutes',
        'notes',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'shift_start' => 'datetime',
        'shift_end' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
