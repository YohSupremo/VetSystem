<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'contact_number',
        'email',
        'username',
        'password',
        'role',
        'license_number',
        'specialization',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
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

    // Veterinarian specific relationships
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'veterinarian_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'veterinarian_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'veterinarian_id');
    }

    public function assignedPets(): BelongsToMany
    {
        return $this->belongsToMany(Pet::class, 'pet_veterinarian_assignments')
            ->withTimestamps();
    }

    // Helper methods
    public function isVeterinarian(): bool
    {
        return $this->role === 'veterinarian';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'pet_owner';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isPharmacist(): bool
    {
        return $this->role === 'pharmacist';
    }

    public function isGroomer(): bool
    {
        return $this->role === 'groomer';
    }

    public function isBoarding(): bool
    {
        return $this->role === 'boarding';
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // Scopes
    public function scopeVeterinarians($query)
    {
        return $query->where('role', 'veterinarian');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}

