<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'sku',
        'manufacturer',
        'batch_number',
        'dosage_form',
        'strength',
        'unit_price',
        'requires_prescription',
        'controlled_substance',
        'storage_instructions',
        'supplier_id',
        'quantity',
        'min_stock',
        'expiry_date',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'requires_prescription' => 'boolean',
        'controlled_substance' => 'boolean',
        'quantity' => 'integer',
        'min_stock' => 'integer',
        'expiry_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function medicationDispensing(): HasMany
    {
        return $this->hasMany(MedicationDispensing::class, 'inventory_item_id');
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_stock;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 10): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= $days;
    }

    public function requiresExpiryDate(): bool
    {
        return in_array($this->category, ['medicine', 'vaccine', 'food']);
    }
}
