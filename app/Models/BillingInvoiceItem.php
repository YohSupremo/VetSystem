<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingInvoiceItem extends Model
{
    protected $table = 'billing_invoice_items';

    protected $fillable = [
        'invoice_id',
        'item_type',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'billable_type',
        'billable_id',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
    }

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }
}
