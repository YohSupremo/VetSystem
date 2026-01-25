<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Pet extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'species',
        'breed',
        'birth_date',
        'gender',
        'color',
        'weight',
        'microchip_number',
        'photo_path',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'float',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PetOwner::class);
    }

 

    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return 'Unknown';
        }
        return $this->birth_date->age . ' years';
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return asset('images/default-pet.png');
    }

    public function surgeries(): HasMany
    {
        return $this->hasMany(Surgery::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }

       public function cageAssignments(): HasMany
    {
        return $this->hasMany(CageAssignment::class);
    }

    public function feedingSchedules(): HasMany {
        return $this->hasMany(FeedingSchedule::class);
    }

    public function medicationIntructions(): HasMany {
        return $this->hasMany(MedicationInstruction::class);
    }
    public function currentCage(): HasOneThrough {
        return $this->hasOneThrough(
            Cage::class,          // The final model
            CageAssignment::class, // The intermediate model
            'pet_id',             // FK on CageAssignment
            'id',                 // FK on Cage
            'id',                 // Local key on Pet
            'cage_id'             // Local key on CageAssignment
        )->whereDate('start_date', '<=', now())
         ->whereDate('end_date', '>=', now());
    }
}
