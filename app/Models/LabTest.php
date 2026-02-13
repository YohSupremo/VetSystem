<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabTest extends Model
{
    protected $fillable = [
        'test_name',
        'category',
        'description',
        'standard_price',
        'is_active',
    ];

    protected $casts = [
        'standard_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the lab requisitions for this test.
     */
    public function labRequisitions(): HasMany
    {
        return $this->hasMany(LabRequisition::class, 'test_id');
    }
}
