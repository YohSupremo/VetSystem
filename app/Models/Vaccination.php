<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccination extends Model
{
    protected $table = 'pet_vaccinations';
    
    public $timestamps = false;

    protected $fillable = [
        'pet_id',
        'vaccine_id',
        'batch_number',
        'dose_number',
        'administered_date',
        'next_due_date',
        'expiry_date',
        'administered_by',
        'certificate_path',
        'notes',
        'reminder_sent',
    ];

    protected $casts = [
        'administered_date' => 'date',
        'next_due_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
