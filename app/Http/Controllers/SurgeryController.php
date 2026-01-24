<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SurgeryController extends Controller
{
    /**
     * Display a listing of surgeries
     */
    public function index()
    {
        $surgeries = \App\Models\Surgery::with(['pet', 'surgeon', 'medicalRecord'])
            ->orderBy('scheduled_date', 'desc')
            ->paginate(15);

        return view('admin.surgeries.index', compact('surgeries'));
    }

    /**
     * Show the form for creating a new surgery
     */
    public function create()
    {
        $pets = \App\Models\Pet::with('owner.user')->get();
        $surgeons = \App\Models\User::where('role', 'veterinarian')->get();
        $medicalRecords = \App\Models\MedicalRecord::with('pet')->get();

        return view('admin.surgeries.create', compact('pets', 'surgeons', 'medicalRecords'));
    }

    /**
     * Store a newly created surgery
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'surgeon_id' => 'required|exists:users,id',
            'procedure_name' => 'required|string|max:255',
            'scheduled_date' => 'required|date_format:Y-m-d\TH:i',
            'anesthesia_type' => 'nullable|string|max:255',
            'pre_op_notes' => 'nullable|string',
            'post_op_instructions' => 'nullable|string',
            'medical_record_id' => 'nullable|exists:medical_records,id',
        ]);

        \App\Models\Surgery::create([
            'pet_id' => $validated['pet_id'],
            'surgeon_id' => $validated['surgeon_id'],
            'procedure_name' => $validated['procedure_name'],
            'scheduled_date' => $validated['scheduled_date'],
            'anesthesia_type' => $validated['anesthesia_type'],
            'pre_op_notes' => $validated['pre_op_notes'],
            'post_op_instructions' => $validated['post_op_instructions'],
            'medical_record_id' => $validated['medical_record_id'],
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.surgeries.index')
            ->with('success', 'Surgery scheduled successfully!');
    }

    /**
     * Display the specified surgery
     */
    public function show(string $id)
    {
        $surgery = \App\Models\Surgery::with(['pet.owner.user', 'surgeon', 'medicalRecord'])
            ->findOrFail($id);

        return view('admin.surgeries.show', compact('surgery'));
    }

    /**
     * Show the form for editing the surgery
     */
    public function edit(string $id)
    {
        $surgery = \App\Models\Surgery::findOrFail($id);
        $pets = \App\Models\Pet::with('owner.user')->get();
        $surgeons = \App\Models\User::where('role', 'veterinarian')->get();

        return view('admin.surgeries.edit', compact('surgery', 'pets', 'surgeons'));
    }

    /**
     * Update the specified surgery
     */
    public function update(Request $request, string $id)
    {
        $surgery = \App\Models\Surgery::findOrFail($id);

        $validated = $request->validate([
            'surgeon_id' => 'required|exists:users,id',
            'procedure_name' => 'required|string|max:255',
            'scheduled_date' => 'required|date_format:Y-m-d\TH:i',
            'anesthesia_type' => 'nullable|string|max:255',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
            'pre_op_notes' => 'nullable|string',
            'surgery_notes' => 'nullable|string',
            'post_op_instructions' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $surgery->update($validated);

        return redirect()->route('admin.surgeries.show', $surgery->id)
            ->with('success', 'Surgery updated successfully!');
    }

    /**
     * Delete the specified surgery
     */
    public function destroy(string $id)
    {
        $surgery = \App\Models\Surgery::findOrFail($id);
        $surgery->delete();

        return redirect()->route('admin.surgeries.index')
            ->with('success', 'Surgery deleted successfully!');
    }
}
