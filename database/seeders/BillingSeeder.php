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
                ['species' => 'Dog', 'breed' => 'Mixed', 'gender' => 'unknown']
            );

            $invoice = BillingInvoice::create([
                'invoice_number' => '',
                'pet_id' => $pet->id,
                'pet_owner_id' => $owner->id,
                'invoice_date' => Carbon::now()->subDays(3)->toDateString(),
                'due_date' => Carbon::now()->addDays(7)->toDateString(),
                'status' => $index === 0 ? 'paid' : 'partial',
                'tax_amount' => 50,
                'discount_amount' => 20,
                'notes' => 'Seeded invoice for billing demo.',
                'created_by' => $admin?->id,
            ]);

            $invoice->invoice_number = $invoice->generateInvoiceNumber();
            $invoice->save();

            $items = [
                ['type' => 'consultation', 'desc' => 'General checkup', 'qty' => 1, 'price' => 500],
                ['type' => 'medication', 'desc' => 'Antibiotics', 'qty' => 2, 'price' => 150],
            ];

            $subtotal = 0;
            foreach ($items as $item) {
                $total = $item['qty'] * $item['price'];
                $subtotal += $total;

                BillingInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['type'],
                    'description' => $item['desc'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'total_price' => $total,
                ]);
            }

            $invoice->subtotal = $subtotal;
            $invoice->total_amount = $subtotal + $invoice->tax_amount - $invoice->discount_amount;

            if ($invoice->status === 'paid') {
                $invoice->paid_amount = $invoice->total_amount;
            } else {
                $invoice->paid_amount = round($invoice->total_amount / 2, 2);
            }

            $invoice->save();

            if ($invoice->paid_amount > 0) {
                BillingPayment::create([
                    'invoice_id' => $invoice->id,
                    'payment_date' => Carbon::now()->subDay(),
                    'amount' => $invoice->paid_amount,
                    'payment_method' => 'cash',
                    'transaction_id' => 'SEED-' . $invoice->id,
                    'notes' => 'Seeded payment',
                    'received_by' => $admin?->id,
                ]);
            }
        }
    }
}
