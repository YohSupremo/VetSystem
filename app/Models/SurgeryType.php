<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurgeryType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'estimated_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'estimated_duration_minutes' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function surgeries(): HasMany
    {
        return $this->hasMany(Surgery::class);
    }
}
