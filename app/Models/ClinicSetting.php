<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ClinicSetting extends Model
{
    protected $fillable = [
        'clinic_name',
        'clinic_phone',
        'clinic_email',
        'clinic_address',
        'timezone',
        'currency_code',
        'invoice_prefix',
        'default_tax_rate',
        'morning_shift_start',
        'morning_shift_end',
        'night_shift_start',
        'night_shift_end',
        'updated_by',
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:2',
    ];

    public static function defaults(): array
    {
        return [
            'clinic_name' => 'Veterinary Clinic',
            'clinic_phone' => null,
            'clinic_email' => null,
            'clinic_address' => null,
            'timezone' => 'Asia/Manila',
            'currency_code' => 'PHP',
            'invoice_prefix' => 'INV',
            'default_tax_rate' => 0,
            'morning_shift_start' => '09:00',
            'morning_shift_end' => '17:00',
            'night_shift_start' => '17:00',
            'night_shift_end' => '00:00',
            'updated_by' => null,
        ];
    }

    /**
     * Get formatted shift label for display.
     */
    public static function shiftLabel(string $shift): string
    {
        $settings = static::current();
        if ($shift === 'morning') {
            return static::formatTime($settings->morning_shift_start) . ' - ' . static::formatTime($settings->morning_shift_end);
        }
        return static::formatTime($settings->night_shift_start) . ' - ' . static::formatTime($settings->night_shift_end);
    }

    /**
     * Determine which shift a given hour falls into.
     */
    public static function shiftForHour(int $hour): string
    {
        $settings = static::current();
        $morningStart = (int) substr($settings->morning_shift_start, 0, 2);
        $morningEnd = (int) substr($settings->morning_shift_end, 0, 2);

        return ($hour >= $morningStart && $hour < $morningEnd) ? 'morning' : 'night';
    }

    protected static function formatTime(string $time): string
    {
        return \Carbon\Carbon::createFromFormat('H:i', substr($time, 0, 5))->format('g:i A');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): self
    {
        $defaults = static::defaults();

        if (!Schema::hasTable('clinic_settings')) {
            return new static($defaults);
        }

        $record = static::query()->first();

        return $record ?: new static($defaults);
    }

    public static function invoicePrefix(): string
    {
        $prefix = static::current()->invoice_prefix ?: static::defaults()['invoice_prefix'];

        return strtoupper((string) $prefix);
    }

    public static function defaultTaxRate(): float
    {
        $taxRate = static::current()->default_tax_rate;

        if ($taxRate === null) {
            $taxRate = static::defaults()['default_tax_rate'];
        }

        return (float) $taxRate;
    }
}
