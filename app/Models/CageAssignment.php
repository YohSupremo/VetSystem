<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class CageAssignment extends Model
{
    protected $table = 'cage_assignments';

    protected $fillable = [
        'cage_id',
        'pet_id',
        'start_date',
        'end_date',
        'medication_notes',
    ];

    public function petAssigned(): BelongsTo {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function cageAssigned(): BelongsTo {
        return $this->belongsTo(Cage::class, 'cage_id');
    }

    // Alias relationships for easier access in views
    public function pet(): BelongsTo {
        return $this->petAssigned();
    }

    public function cage(): BelongsTo {
        return $this->cageAssigned();
    }

    public function feedingSchedule(): HasOne {
        return $this->hasOne(FeedingSchedule::class, 'pet_id', 'pet_id');
    }

    public function medicationInstruction(): HasOne {
        return $this->hasOne(MedicationInstruction::class, 'pet_id', 'pet_id');
    }

    // Accessor for status (calculated dynamically)
    public function getStatusAttribute(): string
    {
        if ($this->isActive()) {
            return 'active';
        }
        return now()->toDateString() > $this->end_date ? 'completed' : 'upcoming';
    }

      public function isActive(): bool
    {
        $today = now()->toDateString();
        return $today >= $this->start_date && $today <= $this->end_date;
    }
}
