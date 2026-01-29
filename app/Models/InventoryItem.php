<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'category',
        'sku',
        'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];
}
