<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pet;
use Illuminate\Http\Request;

class BoardingController extends BaseController
{
    /**
     * Display a listing of boarding services.
     */
    public function index()
    {
        $pets = Pet::with('owner')->get();
        $boardings = [];
        $currentBoardings = 0;
        $availableCages = 0;
        $upcomingCheckouts = 0;
        $cages = []; // For the form dropdown
        return view('admin.boarding.index', compact('pets', 'boardings', 'currentBoardings', 'availableCages', 'upcomingCheckouts', 'cages'));
    }

    /**
     * Show the form for creating a new boarding service.
     */
    public function create()
    {
        return view('admin.boarding.create');
    }

    /**
     * Store a newly created boarding service in storage.
     */
    public function store(Request $request)
    {
        // Placeholder for boarding store logic
        return redirect()->route('admin.boarding.index')->with('success', 'Boarding created successfully.');
    }

    /**
     * Display the specified boarding service.
     */
    public function show($id)
    {
        return view('admin.boarding.show');
    }

    /**
     * Show the form for editing the specified boarding service.
     */
    public function edit($id)
    {
        return view('admin.boarding.edit');
    }

    /**
     * Update the specified boarding service in storage.
     */
    public function update(Request $request, $id)
    {
        // Placeholder for boarding update logic
        return redirect()->route('admin.boarding.index')->with('success', 'Boarding updated successfully.');
    }

    /**
     * Remove the specified boarding service from storage.
     */
    public function destroy($id)
    {
        // Placeholder for boarding delete logic
        return redirect()->route('admin.boarding.index')->with('success', 'Boarding deleted successfully.');
    }
}
