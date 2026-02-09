<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\InventoryItem;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_type',
        'reference_id',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'reference_id');
    }
}

