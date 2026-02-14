<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\Prescription;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;

class PharmacyController extends BaseController
{
    /**
     * Display a listing of pharmacy items (medicines, vaccines, food, supplies).
     */
    public function index(Request $request)
    {
        $query = InventoryItem::whereIn('category', ['medicine', 'vaccine', 'food', 'supply'])
            ->with('inventoryStocks')
            ->orderBy('name');
        
        // Apply category filter if provided
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $medications = $query->get();
        
        $totalMedications = $medications->count();
        $lowStockCount = $medications->filter(fn($item) => $item->isLowStock())->count();
        $expiredCount = $medications->filter(fn($item) => $item->isExpired())->count();
        $expiringSoonCount = $medications->filter(fn($item) => $item->isExpiringSoon())->count();
        
        $categories = ['medicine', 'vaccine', 'food', 'supply'];
        $selectedCategory = $request->category;
        
        return view('admin.pharmacy.index', compact(
            'medications', 
            'totalMedications', 
            'lowStockCount',
            'expiredCount',
            'expiringSoonCount',
            'categories',
            'selectedCategory'
        ));
    }

    /**
     * Show the form for creating a new pharmacy item.
     */
    public function create()
    {
        $categories = ['medicine', 'vaccine', 'food', 'supply'];
        return view('admin.pharmacy.create', compact('categories'));
    }

    /**
     * Store a newly created pharmacy item in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:inventory_items,name,' . ($request->id ?? 'NULL'),
            'category' => 'required|in:medicine,vaccine,food,supply',
            'description' => 'nullable|string|max:1000',
            'sku' => 'nullable|string|max:50|unique:inventory_items,sku,' . ($request->id ?? 'NULL'),
            'unit_price' => 'required|numeric|min:0.01|max:99999.99',
            'quantity' => 'required|integer|min:1|max:999999',
            'min_stock' => 'required|integer|min:0|max:999999',
            'max_stock' => 'nullable|integer|min:0|gte:min_stock',
            'expiry_date' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048|dimensions:min_width=100,min_height=100',
        ], [
            'name.unique' => 'A medication with this name already exists.',
            'name.required' => 'Medication name is required.',
            'category.required' => 'Category is required.',
            'category.in' => 'Invalid category selected.',
            'sku.unique' => 'This SKU is already in use.',
            'unit_price.min' => 'Unit price must be at least 0.01.',
            'unit_price.numeric' => 'Unit price must be a valid number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 999,999.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'min_stock.min' => 'Minimum stock cannot be negative.',
            'max_stock.gte' => 'Maximum stock must be greater than or equal to minimum stock.',
            'expiry_date.after' => 'Expiry date must be in the future.',
            'image.mimes' => 'Image must be a valid image file (JPEG, PNG, GIF).',
            'image.max' => 'Image size cannot exceed 2MB.',
            'image.dimensions' => 'Image must be at least 100x100 pixels.',
        ]);

        // Additional validation: quantity must not exceed max_stock if max_stock is set
        if (!empty($data['max_stock']) && $data['quantity'] > $data['max_stock']) {
            return back()->withErrors(['quantity' => 'Quantity cannot exceed maximum stock (' . $data['max_stock'] . ')'])->withInput();
        }

        DB::beginTransaction();
        
        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $data['name']) . '.' . $image->getClientOriginalExtension();
                
                // Move image to public directory
                $image->move(public_path('images/medications'), $imageName);
                $imagePath = 'images/medications/' . $imageName;
            }

            // Create inventory item
            $medication = InventoryItem::create([
                'name' => $data['name'],
                'category' => $data['category'],
                'description' => $data['description'] ?? null,
                'sku' => $data['sku'] ?? null,
                'unit_price' => $data['unit_price'],
                'image_path' => $imagePath,
                'is_active' => true,
            ]);

            // Create inventory stock record
            $stock = InventoryStock::create([
                'item_id' => $medication->id,
                'quantity' => $data['quantity'],
                'min_stock' => $data['min_stock'],
                'max_stock' => $data['max_stock'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'location' => $data['location'] ?? null,
            ]);

            // Create initial inventory transaction
            InventoryTransaction::create([
                'stock_id' => $stock->id,
                'type' => 'in',
                'quantity' => $data['quantity'],
                'reference' => 'Initial stock entry',
                'performed_by' => auth()->id(),
                'notes' => 'Initial stock for medication: ' . $medication->name,
            ]);

            DB::commit();

            return redirect()->route('admin.pharmacy.index')
                ->with('success', 'Medication added successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the detailed error for debugging
            \Log::error('Pharmacy store error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors($e->getMessage())
                ->with('error', 'Failed to add medication: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified pharmacy item.
     */
    public function show($id)
    {
        $medication = InventoryItem::where('category', 'medicine')
            ->with(['inventoryStocks', 'inventoryStocks.transactions' => function($query) {
                $query->with('performedBy')
                      ->orderBy('transaction_date', 'desc');
            }])
            ->findOrFail($id);
        
        $recentTransactions = $medication->inventoryStocks->flatMap(function($stock) {
            return $stock->transactions;
        })->take(10);
        
        return view('admin.pharmacy.show', compact('medication', 'recentTransactions'));
    }

