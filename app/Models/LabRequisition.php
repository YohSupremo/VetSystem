<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabRequisition extends Model
{
    protected $fillable = [
        'medical_record_id',
        'test_id',
        'invoice_id',
        'requested_by',
        'requested_date',
        'sample_collected',
        'sample_collection_date',
        'status',
        'results',
        'result_date',
        'notes',
        'result_notification_sent',
    ];

    protected $casts = [
        'requested_date' => 'datetime',
        'sample_collection_date' => 'datetime',
        'result_date' => 'datetime',
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

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
