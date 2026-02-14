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
     * Display a listing of prescriptions - one entry per pet showing latest prescription.
     */
    public function index(Request $request)
    {
        $query = Prescription::with(['medicalRecord.pet.owner.user', 'medicalRecord.veterinarian']);
        
        // Apply filters
        if ($request->filled('pet_id')) {
            $query->whereHas('medicalRecord', function($q) use ($request) {
                $q->where('pet_id', $request->pet_id);
            });
        }
        
        if ($request->filled('dispensed')) {
            $query->where('dispensed', $request->dispensed);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $prescriptions = $query->orderBy('created_at', 'desc')->get();
        
        // Group prescriptions by pet (not medical record) to show one entry per pet
        $groupedPrescriptions = $prescriptions->groupBy(function($prescription) {
            return $prescription->medicalRecord->pet_id;
        })->map(function($group) {
            $latestPrescription = $group->first();
            return [
                'medical_record' => $latestPrescription->medicalRecord,
                'latest_prescription' => $latestPrescription,
                'count' => $group->count()
            ];
        })->sortByDesc(function($group) {
            return $group['latest_prescription']->created_at;
        });
        
        // Get all pets for filter dropdown
        $pets = Pet::with('owner.user')
            ->whereHas('medicalRecords.prescriptions')
            ->orderBy('name')
            ->get();
        
        return view('admin.prescriptions.index', compact('groupedPrescriptions', 'pets'));
    }

    /**
     * Show the form for creating a new prescription.
     * Medical records dropdown shows only records for the selected pet (when pet_id in request).
     */
    public function create()
    {
        // Only show pets that have medical records
        $pets = Pet::with('owner')
            ->whereHas('medicalRecords')
            ->get();
        $petId = request('pet_id');
        $medicalRecords = $petId
            ? MedicalRecord::where('pet_id', $petId)->with('pet')->orderBy('visit_date', 'desc')->get()
            : collect();
        
        // Get medicines from inventory for selection, with stock information
        $medicines = InventoryItem::where('category', 'medicine')
            ->with('inventoryStocks')
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
            'medical_record_id' => 'required|exists:medical_records,id',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'medication_name' => 'required|string|max:150',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        // If inventory item is selected, use its name as medication
        if ($request->filled('inventory_item_id')) {
            $inventoryItem = InventoryItem::find($request->inventory_item_id);
            $validated['medication_name'] = $inventoryItem->name;
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
        $prescription->load('medicalRecord.pet.owner.user', 'medicalRecord.veterinarian');
        return view('admin.prescriptions.show', compact('prescription'));
    }

    /**
     * Show the form for editing the specified prescription.
     * Medical records dropdown shows only records for this prescription's pet.
     */
    public function edit(Prescription $prescription)
    {
        $prescription->load('medicalRecord.pet.owner.user', 'medicalRecord.veterinarian');
        // Only show pets that have medical records
        $pets = Pet::with('owner.user')
            ->whereHas('medicalRecords')
            ->get();
        
        // Get pet_id through medical record relationship
        $petId = $prescription->medicalRecord?->pet_id;
        $medicalRecords = $petId 
            ? MedicalRecord::where('pet_id', $petId)
                ->with('pet', 'veterinarian')
                ->orderBy('visit_date', 'desc')
                ->get()
            : collect();
        
        // Get medicines from inventory for selection, with stock information
        $medicines = InventoryItem::where('category', 'medicine')
            ->with('inventoryStocks')
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
            'medical_record_id' => 'required|exists:medical_records,id',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'medication_name' => 'required|string|max:150',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        // If inventory item is selected, use its name as medication
        if ($request->filled('inventory_item_id')) {
            $inventoryItem = InventoryItem::find($request->inventory_item_id);
            $validated['medication_name'] = $inventoryItem->name;
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
     * Display prescriptions for a specific pet grouped by medical records.
     */
    public function byPet($petId)
    {
        $pet = Pet::with('owner.user')->findOrFail($petId);
        
        $prescriptions = Prescription::whereHas('medicalRecord', function($query) use ($petId) {
                $query->where('pet_id', $petId);
            })
            ->with(['medicalRecord.veterinarian'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group prescriptions by medical record
        $groupedPrescriptions = $prescriptions->groupBy('medical_record_id')->map(function($group) {
            return [
                'medical_record' => $group->first()->medicalRecord,
                'prescriptions' => $group->sortByDesc('created_at'),
                'count' => $group->count()
            ];
        })->sortByDesc(function($group) {
            return $group['prescriptions']->first()->created_at;
        });

        return view('admin.prescriptions.pet', compact('pet', 'groupedPrescriptions'));
    }
}
