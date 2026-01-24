<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class TestType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'description',
        'reference_ranges',
        'price',
        'turnaround_time',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reference_ranges' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the test requests for the test type.
     */
    public function testRequests()
    {
        return $this->hasMany(TestRequest::class);
    }

    /**
     * Scope a query to only include active test types.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get the categories for the test types.
     */
    public static function getCategories()
    {
        return self::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }
}
