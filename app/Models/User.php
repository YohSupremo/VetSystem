<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'contact_number',
        'email',
        'username',
        'password',
        'role'
    ];

    public function petOwner(): HasOne
    {
        return $this->hasOne(PetOwner::class);
    }

    public function performedSurgeries(): HasMany
    {
        return $this->hasMany(Surgery::class, 'surgeon_id');
    }

    public function administeredVaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class, 'veterinarian_id');
    }
}

