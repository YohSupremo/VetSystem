<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    protected $table = 'inventory_stock';

    protected $fillable = [
        'item_id',
        'quantity',
        'min_stock',
        'max_stock',
        'expiry_date',
        'location',
        'low_stock_alert_sent',
        'expiry_alert_sent',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'stock_id');
    }
}
