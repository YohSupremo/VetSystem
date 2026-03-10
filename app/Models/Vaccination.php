<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vaccination extends Model
{
    use SoftDeletes;

    protected $table = 'pet_vaccinations';
    
    public $timestamps = false;

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
        'expiry_date',
        'administered_by',
        'veterinarian_id',
        'certificate_path',
        'notes',
        'reminder_sent',
        'status',
    ];

    protected $casts = [
        'administered_date' => 'date',
        'next_due_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'veterinarian_id');
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
