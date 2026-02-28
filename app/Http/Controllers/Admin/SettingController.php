<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClinicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends BaseController
{
    /**
     * Display a listing of settings.
     */
    public function index()
    {
        $clinicSettings = ClinicSetting::query()->first()
            ?? new ClinicSetting(ClinicSetting::defaults());

        return view('admin.settings.index', compact('clinicSettings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('admin.settings.create');
    }

    /**
     * Store a newly created setting in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'clinic_name' => 'required|string|max:150',
            'clinic_phone' => 'nullable|string|max:30',
            'clinic_email' => 'nullable|email|max:150',
            'clinic_address' => 'nullable|string',
            'timezone' => 'required|timezone',
            'currency_code' => 'required|string|size:3',
            'invoice_prefix' => 'required|string|max:10',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'appointment_slot_minutes' => 'required|integer|min:1|max:480',
            'appointment_buffer_minutes' => 'required|integer|min:0|max:240',
            'low_stock_threshold' => 'required|integer|min:0|max:100000',
        ]);

        $payload = [
            'clinic_name' => $validated['clinic_name'],
            'clinic_phone' => $validated['clinic_phone'] ?? null,
            'clinic_email' => $validated['clinic_email'] ?? null,
            'clinic_address' => $validated['clinic_address'] ?? null,
            'timezone' => $validated['timezone'],
            'currency_code' => strtoupper($validated['currency_code']),
            'invoice_prefix' => strtoupper($validated['invoice_prefix']),
            'default_tax_rate' => $validated['default_tax_rate'],
            'appointment_slot_minutes' => $validated['appointment_slot_minutes'],
            'appointment_buffer_minutes' => $validated['appointment_buffer_minutes'],
            'low_stock_threshold' => $validated['low_stock_threshold'],
            'updated_by' => Auth::id(),
        ];

        $current = ClinicSetting::query()->first();

        if ($current) {
            $current->update($payload);
        } else {
            ClinicSetting::create($payload);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Clinic settings updated successfully.');
    }

    /**
     * Display the specified setting.
     */
    public function show($id)
    {
        return view('admin.settings.show');
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit($id)
    {
        return view('admin.settings.edit');
    }

    /**
     * Update the specified setting in storage.
     */
    public function update(Request $request, $id)
    {
        return $this->store($request);
    }

    /**
     * Remove the specified setting from storage.
     */
    public function destroy($id)
    {
        // Placeholder for settings delete logic
        return redirect()->route('admin.settings.index')->with('success', 'Setting deleted successfully.');
    }
}
