<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Database\Seeder;

class PharmacyInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Amoxicillin 250mg Capsule',
                'category' => 'medicine',
                'sku' => 'MED-AMOX-250',
                'description' => 'Broad-spectrum antibiotic for bacterial infections',
                'unit_price' => 18.50,
                'quantity' => 120,
                'min_stock' => 30,
                'max_stock' => 200,
                'expiry_date' => now()->addMonths(18)->toDateString(),
                'location' => 'Cabinet A1',
            ],
            [
                'name' => 'Carprofen 75mg Chewable',
                'category' => 'medicine',
                'sku' => 'MED-CARP-075',
                'description' => 'NSAID for canine pain and inflammation',
                'unit_price' => 32.00,
                'quantity' => 80,
                'min_stock' => 20,
                'max_stock' => 150,
                'expiry_date' => now()->addMonths(16)->toDateString(),
                'location' => 'Cabinet A2',
            ],
            [
                'name' => 'Prednisone 5mg Tablet',
                'category' => 'medicine',
                'sku' => 'MED-PRED-005',
                'description' => 'Anti-inflammatory corticosteroid tablet',
                'unit_price' => 12.75,
                'quantity' => 140,
                'min_stock' => 40,
                'max_stock' => 220,
                'expiry_date' => now()->addMonths(20)->toDateString(),
                'location' => 'Cabinet A3',
            ],
            [
                'name' => 'Cefalexin Oral Suspension 100ml',
                'category' => 'medicine',
                'sku' => 'MED-CEFA-100',
                'description' => 'Oral antibiotic suspension for dogs and cats',
                'unit_price' => 145.00,
                'quantity' => 45,
                'min_stock' => 12,
                'max_stock' => 90,
                'expiry_date' => now()->addMonths(12)->toDateString(),
                'location' => 'Refrigerator R1',
            ],
            [
                'name' => 'DHPPi Vaccine 1ml',
                'category' => 'vaccine',
                'sku' => 'VAC-DHPP-001',
                'description' => 'Canine distemper, hepatitis, parvovirus, and parainfluenza vaccine',
                'unit_price' => 280.00,
                'quantity' => 60,
                'min_stock' => 20,
                'max_stock' => 120,
                'expiry_date' => now()->addMonths(10)->toDateString(),
                'location' => 'Cold Storage V1',
            ],
            [
                'name' => 'Anti-Rabies Vaccine 1ml',
                'category' => 'vaccine',
                'sku' => 'VAC-RAB-001',
                'description' => 'Inactivated rabies vaccine for annual protection',
                'unit_price' => 250.00,
                'quantity' => 70,
                'min_stock' => 25,
                'max_stock' => 140,
                'expiry_date' => now()->addMonths(11)->toDateString(),
                'location' => 'Cold Storage V2',
            ],
            [
                'name' => 'FVRCP Vaccine 1ml',
                'category' => 'vaccine',
                'sku' => 'VAC-FVRC-001',
                'description' => 'Core feline vaccine for rhinotracheitis, calicivirus, and panleukopenia',
                'unit_price' => 295.00,
                'quantity' => 40,
                'min_stock' => 12,
                'max_stock' => 80,
                'expiry_date' => now()->addMonths(9)->toDateString(),
                'location' => 'Cold Storage V3',
            ],
            [
                'name' => 'Recovery Diet Can 156g',
                'category' => 'food',
                'sku' => 'FOOD-RC-156',
                'description' => 'High-energy recovery diet for post-operative and critical care patients',
                'unit_price' => 95.00,
                'quantity' => 90,
                'min_stock' => 20,
                'max_stock' => 180,
                'expiry_date' => now()->addMonths(14)->toDateString(),
                'location' => 'Shelf F1',
            ],
            [
                'name' => 'Hypoallergenic Dry Food 2kg',
                'category' => 'food',
                'sku' => 'FOOD-HYPO-2K',
                'description' => 'Limited-ingredient diet for pets with food sensitivities',
                'unit_price' => 1250.00,
                'quantity' => 25,
                'min_stock' => 8,
                'max_stock' => 50,
                'expiry_date' => now()->addMonths(13)->toDateString(),
                'location' => 'Shelf F2',
            ],
            [
                'name' => 'Sterile Syringe 3ml',
                'category' => 'supply',
                'sku' => 'SUP-SYR-3ML',
                'description' => 'Single-use sterile syringe for medication administration',
                'unit_price' => 7.50,
                'quantity' => 500,
                'min_stock' => 100,
                'max_stock' => 800,
                'expiry_date' => now()->addMonths(30)->toDateString(),
                'location' => 'Supply Rack S1',
            ],
            [
                'name' => 'Latex Examination Gloves (Box)',
                'category' => 'supply',
                'sku' => 'SUP-GLV-LTX',
                'description' => 'Powder-free latex examination gloves',
                'unit_price' => 210.00,
                'quantity' => 110,
                'min_stock' => 25,
                'max_stock' => 180,
                'expiry_date' => now()->addMonths(24)->toDateString(),
                'location' => 'Supply Rack S2',
            ],
            [
                'name' => 'Antiseptic Wound Spray 100ml',
                'category' => 'supply',
                'sku' => 'SUP-ANT-100',
                'description' => 'Topical antiseptic spray for wound cleaning',
                'unit_price' => 165.00,
                'quantity' => 65,
                'min_stock' => 15,
                'max_stock' => 120,
                'expiry_date' => now()->addMonths(18)->toDateString(),
                'location' => 'Cabinet B1',
            ],
        ];

        foreach ($items as $itemData) {
            $item = InventoryItem::updateOrCreate(
                ['sku' => $itemData['sku']],
                [
                    'name' => $itemData['name'],
                    'category' => $itemData['category'],
                    'description' => $itemData['description'],
                    'unit_price' => $itemData['unit_price'],
                    'is_active' => true,
                ]
            );

            $stock = InventoryStock::updateOrCreate(
                ['item_id' => $item->id],
                [
                    'quantity' => $itemData['quantity'],
                    'min_stock' => $itemData['min_stock'],
                    'max_stock' => $itemData['max_stock'],
                    'expiry_date' => $itemData['expiry_date'],
                    'location' => $itemData['location'],
                    'low_stock_alert_sent' => false,
                    'expiry_alert_sent' => false,
                ]
            );

            InventoryTransaction::create([
                'stock_id' => $stock->id,
                'type' => 'in',
                'quantity' => $itemData['quantity'],
                'reference' => 'Seeded opening stock',
                'performed_by' => null,
                'notes' => 'Initial seeded stock for pharmacy item: ' . $itemData['name'],
                'transaction_date' => now(),
            ]);
        }
    }
}
