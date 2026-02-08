<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingInvoice extends Model
{
    protected $table = 'billing_invoices';

    protected $fillable = [
        'invoice_number',
        'appointment_id',
        'pet_id',
        'pet_owner_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function petOwner(): BelongsTo
    {
        return $this->belongsTo(PetOwner::class, 'pet_owner_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(BillingInvoiceItem::class, 'invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BillingPayment::class, 'invoice_id');
    }

    public function getBalanceAttribute(): float
    {
        $paid = $this->paid_amount ?? 0;
        $total = $this->total_amount ?? 0;
        return max(0, (float) $total - (float) $paid);
    }

    public function getIsPaidAttribute(): bool
    {
        return ($this->status === 'paid') || $this->balance <= 0;
    }

    public function generateInvoiceNumber(): string
    {
        $datePart = now()->format('Ymd');
        $suffix = str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
        return 'INV-' . $datePart . '-' . $suffix;
    }
}
