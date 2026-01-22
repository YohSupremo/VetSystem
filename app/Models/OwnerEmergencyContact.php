<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerEmergencyContact extends Model
{
    protected $table = 'owner_emergency_contacts';

    protected $fillable = [
        'owner_id',
        'contact_name',
        'contact_number',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PetOwner::class, 'owner_id');
    }
}
