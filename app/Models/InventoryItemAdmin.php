<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItemAdmin extends Model
{
    protected $table = 'inventory_items';

    protected $fillable = [
        'name',
        'category',
        'sku',
        'unit_price',
        'image_path',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];
}
