<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class InventoryItemImageSeeder extends Seeder
{
    /**
     * Assign uploaded images from public/uploads/inventory-items to inventory items.
     */
    public function run(): void
    {
        $imageDir = public_path('uploads/inventory-items');

        if (!File::isDirectory($imageDir)) {
            $this->command?->warn('Inventory image directory not found: public/uploads/inventory-items');
            return;
        }

        $files = collect(File::files($imageDir))
            ->filter(function ($file) {
                return in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            })
            ->values();

        if ($files->isEmpty()) {
            $this->command?->warn('No inventory item images found in public/uploads/inventory-items');
            return;
        }

        $items = InventoryItem::query()->orderBy('id')->get();

        if ($items->isEmpty()) {
            $this->command?->warn('No inventory items found to assign images.');
            return;
        }

        foreach ($items as $index => $item) {
            // Cycle through available files so every product gets an image.
            $file = $files[$index % $files->count()];
            $relativePath = 'uploads/inventory-items/' . $file->getFilename();

            /** @var InventoryItem $item */
            $item->update([
                'image_path' => $relativePath,
            ]);
        }

        $this->command?->info('Assigned uploaded images to ' . $items->count() . ' inventory items.');
    }
}
