<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pet;
use App\Models\Prescription;
use App\Models\MedicalRecord;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class PrescriptionController extends BaseController
{
    /**
     * Display a listing of prescriptions.
     */
    public function index()
    {
        $prescriptions = Prescription::with(['pet.owner.user', 'medicalRecord'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.prescriptions.index', compact('prescriptions'));
    }

    /**
     * Show the form for creating a new prescription.
     * Medical records dropdown shows only records for the selected pet (when pet_id in request).
     */
    public function create()
    {
        $pets = Pet::with('owner')->get();
        $petId = request('pet_id');
        $medicalRecords = $petId
            ? MedicalRecord::where('pet_id', $petId)->with('pet')->orderBy('visit_date', 'desc')->get()
            : collect();
        
        // Get medicines from inventory for selection
        $medicines = InventoryItem::where('category', 'medicine')
            ->orderBy('name')
            ->get();
        
        return view('admin.prescriptions.create', compact('pets', 'medicalRecords', 'medicines'));
    }

    /**
     * Store a newly created prescription in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'medical_record_id' => 'nullable|exists:medical_records,id',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'medication' => 'required|string|max:150',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        // If inventory item is selected, use its name as medication
        if ($request->filled('inventory_item_id')) {
            $inventoryItem = InventoryItem::find($request->inventory_item_id);
            $validated['medication'] = $inventoryItem->name;
        }

        Prescription::create($validated);

        return redirect()->route('admin.prescriptions.index')
            ->with('success', 'Prescription created successfully!');
    }

    /**
     * Display the specified prescription.
     */
    public function show(Prescription $prescription)
    {
        $prescription->load('pet.owner.user', 'medicalRecord.veterinarian');
        return view('admin.prescriptions.show', compact('prescription'));
    }

    /**
     * Show the form for editing the specified prescription.
     * Medical records dropdown shows only records for this prescription's pet.
     */
    public function edit(Prescription $prescription)
    {
        $prescription->load('pet.owner.user');
        $pets = Pet::with('owner.user')->get();
        $medicalRecords = MedicalRecord::where('pet_id', $prescription->pet_id)
            ->with('pet', 'veterinarian')
            ->orderBy('visit_date', 'desc')
            ->get();
        
        // Get medicines from inventory for selection
        $medicines = InventoryItem::where('category', 'medicine')
            ->orderBy('name')
            ->get();
        
        return view('admin.prescriptions.edit', compact('prescription', 'pets', 'medicalRecords', 'medicines'));
    }

    /**
     * Update the specified prescription in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'medical_record_id' => 'nullable|exists:medical_records,id',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'medication' => 'required|string|max:150',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        // If inventory item is selected, use its name as medication
        if ($request->filled('inventory_item_id')) {
            $inventoryItem = InventoryItem::find($request->inventory_item_id);
            $validated['medication'] = $inventoryItem->name;
        }

        $prescription->update($validated);

        return redirect()->route('admin.prescriptions.show', $prescription->id)
            ->with('success', 'Prescription updated successfully!');
    }

    /**
     * Remove the specified prescription from storage.
     */
    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('admin.prescriptions.index')
            ->with('success', 'Prescription deleted successfully!');
    }

    /**
     * Display prescriptions for a specific pet.
     */
    public function byPet($petId)
    {
        $pet = Pet::with('owner.user')->findOrFail($petId);
        $prescriptions = Prescription::where('pet_id', $petId)
            ->with('medicalRecord')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.prescriptions.pet', compact('pet', 'prescriptions'));
    }
}
