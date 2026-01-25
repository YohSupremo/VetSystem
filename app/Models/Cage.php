<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Cage extends Model
{
    protected $fillable = [
        'id',
        'cage_code',
        'location',
        'status'
    ];

    public function assignments(): HasMany {
        return $this->hasMany(CageAssignment::class);
    }

    public function currentAssignment(): HasOne {
        return $this->hasOne(CageAssignment::class)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now());
    }

    public function currentPet() {
        return $this->currentAssignment ? $this->currentAssignment->pet : null;
    }

    public function available(): bool{
        return $this->status === 'available' && is_null($this->currentAssignment);
    }
}
