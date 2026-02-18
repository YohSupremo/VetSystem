<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cage extends Model
{
    protected $fillable = [
        'cage_code',
        'location',
        'size',
        'status',
        'qr_code_path',
        'notes',
    ];

    public function assignments()
    {
        return $this->hasMany(CageAssignment::class);
    }

    public function currentAssignment()
    {
        $now = now();

        return $this->hasOne(CageAssignment::class)
            ->where(function ($query) use ($now) {
                $query->whereNull('check_in_time')
                    ->whereDate('start_date', '<=', $now->toDateString())
                    ->orWhere('check_in_time', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('check_out_time')
                    ->whereDate('end_date', '>=', $now->toDateString())
                    ->orWhere('check_out_time', '>', $now);
            });
    }

    /**
     * Synchronize cage status based on active assignments
     * If cage has active assignments, status should be 'occupied'
     * If no active assignments, status should be 'available' (unless maintenance/out_of_service)
     */
    public function syncStatus()
    {
        // Skip syncing if cage is in maintenance or out of service
        if (in_array($this->status, ['maintenance', 'out_of_service'])) {
            return $this;
        }

        // Check for active assignments
        $now = now();
        $hasActiveAssignment = CageAssignment::where('cage_id', $this->id)
            ->where(function ($query) use ($now) {
                $query->whereNull('check_in_time')
                    ->whereDate('start_date', '<=', $now->toDateString())
                    ->orWhere('check_in_time', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('check_out_time')
                    ->whereDate('end_date', '>=', $now->toDateString())
                    ->orWhere('check_out_time', '>', $now);
            })
            ->exists();

        $newStatus = $hasActiveAssignment ? 'occupied' : 'available';
        
        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }

        return $this;
    }
}
