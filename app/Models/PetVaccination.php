<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetVaccination extends Model
{
    public $timestamps = false;
    protected $table = 'pet_vaccinations';

    protected $fillable = [
        'pet_id',
        'inventory_item_id',
        'batch_number',
        'dose_number',
        'administered_date',
        'next_due_date',
        'administered_by',
        'certificate_path',
        'notes',
        'reminder_sent',
    ];

    protected $casts = [
        'administered_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
