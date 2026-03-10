<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroomingService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_name',
        'description',
        'duration_minutes',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the grooming appointments for this service.
     */
    public function groomingAppointments(): HasMany
    {
        return $this->hasMany(GroomingAppointment::class, 'service_id');
    }
}
