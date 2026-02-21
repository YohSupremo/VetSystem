<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationDispensing extends Model
{
    protected $table = 'medication_dispensing';

    protected $fillable = [
        'prescription_id',
        'invoice_id',
        'inventory_item_id',
        'dispensed_by',
        'quantity_dispensed',
        'unit_price',
        'dispensed_at',
        'instructions',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'dispensed_at' => 'datetime',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function dispensedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
