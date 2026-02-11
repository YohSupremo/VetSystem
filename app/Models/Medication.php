<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'strength',
        'form',
        'category',
        'manufacturer',
        'requires_prescription',
        'side_effects',
        'dosage_instructions',
        'contraindications',
        'storage_requirements',
        'is_active'
    ];

    protected $casts = [
        'requires_prescription' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
