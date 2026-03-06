<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrScanLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'scan_type',
        'cage_id',
        'pet_id',
        'scanned_by',
        'scan_timestamp',
        'location',
        'notes',
    ];

    protected $casts = [
        'scan_timestamp' => 'datetime',
    ];

    public function cage()
    {
        return $this->belongsTo(Cage::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
