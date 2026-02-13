<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    protected $fillable = [
        'invoice_id',
        'insurance_provider',
        'policy_number',
        'claim_number',
        'claim_amount',
        'claim_status',
        'submitted_date',
        'processed_date',
        'notes',
    ];

    protected $casts = [
        'claim_amount' => 'decimal:2',
        'submitted_date' => 'date',
        'processed_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
