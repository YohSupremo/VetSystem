<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

    /**
     * Write a QR scan log without breaking user flow when schema is missing.
     */
    public static function safeLog(array $attributes): ?self
    {
        if (!Schema::hasTable('qr_scan_logs')) {
            Log::warning('Skipping QR scan log: qr_scan_logs table is missing.', [
                'scan_type' => $attributes['scan_type'] ?? null,
                'cage_id' => $attributes['cage_id'] ?? null,
                'pet_id' => $attributes['pet_id'] ?? null,
                'scanned_by' => $attributes['scanned_by'] ?? null,
            ]);

            return null;
        }

        try {
            return self::create($attributes);
        } catch (QueryException $exception) {
            Log::warning('Skipping QR scan log due to database error.', [
                'error' => $exception->getMessage(),
                'scan_type' => $attributes['scan_type'] ?? null,
                'cage_id' => $attributes['cage_id'] ?? null,
                'pet_id' => $attributes['pet_id'] ?? null,
                'scanned_by' => $attributes['scanned_by'] ?? null,
            ]);

            return null;
        }
    }

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
