<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\MedicationDispensing;
use App\Models\Prescription;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;

class PharmacyController extends BaseController
{
    /**
     * Display a listing of pharmacy items (medicines).
     */
    public function index()
    {
        $medications = InventoryItem::where('category', 'medicine')
            ->with(['supplier', 'medicationDispensing'])
            ->orderBy('name')
            ->get();
        
        $totalMedications = $medications->count();
        $lowStockCount = $medications->filter(fn($item) => $item->isLowStock())->count();
        $expiredCount = $medications->filter(fn($item) => $item->isExpired())->count();
        $expiringSoonCount = $medications->filter(fn($item) => $item->isExpiringSoon())->count();
        
        return view('admin.pharmacy.index', compact(
            'medications', 
            'totalMedications', 
            'lowStockCount',
            'expiredCount',
            'expiringSoonCount'
        ));
    }

    /**
     * Show the form for creating a new pharmacy item.
     */
    public function create()
    {
        return view('admin.pharmacy.create');
    }

    /**
     * Store a newly created pharmacy item in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:50|unique:inventory_items,sku',
            'manufacturer' => 'nullable|string|max:150',
            'batch_number' => 'nullable|string|max:50',
            'dosage_form' => 'nullable|string|max:50',
            'strength' => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'requires_prescription' => 'boolean',
            'controlled_substance' => 'boolean',
            'storage_instructions' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $medication = InventoryItem::create([
            'name' => $data['name'],
            'category' => 'medicine',
            'description' => $data['description'] ?? null,
            'sku' => $data['sku'] ?? null,
            'manufacturer' => $data['manufacturer'] ?? null,
            'batch_number' => $data['batch_number'] ?? null,
            'dosage_form' => $data['dosage_form'] ?? null,
            'strength' => $data['strength'] ?? null,
            'unit_price' => $data['unit_price'],
            'requires_prescription' => $data['requires_prescription'] ?? false,
            'controlled_substance' => $data['controlled_substance'] ?? false,
            'storage_instructions' => $data['storage_instructions'] ?? null,
            'supplier_id' => $data['supplier_id'] ?? null,
            'quantity' => $data['quantity'],
            'min_stock' => $data['min_stock'],
            'expiry_date' => $data['expiry_date'] ?? null,
        ]);

        // Create initial inventory transaction
        InventoryTransaction::create([
            'inventory_item_id' => $medication->id,
            'transaction_type' => 'initial_stock',
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_price' => $data['quantity'] * $data['unit_price'],
            'notes' => 'Initial stock entry',
        ]);

        return redirect()->route('admin.pharmacy.index')
            ->with('success', 'Medication added successfully.');
    }

    /**
     * Display the specified pharmacy item.
     */
    public function show($id)
    {
        $medication = InventoryItem::where('category', 'medicine')
            ->with(['supplier', 'medicationDispensing' => function($query) {
                $query->with(['prescription.pet', 'dispensedBy'])
                      ->orderBy('dispensed_at', 'desc');
            }])
            ->findOrFail($id);
        
        $recentDispensing = $medication->medicationDispensing->take(10);
        
        return view('admin.pharmacy.show', compact('medication', 'recentDispensing'));
    }

    /**
     * Show the form for editing the specified pharmacy item.
     */
    public function edit($id)
    {
        $medication = InventoryItem::where('category', 'medicine')
            ->with('supplier')
            ->findOrFail($id);
        
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        
        return view('admin.pharmacy.edit', compact('medication', 'suppliers'));
    }

