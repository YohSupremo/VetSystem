<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'stock_id',
        'type',
        'quantity',
        'reference',
        'performed_by',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function inventoryStock()
    {
        return $this->belongsTo(InventoryStock::class, 'stock_id');
    }

    public function stock()
    {
        return $this->belongsTo(InventoryStock::class, 'stock_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
