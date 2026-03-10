<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'sku',
        'description',
        'unit_price',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class, 'item_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'inventory_item_id');
    }

    public function isLowStock()
    {
        $total = $this->inventoryStocks->sum('quantity');
        $min = $this->inventoryStocks->max('min_stock') ?? 10;
        return $total <= $min;
    }

    public function isExpired()
    {
        return $this->inventoryStocks->whereNotNull('expiry_date')
            ->filter(fn ($s) => $s->expiry_date && $s->expiry_date->isPast())->isNotEmpty();
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->inventoryStocks->whereNotNull('expiry_date')
            ->filter(fn ($s) => $s->expiry_date && $s->expiry_date->isFuture() && $s->expiry_date->lte(now()->addDays($days)))->isNotEmpty();
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'inventory_item_id');
    }

    public function hasAlerts()
    {
        return $this->alerts()->exists();
    }

    public function hasExpiredAlerts()
    {
        return $this->alerts()->where('type', 'expired')->exists();
    }

    public function hasLowStockAlerts()
    {
        return $this->alerts()->where('type', 'low_stock')->exists();
    }

    public function hasExpiringSoonAlerts()
    {
        return $this->alerts()->where('type', 'expiring_soon')->exists();
    }

    /**
     * Get total stock quantity across all inventory stock records
     */
    public function getTotalStockAttribute()
    {
        return $this->inventoryStocks->sum('quantity');
    }

    /**
     * Get quantity attribute as alias for total stock
     * This fixes the issue where views access $product->quantity
     */
    public function getQuantityAttribute()
    {
        return $this->inventoryStocks->sum('quantity');
    }

    /**
     * Decrement stock from available inventory batches
     * Uses FIFO (First In First Out) approach based on expiry date or created_at
     */
    public function decrementStock($amount)
    {
        if ($amount <= 0) {
            return;
        }

        $remainingToDeduct = $amount;
        
        // Get stocks ordered by expiry (nulls last) then created_at
        // This ensures we use oldest stock first
        $stocks = $this->inventoryStocks()
            ->where('quantity', '>', 0)
            ->orderByRaw('ISNULL(expiry_date), expiry_date ASC')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($stocks as $stock) {
            if ($remainingToDeduct <= 0) break;

            if ($stock->quantity >= $remainingToDeduct) {
                $stock->decrement('quantity', $remainingToDeduct);
                $remainingToDeduct = 0;
            } else {
                $deducted = $stock->quantity;
                $stock->update(['quantity' => 0]);
                $remainingToDeduct -= $deducted;
            }
        }
    }
}
