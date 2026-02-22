<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChronicCondition extends Model
{
    protected $fillable = [
        'pet_id',
        'medical_record_id',
        'condition_name',
        'diagnosed_date',
        'ongoing_treatment',
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

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
