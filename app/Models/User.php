<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'contact_number',
        'email',
        'username',
        'password'
    ];

    public function petOwner(): HasOne
    {
        return $this->hasOne(PetOwner::class);
    }
}

