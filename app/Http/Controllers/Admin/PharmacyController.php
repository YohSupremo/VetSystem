<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class PharmacyController extends BaseController
{
    /**
     * Display a listing of pharmacy items.
     */
    public function index()
    {
        return view('admin.pharmacy.index');
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
        // Placeholder for pharmacy store logic
        return redirect()->route('admin.pharmacy.index')->with('success', 'Pharmacy item created successfully.');
    }

    /**
     * Display the specified pharmacy item.
     */
    public function show($id)
    {
        return view('admin.pharmacy.show');
    }

    /**
     * Show the form for editing the specified pharmacy item.
     */
    public function edit($id)
    {
        return view('admin.pharmacy.edit');
    }

    /**
     * Update the specified pharmacy item in storage.
     */
    public function update(Request $request, $id)
    {
        // Placeholder for pharmacy update logic
        return redirect()->route('admin.pharmacy.index')->with('success', 'Pharmacy item updated successfully.');
    }

    /**
     * Remove the specified pharmacy item from storage.
     */
    public function destroy($id)
    {
        // Placeholder for pharmacy delete logic
        return redirect()->route('admin.pharmacy.index')->with('success', 'Pharmacy item deleted successfully.');
    }
}
