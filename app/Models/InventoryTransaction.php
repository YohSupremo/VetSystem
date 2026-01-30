<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'stock_id',
        'type',
        'quantity',
        'reference',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function inventoryStock(): BelongsTo
    {
        return $this->belongsTo(InventoryStock::class, 'stock_id');
    }
}
