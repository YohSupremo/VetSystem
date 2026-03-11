<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StaffSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'day_of_week',
        'shift',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get staff IDs who are scheduled for a specific day and shift
     */
    public static function getScheduledStaffIds($dayOfWeek = null, $shift = null)
    {
        $query = self::where('is_active', true);

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        if ($shift) {
            $query->where('shift', $shift);
        }

        return $query->pluck('user_id')->unique()->toArray();
    }

    /**
     * Get staff IDs scheduled for current day and time
     */
    public static function getCurrentScheduledStaffIds()
    {
        $now = Carbon::now();
        $dayOfWeek = $now->format('l'); // Monday, Tuesday, etc.
        $hour = $now->hour;

        // Determine shift based on current time
        // Morning: 9AM-5PM (9-17), Night: 5PM-12AM (17-24)
        $shift = ($hour >= 9 && $hour < 17) ? 'morning' : 'night';

        return self::getScheduledStaffIds($dayOfWeek, $shift);
    }

    /**
     * Check if a user is scheduled for a specific day and shift
     */
    public static function isUserScheduled($userId, $dayOfWeek, $shift)
    {
        return self::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->where('shift', $shift)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get shift times as string
     */
    public function getShiftTimesAttribute()
    {
        return ClinicSetting::shiftLabel($this->shift);
    }
}
