<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetVaccination extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'pet_id',
        'vaccine_id',
        'batch_number',
        'dose_number',
        'administered_date',
        'next_due_date',
        'administered_by',
        'certificate_path',
        'notes',
        'reminder_sent',
    ];

    protected $casts = [
        'administered_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
