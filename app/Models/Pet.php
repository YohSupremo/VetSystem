<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
