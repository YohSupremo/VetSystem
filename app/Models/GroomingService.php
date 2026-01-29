<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroomingService extends Model
{
    protected $fillable = [
        'service_name',
        'description',
        'duration_minutes',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    /**
     * Get the grooming appointments for this service.
     */
    public function groomingAppointments(): HasMany
    {
        return $this->hasMany(GroomingAppointment::class, 'service_id');
    }
}
