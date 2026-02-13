<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cage extends Model
{
    protected $fillable = [
        'cage_code',
        'location',
        'size',
        'status',
        'qr_code_path',
        'notes',
    ];

    public function assignments()
    {
        return $this->hasMany(CageAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(CageAssignment::class)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }
}
