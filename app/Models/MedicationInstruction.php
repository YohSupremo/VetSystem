<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationInstruction extends Model
{
    protected $fillable =  [
        'pet_id',
        'instructions'
    ];

    public function pet(): BelongsTo {
        return $this->belongsTo(Pet::class);
    }
}
