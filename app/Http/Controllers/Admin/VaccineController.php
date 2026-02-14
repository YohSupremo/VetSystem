<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vaccine;
use Illuminate\Http\Request;

class VaccineController extends Controller
{
    public function index()
    {
        $vaccines = Vaccine::orderBy('vaccine_name')->paginate(15);
        return view('admin.vaccines.index', compact('vaccines'));
    }

    public function create()
    {
        return view('admin.vaccines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:150|unique:vaccines,vaccine_name',
            'manufacturer' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Ensure is_active has a value (default to 1 if not set)
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

        Vaccine::create($validated);

        return redirect()->route('admin.vaccines.index')
            ->with('success', 'Vaccine created successfully!');
    }

    public function edit($id)
    {
        $vaccine = Vaccine::findOrFail($id);
        return view('admin.vaccines.edit', compact('vaccine'));
    }

    public function update(Request $request, $id)
    {
        $vaccine = Vaccine::findOrFail($id);

        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:150|unique:vaccines,vaccine_name,' . $id,
            'manufacturer' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Ensure is_active has a value
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;

        $vaccine->update($validated);

        return redirect()->route('admin.vaccines.index')
            ->with('success', 'Vaccine updated successfully!');
    }

    public function destroy($id)
    {
        $vaccine = Vaccine::findOrFail($id);
        $vaccine->delete();

        return redirect()->route('admin.vaccines.index')
            ->with('success', 'Vaccine deleted successfully!');
    }
}
