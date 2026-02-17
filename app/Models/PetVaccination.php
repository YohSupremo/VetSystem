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
        'vaccine_name',
        'vaccine_type',
        'manufacturer',
        'batch_number',
        'dose_number',
        'administered_date',
        'vaccination_date',
        'next_due_date',
        'veterinarian_id',
        'administered_by',
        'certificate_path',
        'notes',
        'reminder_sent',
        'status',
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

    public function vet()
    {
        return $this->belongsTo(User::class, 'veterinarian_id');
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