    /**
     * Update the specified pharmacy item in storage.
     */
    public function update(Request $request, $id)
    {
        $medication = InventoryItem::where('category', 'medicine')
            ->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:50|unique:inventory_items,sku,' . $id,
            'manufacturer' => 'nullable|string|max:150',
            'batch_number' => 'nullable|string|max:50',
            'dosage_form' => 'nullable|string|max:50',
            'strength' => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'requires_prescription' => 'boolean',
            'controlled_substance' => 'boolean',
            'storage_instructions' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $oldQuantity = $medication->quantity;
        $newQuantity = $data['quantity'];
        
        $medication->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sku' => $data['sku'] ?? null,
            'manufacturer' => $data['manufacturer'] ?? null,
            'batch_number' => $data['batch_number'] ?? null,
            'dosage_form' => $data['dosage_form'] ?? null,
            'strength' => $data['strength'] ?? null,
            'unit_price' => $data['unit_price'],
            'requires_prescription' => $data['requires_prescription'] ?? false,
            'controlled_substance' => $data['controlled_substance'] ?? false,
            'storage_instructions' => $data['storage_instructions'] ?? null,
            'supplier_id' => $data['supplier_id'] ?? null,
            'quantity' => $newQuantity,
            'min_stock' => $data['min_stock'],
            'expiry_date' => $data['expiry_date'] ?? null,
        ]);
        
        // Create inventory transaction if quantity changed
        if ($oldQuantity != $newQuantity) {
            $difference = $newQuantity - $oldQuantity;
            $transactionType = $difference > 0 ? 'stock_in' : 'stock_adjustment';
            
            InventoryTransaction::create([
                'inventory_item_id' => $medication->id,
                'transaction_type' => $transactionType,
                'quantity' => abs($difference),
                'unit_price' => $data['unit_price'],
                'total_price' => abs($difference) * $data['unit_price'],
                'notes' => 'Stock adjustment during update',
            ]);
        }

        return redirect()->route('admin.pharmacy.show', $medication->id)
            ->with('success', 'Medication updated successfully.');
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
        $prescriptions = Prescription::with(['pet', 'medicalRecord'])
            ->whereDoesntHave('medicationDispensing')
            ->orWhereHas('medicationDispensing', function($query) {
                $query->where('quantity_dispensed', '<', DB::raw('(SELECT duration_days * 1 FROM prescriptions WHERE id = prescriptions.id)'));
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        $medications = InventoryItem::where('category', 'medicine')
            ->where('quantity', '>', 0)
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
        
        $medication = InventoryItem::findOrFail($data['inventory_item_id']);
        $prescription = Prescription::findOrFail($data['prescription_id']);
        
        if ($medication->quantity < $data['quantity_dispensed']) {
            return back()->withErrors(['quantity_dispensed' => 'Insufficient stock available.']);
        }
        
        DB::beginTransaction();
        
        try {
            // Create medication dispensing record
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
            
            // Update medication stock
            $medication->decrement('quantity', $data['quantity_dispensed']);
            
            // Create inventory transaction
            InventoryTransaction::create([
                'inventory_item_id' => $medication->id,
                'transaction_type' => 'dispensed',
                'quantity' => $data['quantity_dispensed'],
                'unit_price' => $medication->unit_price,
                'total_price' => $data['quantity_dispensed'] * $medication->unit_price,
                'notes' => 'Medication dispensed for prescription #' . $prescription->id,
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
    public function dispensingHistory()
    {
        $dispensingHistory = MedicationDispensing::with(['prescription.pet', 'inventoryItem', 'dispensedBy'])
            ->orderBy('dispensed_at', 'desc')
            ->paginate(50);
            
        return view('admin.pharmacy.dispensing-history', compact('dispensingHistory'));
    }
    
    /**
     * Show inventory alerts.
     */
    public function alerts()
    {
        $lowStockItems = InventoryItem::where('category', 'medicine')
            ->whereColumn('quantity', '<=', 'min_stock')
            ->with('supplier')
            ->get();
            
        $expiredItems = InventoryItem::where('category', 'medicine')
            ->where('expiry_date', '<', now())
            ->get();
            
        $expiringSoonItems = InventoryItem::where('category', 'medicine')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->get();
            
        return view('admin.pharmacy.alerts', compact(
            'lowStockItems',
            'expiredItems',
            'expiringSoonItems'
        ));
    }
}
