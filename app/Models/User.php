<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'contact_number',
        'address',
        'is_active',
        'email_verified',
        'phone_verified',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function petOwner()
    {
        return $this->hasOne(PetOwner::class);
    }

    public function performedSurgeries()
    {
        return $this->hasMany(Surgery::class, 'surgeon_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'veterinarian_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'veterinarian_id');
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function scopeVeterinarians($query)
    {
        return $query->where('role', 'veterinarian');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isVeterinarian()
    {
        return $this->role === 'veterinarian';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPetOwner()
    {
        return $this->role === 'registered_user';
    }
}
