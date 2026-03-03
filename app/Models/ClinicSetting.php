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
        'appointment_slot_minutes',
        'appointment_buffer_minutes',
        'updated_by',
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:2',
        'appointment_slot_minutes' => 'integer',
        'appointment_buffer_minutes' => 'integer',
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
            'appointment_slot_minutes' => 30,
            'appointment_buffer_minutes' => 10,
            'updated_by' => null,
        ];
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
