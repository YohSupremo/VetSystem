<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabRequisition extends Model
{
    protected $fillable = [
        'medical_record_id',
        'test_id',
        'requested_by',
        'requested_date',
        'sample_collected',
        'sample_collection_date',
        'status',
        'results',
        'notes',
    ];

    protected $casts = [
        'requested_date' => 'datetime',
        'sample_collection_date' => 'datetime',
        'sample_collected' => 'boolean',
    ];

    /**
     * Get the medical record that owns this requisition.
     */
    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    /**
     * Get the lab test for this requisition.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(LabTest::class, 'test_id');
    }

    /**
     * Get the user who requested this test.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
