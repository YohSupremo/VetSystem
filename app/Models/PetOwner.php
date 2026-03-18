<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Pet;

class PetOwner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'notes',
        'preferred_contact_method',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // always eager load user for convenience
    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'owner_id');
    }


    public function getFullNameAttribute()
    {
        return $this->user ? trim($this->user->first_name . ' ' . $this->user->last_name) : '';
    }

    public function getNameAttribute()
    {
        return $this->full_name;
    }

    public function getFirstNameAttribute()
    {
        return $this->user?->first_name;
    }

    public function getLastNameAttribute()
    {
        return $this->user?->last_name;
    }
}
