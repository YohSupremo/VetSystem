<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PetOwner extends Model
{
    protected $fillable = [
        'user_id',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(OwnerEmergencyContact::class, 'owner_id');
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }

    public function getFullNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }
}
