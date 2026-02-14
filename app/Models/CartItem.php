<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'inventory_item_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'cart_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    /**
     * Check if the item is available for purchase
     */
    public function isAvailable()
    {
        return $this->inventoryItem && 
               $this->inventoryItem->is_active && 
               $this->inventoryItem->quantity > 0;
    }

    /**
     * Check if the requested quantity can be updated
     */
    public function canUpdateQuantity($quantity)
    {
        if ($quantity <= 0) {
            return false;
        }

        if (!$this->isAvailable()) {
            return false;
        }

        return $quantity <= $this->inventoryItem->quantity;
    }

    /**
     * Get the total price for this item
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}