    /**
     * Show the form for editing the specified pharmacy item.
     */
    public function edit($id)
    {
        $medication = InventoryItem::whereIn('category', ['medicine', 'vaccine', 'food', 'supply'])
            ->with('inventoryStocks')
            ->findOrFail($id);
        
        $categories = ['medicine', 'vaccine', 'food', 'supply'];
        return view('admin.pharmacy.edit', compact('medication', 'categories'));
    }

    /**
     * Update the specified pharmacy item in storage.
     */
    public function update(Request $request, $id)
    {
        $medication = InventoryItem::whereIn('category', ['medicine', 'vaccine', 'food', 'supply'])
            ->with('inventoryStocks')
            ->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:150|unique:inventory_items,name,' . $id,
            'category' => 'required|in:medicine,vaccine,food,supply',
            'description' => 'nullable|string|max:1000',
            'sku' => 'nullable|string|max:50|unique:inventory_items,sku,' . $id,
            'unit_price' => 'required|numeric|min:0.01|max:99999.99',
            'quantity' => 'required|integer|min:0|max:999999',
            'min_stock' => 'required|integer|min:0|max:999999',
            'max_stock' => 'nullable|integer|min:0|gte:min_stock',
            'expiry_date' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048|dimensions:min_width=100,min_height=100',
        ], [
            'name.unique' => 'A medication with this name already exists.',
            'name.required' => 'Medication name is required.',
            'category.required' => 'Category is required.',
            'category.in' => 'Invalid category selected.',
            'sku.unique' => 'This SKU is already in use.',
            'unit_price.min' => 'Unit price must be at least 0.01.',
            'unit_price.numeric' => 'Unit price must be a valid number.',
            'quantity.min' => 'Quantity must be at least 0.',
            'quantity.max' => 'Quantity cannot exceed 999,999.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'min_stock.min' => 'Minimum stock cannot be negative.',
            'max_stock.gte' => 'Maximum stock must be greater than or equal to minimum stock.',
            'expiry_date.after' => 'Expiry date must be in the future.',
            'image.mimes' => 'Image must be a valid image file (JPEG, PNG, GIF).',
            'image.max' => 'Image size cannot exceed 2MB.',
            'image.dimensions' => 'Image must be at least 100x100 pixels.',
        ]);

        // Additional validation: quantity must not exceed max_stock if max_stock is set
        if (!empty($data['max_stock']) && $data['quantity'] > $data['max_stock']) {
            return back()->withErrors(['quantity' => 'Quantity cannot exceed maximum stock (' . $data['max_stock'] . ')'])->withInput();
        }

        DB::beginTransaction();
        
        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $data['name']) . '.' . $image->getClientOriginalExtension();
                
                // Move image to public directory
                $image->move(public_path('images/medications'), $imageName);
                $imagePath = 'images/medications/' . $imageName;
                
                // Delete old image if exists
                if ($medication->image_path && file_exists(public_path($medication->image_path))) {
                    unlink(public_path($medication->image_path));
                }
            } else {
                $imagePath = $medication->image_path;
            }

            // Update inventory item
            $medication->update([
                'name' => $data['name'],
                'category' => $data['category'],
                'description' => $data['description'] ?? null,
                'sku' => $data['sku'] ?? null,
                'unit_price' => $data['unit_price'],
                'image_path' => $imagePath,
                'is_active' => true,
            ]);

            // Update or create inventory stock
            $stock = $medication->inventoryStocks->first();
            $oldQuantity = $stock ? $stock->quantity : 0;
            $newQuantity = $data['quantity'];
            
            if ($stock) {
                $stock->update([
                    'quantity' => $data['quantity'],
                    'min_stock' => $data['min_stock'],
                    'max_stock' => $data['max_stock'] ?? null,
                    'expiry_date' => $data['expiry_date'] ?? null,
                    'location' => $data['location'] ?? null,
                ]);
            } else {
                $stock = InventoryStock::create([
                    'item_id' => $medication->id,
                    'quantity' => $data['quantity'],
                    'min_stock' => $data['min_stock'],
                    'max_stock' => $data['max_stock'] ?? null,
                    'expiry_date' => $data['expiry_date'] ?? null,
                    'location' => $data['location'] ?? null,
                ]);
            }
            
            // Create inventory transaction if quantity changed
            if ($oldQuantity != $newQuantity) {
                $difference = $newQuantity - $oldQuantity;
                $transactionType = $difference > 0 ? 'in' : 'adjustment';
                
                InventoryTransaction::create([
                    'stock_id' => $stock->id,
                    'type' => $transactionType,
                    'quantity' => abs($difference),
                    'reference' => 'Stock adjustment during update',
                    'performed_by' => auth()->id(),
                    'notes' => 'Updated medication: ' . $medication->name,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.pharmacy.show', $medication->id)
                ->with('success', 'Medication updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the detailed error for debugging
            \Log::error('Pharmacy update error: ' . $e->getMessage(), [
                'medication_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors($e->getMessage())
                ->with('error', 'Failed to update medication: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified pharmacy item from storage.
     */
    public function destroy($id)
    {
        $medication = InventoryItem::where('category', 'medicine')
            ->findOrFail($id);
        
        $medication->delete();

        return redirect()->route('admin.pharmacy.index')
            ->with('success', 'Medication deleted successfully.');
    }
    
    /**
     * Show medication dispensing form.
     */
    public function dispenseForm()
    {
        $prescriptions = Prescription::with(['medicalRecord.pet', 'medicalRecord.veterinarian'])
            ->where('dispensed', false)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $medications = InventoryItem::where('category', 'medicine')
            ->whereHas('inventoryStocks', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->with('inventoryStocks') // Eager load stocks for sum calculation
            ->orderBy('name')
            ->get();
        
        return view('admin.pharmacy.dispense', compact('prescriptions', 'medications'));
    }
    
    /**
     * Process medication dispensing.
     */
    public function dispense(Request $request)
    {
        $data = $request->validate([
            'prescription_id' => 'required|exists:prescriptions,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity_dispensed' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $medication = InventoryItem::with('inventoryStocks')->findOrFail($data['inventory_item_id']);
        $prescription = Prescription::findOrFail($data['prescription_id']);
        
        // Check stock availability
        $totalStock = $medication->inventoryStocks->sum('quantity');
        if ($totalStock < $data['quantity_dispensed']) {
            return back()->withErrors(['quantity_dispensed' => 'Insufficient stock available.']);
        }
        
        DB::beginTransaction();
        
        try {
            // Find the stock record to deduct from (FIFO - use oldest stock first)
            $stock = $medication->inventoryStocks
                ->where('quantity', '>', 0)
                ->sortBy('expiry_date')
                ->first();
            
            if (!$stock) {
                throw new \Exception('No available stock found');
            }
            
            // Create medication dispensing record (if this model exists)
            if (class_exists('App\Models\MedicationDispensing')) {
                MedicationDispensing::create([
                    'prescription_id' => $data['prescription_id'],
                    'inventory_item_id' => $data['inventory_item_id'],
                    'dispensed_by' => auth()->id(),
                    'quantity_dispensed' => $data['quantity_dispensed'],
                    'unit_price' => $medication->unit_price,
                    'total_price' => $data['quantity_dispensed'] * $medication->unit_price,
                    'dispensed_at' => now(),
                    'instructions' => $data['instructions'] ?? $prescription->instructions,
                    'notes' => $data['notes'],
                ]);
            }
            
            // Update stock
            $stock->decrement('quantity', $data['quantity_dispensed']);
            
            // Create inventory transaction
            InventoryTransaction::create([
                'stock_id' => $stock->id,
                'type' => 'out',
                'quantity' => $data['quantity_dispensed'],
                'reference' => 'Medication dispensed for prescription #' . $prescription->id,
                'performed_by' => auth()->id(),
                'notes' => 'Dispensed: ' . $medication->name,
            ]);
            
            // Update prescription as dispensed
            $prescription->update(['dispensed' => true]);
            
            DB::commit();
            
            return redirect()->route('admin.pharmacy.dispensing')
                ->with('success', 'Medication dispensed successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to dispense medication: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Show dispensing history.
     */
    public function dispensingHistory(Request $request)
    {
        $medications = InventoryItem::where('category', 'medicine')->orderBy('name')->get();

        $query = InventoryTransaction::where('type', 'out')
            ->with(['stock.inventoryItem', 'performedBy'])
            ->orderBy('transaction_date', 'desc');

        if ($request->filled('medication_id')) {
            $query->whereHas('stock.inventoryItem', function($q) use ($request) {
                $q->where('id', $request->medication_id);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $dispensingRecords = $query->paginate(50)->appends($request->query());

        return view('admin.pharmacy.dispensing-history', compact('dispensingRecords', 'medications'));
    }
    
    /**
     * Show inventory alerts.
     */
    public function alerts()
    {
        $lowStockStocks = InventoryStock::with('inventoryItem')
            ->whereColumn('quantity', '<=', 'min_stock')
            ->get();
        $lowStockItems = $lowStockStocks->map(fn ($s) => $s->inventoryItem)->filter()->unique('id')->values();

        $expiredStocks = InventoryStock::with('inventoryItem')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->get();
        $expiredItems = $expiredStocks->map(fn ($s) => $s->inventoryItem)->filter()->unique('id')->values();

        $expiringSoonStocks = InventoryStock::with('inventoryItem')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->get();
        $expiringSoonItems = $expiringSoonStocks->map(fn ($s) => $s->inventoryItem)->filter()->unique('id')->values();

        return view('admin.pharmacy.alerts', compact(
            'lowStockItems',
            'expiredItems',
            'expiringSoonItems'
        ));
    }
}
