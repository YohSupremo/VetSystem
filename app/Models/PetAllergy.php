<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetAllergy extends Model
{
    protected $fillable = [
        'pet_id',
        'allergen',
        'reaction_type',
        'severity',
        'diagnosed_date',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'diagnosed_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
