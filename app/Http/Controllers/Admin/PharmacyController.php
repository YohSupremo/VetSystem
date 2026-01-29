<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\InventoryItem;

class PharmacyController extends BaseController
{
    /**
     * Display a listing of pharmacy items (medicines).
     */
    public function index()
    {
        $medications = InventoryItem::where('category', 'medicine')
            ->orderBy('name')
            ->get();
        
        $totalMedications = $medications->count();
        $lowStockCount = 0; // Can be enhanced later with inventory_stock
        
        return view('admin.pharmacy.index', compact('medications', 'totalMedications', 'lowStockCount'));
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
            'sku' => 'nullable|string|max:50|unique:inventory_items,sku',
            'unit_price' => 'required|numeric|min:0',
        ]);

        InventoryItem::create([
            'name' => $data['name'],
            'category' => 'medicine',
            'sku' => $data['sku'] ?? null,
            'unit_price' => $data['unit_price'],
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
            ->findOrFail($id);
        
        return view('admin.pharmacy.show', compact('medication'));
    }

    /**
     * Show the form for editing the specified pharmacy item.
     */
    public function edit($id)
    {
        $medication = InventoryItem::where('category', 'medicine')
            ->findOrFail($id);
        
        return view('admin.pharmacy.edit', compact('medication'));
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
            'sku' => 'nullable|string|max:50|unique:inventory_items,sku,' . $id,
            'unit_price' => 'required|numeric|min:0',
        ]);

        $medication->update([
            'name' => $data['name'],
            'sku' => $data['sku'] ?? null,
            'unit_price' => $data['unit_price'],
        ]);

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
}
