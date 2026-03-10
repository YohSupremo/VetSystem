<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'order_id',
        'appointment_id',
        'pet_id',
        'owner_id',
        'invoice_prefix',
        'invoice_sequence',
        'invoice_number',
        'issue_date',
        'due_date',
        'tax_rate',
        'discount_amount',
        'discount_reason',
        'status',
        'notes',
        'overdue_reminder_sent',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'tax_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function owner()
    {
        return $this->belongsTo(PetOwner::class, 'owner_id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function petOwner()
    {
        return $this->belongsTo(PetOwner::class, 'owner_id');
    }

    public function getInvoiceDateAttribute()
    {
        return $this->issue_date;
    }

    public function getSubtotalAttribute()
    {
        $subtotal = $this->invoiceItems()->get()->sum(function ($item) {
            return $item->quantity * (float) $item->unit_price;
        });

        return round((float) $subtotal, 2);
    }

    public function getTaxAmountAttribute()
    {
        return round((float) $this->subtotal * ((float) $this->tax_rate / 100), 2);
    }

    public function getTaxAttribute()
    {
        return $this->tax_amount;
    }

    public function getTotalAmountAttribute()
    {
        return round((float) $this->subtotal + (float) $this->tax_amount - (float) $this->discount_amount, 2);
    }

    public function getPaidAmountAttribute()
    {
        return round((float) $this->payments->sum('amount'), 2);
    }

    public function getBalanceAttribute()
    {
        return round(max(0, (float) $this->total_amount - (float) $this->paid_amount), 2);
    }

    public function getIsPaidAttribute()
    {
        return $this->status === 'paid' || $this->balance <= 0;
    }

    public function generateInvoiceNumber()
    {
        $prefix = $this->invoice_prefix ?: 'INV';
        $year = ($this->issue_date ?? now())->format('Y');
        $lastSequence = self::where('invoice_prefix', $prefix)
            ->whereYear('issue_date', $year)
            ->max('invoice_sequence');

        $nextSequence = $lastSequence ? $lastSequence + 1 : 1;
        $this->invoice_sequence = $nextSequence;

        return sprintf('%s-%s-%06d', $prefix, $year, $nextSequence);
    }
}
