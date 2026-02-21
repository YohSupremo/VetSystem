<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\File;

class InventoryController extends BaseController
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::with('inventoryStocks');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $status = $request->get('status');

        // Filter by status dropdown (fallback to old checkbox params)
        if ($status === 'low_stock' || $request->boolean('low_stock')) {
            $query->whereHas('inventoryStocks', function ($stockQuery) {
                $stockQuery->whereColumn('quantity', '<=', 'min_stock');
            });
        }

        if ($status === 'expiring_soon' || $request->boolean('expiring_soon')) {
            $query->whereHas('inventoryStocks', function ($stockQuery) {
                $stockQuery->whereNotNull('expiry_date')
                    ->where('expiry_date', '>=', now())
                    ->where('expiry_date', '<=', now()->addDays(10));
            });
        }

        if ($status === 'expired' || $request->boolean('expired')) {
            $query->whereHas('inventoryStocks', function ($stockQuery) {
                $stockQuery->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', now());
            });
        }

        $inventoryItems = $query->paginate(15);

        // Get summary statistics
        $totalItems = InventoryItem::count();
        $lowStockItems = InventoryItem::whereHas('inventoryStocks', function ($stockQuery) {
            $stockQuery->whereColumn('quantity', '<=', 'min_stock');
        })->count();
        $expiringSoonItems = InventoryItem::whereHas('inventoryStocks', function ($stockQuery) {
            $stockQuery->whereNotNull('expiry_date')
                ->where('expiry_date', '>=', now())
                ->where('expiry_date', '<=', now()->addDays(10));
        })->count();
        $expiredItems = InventoryItem::whereHas('inventoryStocks', function ($stockQuery) {
            $stockQuery->whereNotNull('expiry_date')
                ->where('expiry_date', '<', now());
        })->count();

        $categories = ['medicine', 'vaccine', 'supply', 'food', 'toy', 'accessory', 'other'];

        return view('admin.inventory.index', compact(
            'inventoryItems',
            'totalItems',
            'lowStockItems',
            'expiringSoonItems',
            'expiredItems',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        return view('admin.inventory.create');
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'category' => 'required|in:medicine,vaccine,supply,food,toy,accessory,other',
            'sku' => 'required|string|max:50|unique:inventory_items,sku',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0|gte:min_stock',
            'expiry_date' => 'nullable|date',
            'location' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Additional validation: quantity must not exceed max_stock if max_stock is set
        if (!empty($validated['max_stock']) && $validated['quantity'] > $validated['max_stock']) {
            return back()->withErrors(['quantity' => 'Quantity cannot exceed maximum stock (' . $validated['max_stock'] . ')'])->withInput();
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $directory = public_path('uploads/inventory-items');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            $filename = $file->hashName();
            $file->move($directory, $filename);
            $validated['image_path'] = 'uploads/inventory-items/' . $filename;
        }

        $item = InventoryItem::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'sku' => $validated['sku'],
            'unit_price' => $validated['unit_price'],
            'image_path' => $validated['image_path'] ?? null,
            'is_active' => true,
        ]);

        $stock = InventoryStock::create([
            'item_id' => $item->id,
            'quantity' => $validated['quantity'],
            'min_stock' => $validated['min_stock'],
            'max_stock' => $validated['max_stock'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'location' => $validated['location'] ?? null,
        ]);

        $this->notifyInventoryAlerts($notificationService, $item, $stock);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    /**
     * Display the specified inventory item.
     */
    public function show($id)
    {
        $item = InventoryItem::with('inventoryStocks')->findOrFail($id);
        return view('admin.inventory.show', compact('item'));
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit($id)
    {
        $item = InventoryItem::with('inventoryStocks')->findOrFail($id);
        return view('admin.inventory.edit', compact('item'));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, $id, NotificationService $notificationService)
    {
        $item = InventoryItem::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'category' => 'required|in:medicine,vaccine,supply,food,toy,accessory,other',
            'sku' => 'required|string|max:50|unique:inventory_items,sku,' . $id,
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0|gte:min_stock',
            'expiry_date' => 'nullable|date',
            'location' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Additional validation: quantity must not exceed max_stock if max_stock is set
        if (!empty($validated['max_stock']) && $validated['quantity'] > $validated['max_stock']) {
            return back()->withErrors(['quantity' => 'Quantity cannot exceed maximum stock (' . $validated['max_stock'] . ')'])->withInput();
        }

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                File::delete(public_path($item->image_path));
            }
            $file = $request->file('image');
            $directory = public_path('uploads/inventory-items');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            $filename = $file->hashName();
            $file->move($directory, $filename);
            $validated['image_path'] = 'uploads/inventory-items/' . $filename;
        }

        $item->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'sku' => $validated['sku'],
            'unit_price' => $validated['unit_price'],
            'image_path' => $validated['image_path'] ?? $item->image_path,
        ]);

        $stock = $item->inventoryStocks()->first();
        if ($stock) {
            $stock->update([
                'quantity' => $validated['quantity'],
                'min_stock' => $validated['min_stock'],
                'max_stock' => $validated['max_stock'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'location' => $validated['location'] ?? null,
            ]);
        } else {
            $stock = InventoryStock::create([
                'item_id' => $item->id,
                'quantity' => $validated['quantity'],
                'min_stock' => $validated['min_stock'],
                'max_stock' => $validated['max_stock'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'location' => $validated['location'] ?? null,
            ]);
        }

        if ($stock) {
            $this->notifyInventoryAlerts($notificationService, $item, $stock);
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified inventory item from storage.
     */
    public function destroy($id)
    {
        $item = InventoryItem::findOrFail($id);
        if ($item->image_path) {
            File::delete(public_path($item->image_path));
        }
        $item->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    private function notifyInventoryAlerts(NotificationService $notificationService, InventoryItem $item, InventoryStock $stock): void
    {
        if ((int) $stock->quantity <= (int) $stock->min_stock) {
            $notificationService->sendToRole(
                'pharmacy',
                Notification::TYPE_INVENTORY,
                'Low Stock Alert',
                $item->name . ' is low (' . $stock->quantity . ' remaining).',
                [
                    'reference_type' => 'inventory',
                    'reference_id' => $item->id,
                    'action_url' => route('admin.inventory.show', $item->id),
                ]
            );
        }

        if ($stock->expiry_date) {
            $expiryDate = \Carbon\Carbon::parse($stock->expiry_date);
            if ($expiryDate->isPast() || $expiryDate->isBetween(now(), now()->addDays(10))) {
                $notificationService->sendToRole(
                    'pharmacy',
                    Notification::TYPE_EXPIRY,
                    'Item Expiring Soon',
                    $item->name . ' expires on ' . $expiryDate->toDateString() . '.',
                    [
                        'reference_type' => 'inventory',
                        'reference_id' => $item->id,
                        'action_url' => route('admin.inventory.show', $item->id),
                    ]
                );
            }
        }
    }
}