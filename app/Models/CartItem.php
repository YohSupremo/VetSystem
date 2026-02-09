<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'inventory_item_id',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShoppingCart::class, 'cart_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cartItem) {
            $cartItem->total = $cartItem->quantity * $cartItem->unit_price;
        });

        static::saved(function ($cartItem) {
            $cartItem->cart->touch();
        });

        static::deleted(function ($cartItem) {
            $cartItem->cart->touch();
        });
    }

    public function canUpdateQuantity(int $newQuantity): bool
    {
        return $newQuantity > 0 && $newQuantity <= $this->inventoryItem->quantity;
    }

    public function isAvailable(): bool
    {
        return $this->inventoryItem && 
               $this->inventoryItem->quantity > 0 && 
               !$this->inventoryItem->requires_prescription;
    }
}
