<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\File;

class InventoryController extends BaseController
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::with('supplier');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $status = $request->get('status');

        // Filter by status dropdown (fallback to old checkbox params)
        if ($status === 'low_stock' || $request->boolean('low_stock')) {
            $query->whereRaw('quantity <= min_stock AND quantity IS NOT NULL AND min_stock IS NOT NULL');
        }

        if ($status === 'expiring_soon' || $request->boolean('expiring_soon')) {
            $query->where('expiry_date', '<=', now()->addDays(10))
                ->where('expiry_date', '>=', now())
                ->whereNotNull('expiry_date');
        }

        if ($status === 'expired' || $request->boolean('expired')) {
            $query->where('expiry_date', '<', now())
                ->whereNotNull('expiry_date');
        }

        $inventoryItems = $query->paginate(15);

        // Get summary statistics
        $totalItems = InventoryItem::count();
        $lowStockItems = InventoryItem::whereRaw('quantity <= min_stock AND quantity IS NOT NULL AND min_stock IS NOT NULL')->count();
        $expiringSoonItems = InventoryItem::where('expiry_date', '<=', now()->addDays(10))
            ->where('expiry_date', '>=', now())
            ->whereNotNull('expiry_date')
            ->count();
        $expiredItems = InventoryItem::where('expiry_date', '<', now())
            ->whereNotNull('expiry_date')
            ->count();

        $categories = ['medicine', 'vaccine', 'supply', 'food', 'other'];

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
        $suppliers = Supplier::orderBy('supplier_name')->get();
        return view('admin.inventory.create', compact('suppliers'));
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $category = $request->category;
        $requiresExpiry = in_array($category, ['medicine', 'vaccine', 'food']);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'category' => 'required|in:medicine,vaccine,supply,food,other',
            'sku' => 'nullable|string|max:50|unique:inventory_items,sku',
            'manufacturer' => 'nullable|string|max:150',
            'batch_number' => 'nullable|string|max:100',
            'dosage_form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'requires_prescription' => 'boolean',
            'controlled_substance' => 'boolean',
            'storage_instructions' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'expiry_date' => $requiresExpiry ? 'nullable|date|after:today' : 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Remove expiry_date if not required for this category
        if (!$requiresExpiry) {
            $validated['expiry_date'] = null;
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

        InventoryItem::create($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    /**
     * Display the specified inventory item.
     */
    public function show($id)
    {
        $item = InventoryItem::with('supplier')->findOrFail($id);
        return view('admin.inventory.show', compact('item'));
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit($id)
    {
        $item = InventoryItem::with('supplier')->findOrFail($id);
        $suppliers = Supplier::orderBy('supplier_name')->get();
        return view('admin.inventory.edit', compact('item', 'suppliers'));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);
        $category = $request->category;
        $requiresExpiry = in_array($category, ['medicine', 'vaccine', 'food']);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'category' => 'required|in:medicine,vaccine,supply,food,other',
            'sku' => 'nullable|string|max:50|unique:inventory_items,sku,' . $id,
            'manufacturer' => 'nullable|string|max:150',
            'batch_number' => 'nullable|string|max:100',
            'dosage_form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'requires_prescription' => 'boolean',
            'controlled_substance' => 'boolean',
            'storage_instructions' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'expiry_date' => $requiresExpiry ? 'nullable|date|after:today' : 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Remove expiry_date if not required for this category
        if (!$requiresExpiry) {
            $validated['expiry_date'] = null;
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

        $item->update($validated);

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
}