<?php

namespace Database\Seeders;

use App\Models\BillingInvoice;
use App\Models\BillingInvoiceItem;
use App\Models\BillingPayment;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $petOwnerUsers = User::where('role', 'pet_owner')->take(2)->get();

        foreach ($petOwnerUsers as $index => $user) {
            $owner = PetOwner::firstOrCreate(
                ['user_id' => $user->id],
                ['notes' => 'Seeded billing owner.']
            );

            $pet = Pet::firstOrCreate(
                ['owner_id' => $owner->id, 'name' => 'Pet ' . ($index + 1)],
                [
                    'registration_number' => '2026-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'species' => 'Dog',
                    'breed' => 'Mixed',
                    'gender' => 'unknown'
                ]
            );

            $invoice = new BillingInvoice([
                'pet_id' => $pet->id,
                'owner_id' => $owner->id,
                'invoice_prefix' => 'INV',
                'issue_date' => Carbon::now()->subDays(3)->toDateString(),
                'due_date' => Carbon::now()->addDays(7)->toDateString(),
                'status' => $index === 0 ? 'paid' : 'partial',
                'tax_rate' => 0,
                'discount_amount' => 20,
                'notes' => 'Seeded invoice for billing demo.',
            ]);

            // Generate invoice number and sequence
            $invoice->invoice_number = $invoice->generateInvoiceNumber();
            $invoice->save();

            $items = [
                ['type' => 'consultation', 'desc' => 'General checkup', 'qty' => 1, 'price' => 500],
                ['type' => 'product', 'desc' => 'Antibiotics', 'qty' => 2, 'price' => 150],
            ];

            foreach ($items as $item) {
                BillingInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['type'],
                    'description' => $item['desc'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                ]);
            }

            // Create payment for the invoice based on status
            if ($invoice->status === 'paid') {
                BillingPayment::create([
                    'invoice_id' => $invoice->id,
                    'payment_date' => Carbon::now()->subDay(),
                    'amount' => $invoice->total_amount,
                    'payment_method' => 'cash',
                    'reference_number' => 'SEED-' . $invoice->id,
                    'notes' => 'Seeded payment',
                    'received_by' => $admin?->id,
                ]);
            } elseif ($invoice->status === 'partial') {
                BillingPayment::create([
                    'invoice_id' => $invoice->id,
                    'payment_date' => Carbon::now()->subDay(),
                    'amount' => round($invoice->total_amount / 2, 2),
                    'payment_method' => 'cash',
                    'reference_number' => 'SEED-' . $invoice->id,
                    'notes' => 'Seeded partial payment',
                    'received_by' => $admin?->id,
                ]);
            }
        }
    }
}
