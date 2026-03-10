<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'medical_record_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration_days',
        'quantity',
        'instructions',
        'dispensed',
        'dispensed_at',
        'dispensed_by',
        'refill_reminder_sent',
    ];

    protected $casts = [
        'dispensed' => 'boolean',
        'dispensed_at' => 'datetime',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function getPetAttribute()
    {
        return $this->medicalRecord?->pet;
    }

    public function getPrescribedByAttribute()
    {
        return $this->medicalRecord?->veterinarian;
    }
}
